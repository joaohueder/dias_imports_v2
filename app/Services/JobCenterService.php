<?php

namespace App\Services;

use App\Models\SystemJobModel;
use App\Models\SystemJobQueueModel;
use App\Models\WhatsappGroupModel;
use App\Libraries\EvolutionApiService;

class JobCenterService
{
    protected SystemJobModel $jobModel;
    protected SystemJobQueueModel $queueModel;

    public function __construct()
    {
        $this->jobModel = new SystemJobModel();
        $this->queueModel = new SystemJobQueueModel();
    }

    /**
     * Enfileira tarefas para atualizar todos os grupos do WhatsApp cadastrados
     */
    public function enqueueWhatsappGroupsSync(): array
    {
        $job = $this->jobModel->getByKey('sync_whatsapp_groups');
        if (!$job || empty($job['is_active'])) {
            return [
                'success' => false,
                'message' => 'O trabalho de atualização de grupos está desativado nas configurações da Central de Trabalho.',
            ];
        }

        $groupModel = new WhatsappGroupModel();
        $groups = $groupModel->orderBy('id', 'ASC')->findAll();

        if (empty($groups)) {
            return [
                'success' => false,
                'message' => 'Nenhum grupo cadastrado no sistema para atualizar.',
            ];
        }

        $minDelay = max(1, (int) ($job['min_delay_seconds'] ?? 5));
        $maxDelay = max($minDelay, (int) ($job['max_delay_seconds'] ?? 20));

        $now = time();
        $enqueuedCount = 0;
        $existingCount = 0;

        foreach ($groups as $group) {
            $groupName = !empty($group['name']) ? $group['name'] : $group['group_jid'];

            // Verifica se já tem uma tarefa pendente ou em processamento para este grupo (pelo JID no payload ou nome)
            $existing = $this->queueModel
                ->where('job_key', 'sync_whatsapp_groups')
                ->groupStart()
                    ->where('item_reference', $groupName)
                    ->orWhere('item_reference', $group['group_jid'])
                    ->orLike('payload', '"group_jid":"' . $group['group_jid'] . '"')
                ->groupEnd()
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if ($existing) {
                $existingCount++;
                continue;
            }

            $this->queueModel->insert([
                'job_key'        => 'sync_whatsapp_groups',
                'item_reference' => $groupName,
                'payload'        => json_encode([
                    'group_id'      => $group['id'],
                    'group_jid'     => $group['group_jid'],
                    'instance_name' => $group['instance_name'] ?? '',
                    'name'          => $group['name'] ?? '',
                ], JSON_UNESCAPED_UNICODE),
                'status'         => 'pending',
                'scheduled_at'   => date('Y-m-d H:i:s', $now),
                'attempts'       => 0,
            ]);

            $enqueuedCount++;
        }

        if ($enqueuedCount === 0 && $existingCount > 0) {
            return [
                'success' => true,
                'message' => 'Todos os grupos já possuem agendamento pendente ou em processamento na Central de Trabalho.',
                'count'   => 0,
            ];
        }

        $message = "{$enqueuedCount} grupo(s) foram enfileirados na Central de Trabalho.";
        if ($existingCount > 0) {
            $message .= " ({$existingCount} já estavam na fila).";
        }

        return [
            'success' => true,
            'message' => $message,
            'count'   => $enqueuedCount,
        ];
    }

    /**
     * Executa o processador da fila (chamado via Spark CLI / Cron Job / Webcron)
     */
    public function processPendingQueue(int $limit = 50, ?callable $logger = null): array
    {
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);

        $log = function (string $msg) use ($logger) {
            if ($logger) {
                $logger($msg);
            }
        };

        // Verifica se já existe uma execução ativa em andamento (status = 'processing' atualizado recentemente)
        $runningThreshold = date('Y-m-d H:i:s', strtotime('-15 minutes'));
        $runningJob = $this->queueModel
            ->where('status', 'processing')
            ->where('started_at >=', $runningThreshold)
            ->first();

