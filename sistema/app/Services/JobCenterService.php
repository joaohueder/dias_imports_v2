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
        $cumulativeTime = $now;
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

            if ($enqueuedCount > 0) {
                $randomGap = random_int($minDelay, $maxDelay);
                $cumulativeTime += $randomGap;
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
                'scheduled_at'   => date('Y-m-d H:i:s', $cumulativeTime),
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
     * Executa o processador da fila (chamado via Spark CLI / Cron Job)
     */
    public function processPendingQueue(int $limit = 50, ?callable $logger = null): array
    {
        $now = date('Y-m-d H:i:s');
        $processed = 0;
        $failed = 0;

        $log = function (string $msg) use ($logger) {
            if ($logger) {
                $logger($msg);
            }
        };

        $jobsConfig = [];
        foreach ($this->jobModel->findAll() as $j) {
            $jobsConfig[$j['job_key']] = $j;
        }

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

        $log("Iniciando processamento de " . count($items) . " tarefas...");

        foreach ($items as $item) {
            $jobConfig = $jobsConfig[$item['job_key']] ?? null;
            if ($jobConfig && empty($jobConfig['is_active'])) {
                $log("Job {$item['job_key']} está inativo. Pulando item #{$item['id']}.");
                continue;
            }

            // Marcar como processando
            $this->queueModel->update($item['id'], [
                'status'     => 'processing',
                'started_at' => date('Y-m-d H:i:s'),
                'attempts'   => ((int) $item['attempts']) + 1,
            ]);

            $log("Processando tarefa #{$item['id']} ({$item['job_key']} - ref: {$item['item_reference']})...");

            // Intervalo randomizado configurado
            $minDelay = (int) ($jobConfig['min_delay_seconds'] ?? 5);
            $maxDelay = (int) ($jobConfig['max_delay_seconds'] ?? 20);
            if ($maxDelay < $minDelay) {
                $maxDelay = $minDelay;
            }

            if ($minDelay > 0) {
                $delay = random_int($minDelay, $maxDelay);
                $log("Aguardando intervalo seguro randomizado de {$delay}s antes da execução...");
                sleep($delay);
            }

            $success = false;
            $errorMsg = null;

            try {
                if ($item['job_key'] === 'sync_whatsapp_groups') {
                    $res = $this->handleSyncWhatsappGroup($item);
                    $success = $res['success'];
                    $errorMsg = $res['message'] ?? null;
                } else {
                    $errorMsg = "Manipulador não implementado para o trabalho {$item['job_key']}";
                }
            } catch (\Throwable $e) {
                $success = false;
                $errorMsg = $e->getMessage();
            }

            if ($success) {
                $this->queueModel->update($item['id'], [
                    'status'        => 'completed',
                    'completed_at'  => date('Y-m-d H:i:s'),
                    'error_message' => null,
                ]);
                $processed++;
                $log("Tarefa #{$item['id']} concluída com sucesso.");
            } else {
                $this->queueModel->update($item['id'], [
                    'status'        => 'failed',
                    'completed_at'  => date('Y-m-d H:i:s'),
                    'error_message' => $errorMsg ?? 'Erro desconhecido durante execução',
                ]);
                $failed++;
                $log("Tarefa #{$item['id']} FALHOU: " . ($errorMsg ?? 'Erro'));
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
        $instanceName = trim((string) ($group['instance_name'] ?? $settings['default_instance_name'] ?? ''));

        if ($instanceName === '') {
            $instances = $evolutionService->fetchInstances();
            foreach ($instances as $inst) {
                if (!empty($inst['connected'])) {
                    $instanceName = $inst['name'];
                    break;
                }
            }
            if ($instanceName === '' && !empty($instances[0]['name'])) {
                $instanceName = $instances[0]['name'];
            }
        }

        if ($instanceName === '') {
            return ['success' => false, 'message' => 'Nenhuma instância da Evolution API conectada ou informada.'];
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

        $updateData = [
            'last_synced_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($found['subject'])) {
            $updateData['name'] = $found['subject'];
        }
        if (isset($found['desc'])) {
            $updateData['description'] = $found['desc'];
        }
        if (isset($found['size'])) {
            $updateData['participants_count'] = (int) $found['size'];
        } elseif (isset($found['participants']) && is_array($found['participants'])) {
            $updateData['participants_count'] = count($found['participants']);
        }

        if (!empty($found['pictureUrl'])) {
            $updateData['avatar_url'] = $found['pictureUrl'];
        } else {
            $pic = $evolutionService->findGroupPicture($instanceName, $groupJid);
            if (!empty($pic)) {
                $updateData['avatar_url'] = $pic;
            }
        }

        $groupModel->update($group['id'], $updateData);

        return [
            'success' => true,
            'message' => "Grupo {$groupJid} atualizado com sucesso.",
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