        if ($runningJob) {
            $log("Já existe um processamento em andamento (tarefa #{$runningJob['id']}). Ignorando nova chamada para evitar concorrência.");
            return [
                'processed' => 0,
                'failed'    => 0,
                'skipped'   => true,
                'message'   => 'Já existe uma execução em andamento.',
            ];
        }

        // Se houver tarefas travadas em processamento há mais de 15 minutos, resetar para pendente
        $stuckJobs = $this->queueModel
            ->where('status', 'processing')
            ->where('started_at <', $runningThreshold)
            ->findAll();

        foreach ($stuckJobs as $stuck) {
            $this->queueModel->update($stuck['id'], [
                'status' => 'pending',
                'error_message' => 'Processamento anterior expirou/interrompido. Retornado para a fila.',
            ]);
        }

        $now = date('Y-m-d H:i:s');
        $processed = 0;
        $failed = 0;

        $jobsConfig = [];
        foreach ($this->jobModel->findAll() as $j) {
            $jobsConfig[$j['job_key']] = $j;
        }

        // Busca apenas tarefas pendentes e vencidas (scheduled_at <= $now)
        $items = $this->queueModel
            ->where('status', 'pending')
            ->where('scheduled_at <=', $now)
            ->orderBy('scheduled_at', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll($limit);

        if (empty($items)) {
            $log("Nenhuma tarefa pendente agendada para o momento ({$now}).");
            return ['processed' => 0, 'failed' => 0];
        }

        $totalItems = count($items);
        $log("Selecionado(s) {$totalItems} registro(s) para execução.");

        // Pré-marcar todos os itens selecionados como "processing" para evitar concorrência
        $itemIds = array_column($items, 'id');
        $this->queueModel->whereIn('id', $itemIds)->set([
            'status'     => 'processing',
            'started_at' => date('Y-m-d H:i:s'),
        ])->update();

        $currentIndex = 0;
        foreach ($items as $item) {
            $currentIndex++;
            $jobConfig = $jobsConfig[$item['job_key']] ?? null;
            if ($jobConfig && empty($jobConfig['is_active'])) {
                $log("[{$currentIndex}/{$totalItems}] Job {$item['job_key']} está inativo. Pulando item #{$item['id']}.");
                // Retorna para pendente se o job estiver inativo
                $this->queueModel->update($item['id'], ['status' => 'pending']);
                continue;
            }

            $currentAttempts = ((int) ($item['attempts'] ?? 0)) + 1;

            // Atualizar apenas a tentativa, pois já foi marcado como processing
            $this->queueModel->update($item['id'], [
                'attempts' => $currentAttempts,
            ]);

            $jobName = $jobConfig['name'] ?? $item['job_key'];
            $log("[{$currentIndex}/{$totalItems}] Executando: {$jobName} - ref: {$item['item_reference']} (tentativa {$currentAttempts}/3)...");

            // Intervalo randomizado configurado
            $minDelay = (int) ($jobConfig['min_delay_seconds'] ?? 5);
            $maxDelay = (int) ($jobConfig['max_delay_seconds'] ?? 20);
            if ($maxDelay < $minDelay) {
                $maxDelay = $minDelay;
            }

            if ($minDelay > 0) {
                $delay = random_int($minDelay, $maxDelay);
                $log("[{$currentIndex}/{$totalItems}] Aguardando intervalo anti-bloqueio de {$delay}s...");
                sleep($delay);
            }

            $success = false;
            $resultMsg = null;

            try {
                if ($item['job_key'] === 'sync_whatsapp_groups') {
                    $res = $this->handleSyncWhatsappGroup($item);
                    $success = $res['success'];
                    $resultMsg = $res['message'] ?? null;
                } else {
                    $resultMsg = "Manipulador não implementado para o trabalho {$item['job_key']}";
                }
            } catch (\Throwable $e) {
                $success = false;
                $resultMsg = $e->getMessage();
            }

            if ($success) {
                $this->queueModel->update($item['id'], [
                    'status'        => 'completed',
                    'completed_at'  => date('Y-m-d H:i:s'),
                    'error_message' => $resultMsg,
                ]);
                $processed++;
                $log("[{$currentIndex}/{$totalItems}] Resultado: SUCESSO - " . ($resultMsg ?? 'OK'));
            } else {
                $errorDetail = $resultMsg ?? 'Erro desconhecido';
                // Se falhar e ainda tiver menos de 3 tentativas, volta para pendente com retry agendado
                if ($currentAttempts < 3) {
                    $retryDelay = random_int($minDelay, $maxDelay);
                    $nextSchedule = date('Y-m-d H:i:s', time() + max(30, $retryDelay));

                    $this->queueModel->update($item['id'], [
                        'status'        => 'pending',
                        'scheduled_at'  => $nextSchedule,
                        'error_message' => "Tentativa {$currentAttempts}/3 falhou: " . $errorDetail,
                    ]);
                    $log("[{$currentIndex}/{$totalItems}] Resultado: FALHA ({$currentAttempts}/3) - Motivo: {$errorDetail} -> Reagendado para {$nextSchedule}");
                } else {
                    $this->queueModel->update($item['id'], [
                        'status'        => 'failed',
                        'completed_at'  => date('Y-m-d H:i:s'),
                        'error_message' => "Falha definitiva após 3 tentativas: " . $errorDetail,
                    ]);
                    $failed++;
                    $log("[{$currentIndex}/{$totalItems}] Resultado: FALHA DEFINITIVA - Motivo: {$errorDetail}");
                }
            }
        }

        return [
            'processed' => $processed,
            'failed'    => $failed,
        ];
    }

    /**
     * Manipulador específico para sincronizar 1 grupo do WhatsApp
     */
    protected function handleSyncWhatsappGroup(array $item): array
    {
        $payload = json_encode($item['payload'] ?? '');
        $data = is_string($item['payload']) ? json_decode($item['payload'], true) : $item['payload'];
        $groupJid = $data['group_jid'] ?? $item['item_reference'];

        if (empty($groupJid)) {
            return ['success' => false, 'message' => 'JID do grupo não informado.'];
        }

        $groupModel = new WhatsappGroupModel();
        $group = $groupModel->where('group_jid', $groupJid)->first();

        if (!$group) {
            return ['success' => false, 'message' => "Grupo com JID {$groupJid} não encontrado no banco."];
        }

        $evolutionService = new EvolutionApiService();
        $settings = $evolutionService->getSettings();
        $defaultInstance = trim((string) ($settings['default_instance_name'] ?? ''));

        // Se o grupo tem instância gravada, verificamos se ela é válida; caso contrário usamos a padrão ou conectada
        $instanceName = trim((string) ($group['instance_name'] ?? ''));

        // Valida se a instância especificada ainda existe / está acessível na Evolution API
        $validInstance = false;
        try {
            $instances = $evolutionService->fetchInstances();
            $connectedInstance = '';
            foreach ($instances as $inst) {
                if ($instanceName !== '' && $inst['name'] === $instanceName) {
                    $validInstance = true;
                }
                if (!empty($inst['connected']) && $connectedInstance === '') {
                    $connectedInstance = $inst['name'];
                }
            }

            // Se a instância gravada no grupo não existe mais na Evolution API, faz fallback
            if (!$validInstance) {
                if ($defaultInstance !== '') {
                    // Verifica se a padrão existe
                    $defaultExists = false;
                    foreach ($instances as $inst) {
                        if ($inst['name'] === $defaultInstance) {
                            $defaultExists = true;
                            break;
                        }
                    }
                    $instanceName = $defaultExists ? $defaultInstance : $connectedInstance;
                } else {
                    $instanceName = $connectedInstance !== '' ? $connectedInstance : ($instances[0]['name'] ?? '');
                }

                // Atualiza o grupo no banco com a instância válida corrigida
                if ($instanceName !== '' && $instanceName !== ($group['instance_name'] ?? '')) {
                    $groupModel->update($group['id'], ['instance_name' => $instanceName]);
                }
            }
        } catch (\Throwable $e) {
            // Em caso de erro na checagem, tenta usar a padrão se houver
            if ($instanceName === '') {
                $instanceName = $defaultInstance;
            }
        }

        if ($instanceName === '') {
            return ['success' => false, 'message' => 'Nenhuma instância da Evolution API conectada ou encontrada.'];
        }

        $remoteGroups = $evolutionService->fetchAllGroups($instanceName, true);
        $found = null;

        foreach ($remoteGroups as $rg) {
            $jid = (string) ($rg['id'] ?? $rg['jid'] ?? '');
            if ($jid === $groupJid) {
                $found = $rg;
                break;
            }
        }

        if (!$found) {
            return [
                'success' => false,
                'message' => "Grupo {$groupJid} não encontrado na listagem da instância {$instanceName}.",
            ];
        }

        $changes = [];
        $updateData = [
            'last_synced_at' => date('Y-m-d H:i:s'),
        ];

        // Nome
        if (!empty($found['subject'])) {
            $newName = (string) $found['subject'];
            $oldName = (string) ($group['name'] ?? '');
            if ($oldName !== $newName) {
                $changes[] = "Nome: \"{$oldName}\" → \"{$newName}\"";
            }
            $updateData['name'] = $newName;
        }

        // Descrição
        if (isset($found['desc'])) {
            $newDesc = (string) $found['desc'];
            $oldDesc = (string) ($group['description'] ?? '');
            if ($oldDesc !== $newDesc) {
                $oldShort = mb_strlen($oldDesc) > 30 ? mb_substr($oldDesc, 0, 30) . '...' : ($oldDesc ?: '(vazio)');
                $newShort = mb_strlen($newDesc) > 30 ? mb_substr($newDesc, 0, 30) . '...' : ($newDesc ?: '(vazio)');
                $changes[] = "Descrição alterada: \"{$oldShort}\" → \"{$newShort}\"";
            }
            $updateData['description'] = $newDesc;
        }

        // Membros / Participantes
        $oldCount = (int) ($group['participants_count'] ?? 0);
        $newCount = $oldCount;
        if (isset($found['size'])) {
            $newCount = (int) $found['size'];
        } elseif (isset($found['participants']) && is_array($found['participants'])) {
            $newCount = count($found['participants']);
        }
        if ($oldCount !== $newCount) {
            $changes[] = "Membros: de {$oldCount} para {$newCount}";
        }
        $updateData['participants_count'] = $newCount;

        // Foto de Perfil
        $oldPic = (string) ($group['avatar_url'] ?? '');
        $newPic = '';
        if (!empty($found['pictureUrl'])) {
            $newPic = (string) $found['pictureUrl'];
            $updateData['avatar_url'] = $newPic;
        } else {
            $pic = $evolutionService->findGroupPicture($instanceName, $groupJid);
            if (!empty($pic)) {
                $newPic = (string) $pic;
                $updateData['avatar_url'] = $newPic;
            }
        }
        if ($newPic !== '' && $oldPic !== $newPic) {
            $changes[] = "Foto de perfil atualizada";
        }

        $groupModel->update($group['id'], $updateData);

        $details = empty($changes) ? "Nenhuma alteração detectada (dados já estavam sincronizados)." : implode(" | ", $changes);
        $reportMessage = "Grupo \"{$group['name']}\" ({$groupJid}) sincronizado com sucesso.\nDetalhes: {$details}";

        return [
            'success' => true,
            'message' => $reportMessage,
        ];
    }

    /**
     * Reprocessa tarefas com falha
     */
    public function retryFailedJobs(?string $jobKey = null): int
    {
        $builder = $this->queueModel->where('status', 'failed');
        if ($jobKey) {
            $builder->where('job_key', $jobKey);
        }

        $failedItems = $builder->findAll();
        $count = 0;
        $now = date('Y-m-d H:i:s');

        foreach ($failedItems as $item) {
            $this->queueModel->update($item['id'], [
                'status'        => 'pending',
                'scheduled_at'  => $now,
                'started_at'    => null,
                'completed_at'  => null,
                'error_message' => null,
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Limpa tarefas concluídas da fila
     */
    public function clearCompletedJobs(): int
    {
        $db = \Config\Database::connect();
        $builder = $db->table('system_job_queue');
        $builder->where('status', 'completed')->delete();
        return $db->affectedRows();
    }
}
