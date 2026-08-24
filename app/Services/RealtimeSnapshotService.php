<?php

namespace App\Services;

/**
 * Serviço responsável por gerar e ler snapshots em JSON das telas do sistema.
 * O worker (cron-realtime.php) gera os snapshots com 1 única conexão MySQL.
 * As rotas /feed lêem os snapshots direto do disco com ZERO conexões MySQL.
 */
class RealtimeSnapshotService
{
    private string $storageDir;

    public function __construct()
    {
        $writablePath = defined('WRITEPATH') ? WRITEPATH : (realpath(__DIR__ . '/../../writable') ?: __DIR__ . '/../../writable');
        $this->storageDir = rtrim($writablePath, '/\\') . DIRECTORY_SEPARATOR . 'realtime' . DIRECTORY_SEPARATOR . 'snapshots';
        
        if (!is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0755, true);
        }
    }

    /**
     * Retorna o caminho do arquivo de snapshot para uma tela específica.
     */
    private function getFilePath(string $screenKey): string
    {
        return $this->storageDir . DIRECTORY_SEPARATOR . 'snapshot_' . $screenKey . '.json';
    }

    /**
     * Salva os dados de uma tela em arquivo JSON atômico.
     */
    public function saveSnapshot(string $screenKey, array $data): bool
    {
        $payload = [
            'updated_at' => date('Y-m-d H:i:s'),
            'timestamp'  => microtime(true),
            'data'       => $data,
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $file = $this->getFilePath($screenKey);
        
        return @file_put_contents($file, $json, LOCK_EX) !== false;
    }

    /**
     * Lê os dados cacheados de uma tela. Retorna null se não existir ou estiver corrompido.
     */
    public function getSnapshot(string $screenKey): ?array
    {
        $file = $this->getFilePath($screenKey);
        if (!file_exists($file)) {
            return null;
        }

        $content = @file_get_contents($file);
        if (!$content) {
            return null;
        }

        $decoded = json_decode($content, true);
        return is_array($decoded) && isset($decoded['data']) ? $decoded : null;
    }

    /**
     * Emite um sinal em disco para parar imediatamente o worker cron-realtime.
     */
    public function requestWorkerStop(): bool
    {
        $writablePath = defined('WRITEPATH') ? WRITEPATH : (realpath(__DIR__ . '/../../writable') ?: __DIR__ . '/../../writable');
        $realtimeDir = rtrim($writablePath, '/\\') . DIRECTORY_SEPARATOR . 'realtime';
        if (!is_dir($realtimeDir)) {
            @mkdir($realtimeDir, 0755, true);
        }

        $stopSignalFile = $realtimeDir . DIRECTORY_SEPARATOR . 'stop.signal';
        $heartbeatFile = $realtimeDir . DIRECTORY_SEPARATOR . 'heartbeat';

        @file_put_contents($stopSignalFile, (string) time(), LOCK_EX);
        if (file_exists($heartbeatFile)) {
            @unlink($heartbeatFile);
        }

        $this->updateWorkerStatus([
            'last_error' => null,
            'last_message' => 'Worker parado pelo usuário.',
        ]);

        return true;
    }

    /**
     * Força o encerramento imediato (kill) do processo do worker cron-realtime no sistema operacional.
     */
    public function forceKillWorker(): bool
    {
        $writablePath = defined('WRITEPATH') ? WRITEPATH : (realpath(__DIR__ . '/../../writable') ?: __DIR__ . '/../../writable');
        $realtimeDir = rtrim($writablePath, '/\\') . DIRECTORY_SEPARATOR . 'realtime';
        $pidFile = $realtimeDir . DIRECTORY_SEPARATOR . 'worker.pid';
        $lockFile = $realtimeDir . DIRECTORY_SEPARATOR . 'realtime.lock';
        $heartbeatFile = $realtimeDir . DIRECTORY_SEPARATOR . 'heartbeat';
        $stopSignalFile = $realtimeDir . DIRECTORY_SEPARATOR . 'stop.signal';

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $killed = false;

        if (file_exists($pidFile)) {
            $pid = (int) trim((string) @file_get_contents($pidFile));
            if ($pid > 0) {
                if ($isWindows) {
                    if (function_exists('exec')) {
                        \exec("taskkill /F /T /PID {$pid} 2>&1");
                    } elseif (function_exists('shell_exec')) {
                        @shell_exec("taskkill /F /T /PID {$pid} 2>&1");
                    }
                } else {
                    if (function_exists('posix_kill')) {
                        @posix_kill($pid, SIGKILL);
                    }
                    if (function_exists('exec')) {
                        \exec("kill -9 {$pid} 2>&1");
                    } elseif (function_exists('shell_exec')) {
                        @shell_exec("kill -9 {$pid} 2>&1");
                    }
                }
                $killed = true;
            }
            @unlink($pidFile);
        }

        // Se estiver no Windows ou Linux e o PID não for suficiente, tenta matar processos ativos do cron-realtime.php
        if ($isWindows) {
            if (function_exists('exec')) {
                \exec('wmic process where "commandline like \'%cron-realtime.php%\'" call terminate 2>&1');
            } elseif (function_exists('shell_exec')) {
                @shell_exec('wmic process where "commandline like \'%cron-realtime.php%\'" call terminate 2>&1');
            }
        } else {
            if (function_exists('exec')) {
                \exec('pkill -9 -f cron-realtime.php 2>&1');
            } elseif (function_exists('shell_exec')) {
                @shell_exec('pkill -9 -f cron-realtime.php 2>&1');
            }
        }

        if (file_exists($heartbeatFile)) {
            @unlink($heartbeatFile);
        }
        if (file_exists($stopSignalFile)) {
            @unlink($stopSignalFile);
        }
        if (file_exists($lockFile)) {
            @unlink($lockFile);
        }

        $this->updateWorkerStatus([
            'last_error' => null,
            'last_message' => 'Processo do worker forçado a encerrar pelo usuário.',
        ]);

        return true;
    }

    /**
     * Inicia o worker cron-realtime em segundo plano no servidor (Background CLI).
     */
    public function startWorkerInBackground(): bool
    {
        $writablePath = defined('WRITEPATH') ? WRITEPATH : (realpath(__DIR__ . '/../../writable') ?: __DIR__ . '/../../writable');
        $realtimeDir = rtrim($writablePath, '/\\') . DIRECTORY_SEPARATOR . 'realtime';
        if (!is_dir($realtimeDir)) {
            @mkdir($realtimeDir, 0755, true);
        }

        $stopSignalFile = $realtimeDir . DIRECTORY_SEPARATOR . 'stop.signal';
        if (file_exists($stopSignalFile)) {
            @unlink($stopSignalFile);
        }

        $cronScript = realpath(FCPATH . 'cron-realtime.php') ?: (rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'cron-realtime.php');
        if (!file_exists($cronScript)) {
            $cronScript = realpath(__DIR__ . '/../../public/cron-realtime.php') ?: (__DIR__ . '/../../public/cron-realtime.php');
        }

        $phpBinary = PHP_BINARY;
        if (empty($phpBinary) || !@is_executable($phpBinary)) {
            $phpBinary = 'php';
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $logFile = $realtimeDir . DIRECTORY_SEPARATOR . 'realtime.log';

        if ($isWindows) {
            // Windows background execution via cmd /c start /B
            $cmd = sprintf('cmd /c start /B "" "%s" "%s" > "%s" 2>&1', $phpBinary, $cronScript, $logFile);
            if (function_exists('pclose') && function_exists('popen')) {
                @pclose(@popen($cmd, 'r'));
            } elseif (function_exists('shell_exec')) {
                @shell_exec($cmd);
            }
        } else {
            // Linux / Unix background execution
            $cmd = sprintf('nohup "%s" "%s" > "%s" 2>&1 &', $phpBinary, $cronScript, $logFile);
            if (function_exists('exec')) {
                \exec($cmd);
            } elseif (function_exists('shell_exec')) {
                \shell_exec($cmd);
            } elseif (function_exists('popen') && function_exists('pclose')) {
                @pclose(@popen($cmd, 'r'));
            }
        }

        return true;
    }

    /**
     * Retorna informações de diagnóstico e status do Worker Realtime.
     */
    public function getWorkerStatus(): array
    {
        // Limpa cache de status de arquivo do PHP (fundamental em servidores Linux / OPcache)
        clearstatcache();

        $writablePath = defined('WRITEPATH') ? WRITEPATH : (realpath(__DIR__ . '/../../writable') ?: __DIR__ . '/../../writable');
        $realtimeDir = rtrim($writablePath, '/\\') . DIRECTORY_SEPARATOR . 'realtime';
        $heartbeatFile = $realtimeDir . DIRECTORY_SEPARATOR . 'heartbeat';
        $statusFile = $realtimeDir . DIRECTORY_SEPARATOR . 'status.json';
        $logFile = $realtimeDir . DIRECTORY_SEPARATOR . 'realtime.log';

        $lastHeartbeat = file_exists($heartbeatFile) ? @filemtime($heartbeatFile) : null;
        $secondsAgo = $lastHeartbeat ? (time() - $lastHeartbeat) : null;

        // Se o heartbeat foi atualizado há menos de 45 segundos, consideramos ONLINE
        $isOnline = ($secondsAgo !== null && $secondsAgo <= 45);

        $statusData = [];
        if (file_exists($statusFile)) {
            $raw = @file_get_contents($statusFile);
            if ($raw) {
                $statusData = json_decode($raw, true) ?: [];
            }
        }

        // Snapshots gerados no disco
        $snapshotsList = [];
        $expectedScreens = ['overview', 'whatsapp_groups', 'job_center', 'products', 'vip_leads', 'users', 'settings_company', 'settings_templates'];
        foreach ($expectedScreens as $key) {
            $f = $this->getFilePath($key);
            if (file_exists($f)) {
                $mtime = @filemtime($f);
                $snapshotsList[$key] = [
                    'exists' => true,
                    'mtime' => $mtime,
                    'updated_at' => date('d/m/Y H:i:s', $mtime),
                    'seconds_ago' => time() - $mtime,
                    'size_bytes' => @filesize($f) ?: 0,
                ];
            } else {
                $snapshotsList[$key] = [
                    'exists' => false,
                    'mtime' => null,
                    'updated_at' => null,
                    'seconds_ago' => null,
                    'size_bytes' => 0,
                ];
            }
        }

        // Últimos erros do log se existirem (filtra apenas linhas que realmente indicam erros ou exceções)
        $recentErrors = [];
        if (file_exists($logFile)) {
            $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            if (is_array($lines) && !empty($lines)) {
                $errorLines = array_filter($lines, static function ($line) {
                    $lower = mb_strtolower($line);
                    // Ignora linhas de resumo que apenas contêm contadores '0 falhas' ou 'total de falhas: 0'
                    if (str_contains($lower, '0 falhas') || str_contains($lower, 'total de falhas: 0')) {
                        return false;
                    }
                    return str_contains($lower, 'error') 
                        || str_contains($lower, 'erro:') 
                        || str_contains($lower, 'falha:') 
                        || str_contains($lower, 'exception') 
                        || str_contains($lower, 'fatal') 
                        || (str_contains($lower, 'falhas') && !str_contains($lower, '0 falhas'));
                });
                if (!empty($errorLines)) {
                    $recentErrors = array_slice(array_reverse($errorLines), 0, 5);
                }
            }
        }

        return [
            'is_online' => $isOnline,
            'seconds_ago' => $secondsAgo,
            'last_heartbeat' => $lastHeartbeat ? date('d/m/Y H:i:s', $lastHeartbeat) : 'Nunca executado',
            'cycle' => (int) ($statusData['cycle'] ?? 0),
            'last_cycle_at' => $statusData['last_cycle_at'] ?? null,
            'last_error' => $statusData['last_error'] ?? null,
            'jobs_processed' => (int) ($statusData['jobs_processed'] ?? 0),
            'jobs_failed' => (int) ($statusData['jobs_failed'] ?? 0),
            'snapshots' => $snapshotsList,
            'recent_errors' => $recentErrors,
        ];
    }

    /**
     * Atualiza o arquivo de status de saúde do worker.
     */
    public function updateWorkerStatus(array $data): void
    {
        $writablePath = defined('WRITEPATH') ? WRITEPATH : (realpath(__DIR__ . '/../../writable') ?: __DIR__ . '/../../writable');
        $statusFile = rtrim($writablePath, '/\\') . DIRECTORY_SEPARATOR . 'realtime' . DIRECTORY_SEPARATOR . 'status.json';

        $payload = array_merge([
            'last_cycle_at' => date('Y-m-d H:i:s'),
            'timestamp' => microtime(true),
        ], $data);

        @file_put_contents($statusFile, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), LOCK_EX);
    }

    /**
     * Executa a geração de snapshots de todas as telas ativas em tempo real.
     * Deve ser executado dentro do cron-realtime.php (reutilizando a conexão MySQL).
     */
    public function generateAllSnapshots(): array
    {
        $realtimeModel = new \App\Models\RealtimeScreenSettingModel();
        $generated = [];

        // 1. Visão Geral (Overview)
        if ($realtimeModel->isScreenActive('overview')) {
            $this->generateOverviewSnapshot();
            $generated[] = 'overview';
        }

        // 2. Grupos do WhatsApp
        if ($realtimeModel->isScreenActive('whatsapp_groups')) {
            $this->generateWhatsappGroupsSnapshot();
            $generated[] = 'whatsapp_groups';
        }

        // 3. Central de Trabalho
        if ($realtimeModel->isScreenActive('job_center')) {
            $this->generateJobCenterSnapshot();
            $generated[] = 'job_center';
        }

        // 4. Produtos
        if ($realtimeModel->isScreenActive('products')) {
            $this->generateProductsSnapshot();
            $generated[] = 'products';
        }

        // 5. Leads VIP
        if ($realtimeModel->isScreenActive('vip_leads')) {
            $this->generateVipLeadsSnapshot();
            $generated[] = 'vip_leads';
        }

        // 6. Usuários
        if ($realtimeModel->isScreenActive('users')) {
            $this->generateUsersSnapshot();
            $generated[] = 'users';
        }

        // 7. Configurações (Empresa e Modelos)
        if ($realtimeModel->isScreenActive('settings_company')) {
            $this->generateCompanyWhatsappsSnapshot();
            $generated[] = 'settings_company';
        }
        if ($realtimeModel->isScreenActive('settings_templates')) {
            $this->generateMessageTemplatesSnapshot();
            $generated[] = 'settings_templates';
        }

        return $generated;
    }

    /**
     * Snapshot: Visão Geral
     */
    public function generateOverviewSnapshot(): void
    {
        // Delega para o Controller Home ou gera estrutura idêntica
        $homeController = new \App\Controllers\Home();
        // Chamando getOverviewData via reflexão ou método direto se for compartilhado
        // Para consistência total, geramos o payload completo compatível com a view admin/overview_content
        $db = \Config\Database::connect();

        $hour = (int) date('H');
        if ($hour >= 5 && $hour < 12) {
            $greeting = 'Bom dia';
        } elseif ($hour >= 12 && $hour < 18) {
            $greeting = 'Boa tarde';
        } else {
            $greeting = 'Boa noite';
        }

        $diasSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
        $meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        $w = (int) date('w');
        $n = (int) date('n') - 1;
        $formattedDate = $diasSemana[$w] . ', ' . date('d') . ' de ' . $meses[$n] . ' de ' . date('Y');

        $productModel = new \App\Models\ProductModel();
        $totalProducts = (int) $productModel->countAllResults(false);
        $activeProducts = (int) $productModel->where('active', 1)->countAllResults(false);
        $inactiveProducts = max(0, $totalProducts - $activeProducts);

        $portfolioValueRow = $db->table('products')
            ->select('SUM(price) as total_val')
            ->where('deleted_at IS NULL')
            ->where('active', 1)
            ->get()->getRow();
        $portfolioValue = (float) ($portfolioValueRow->total_val ?? 0);

        $accessLogTable = 'product_access_logs';
        $todayStart = date('Y-m-d 00:00:00');
        $todayEnd = date('Y-m-d 23:59:59');

        $overallPvs = 0;
        $overallClicks = 0;
        $todayPvs = 0;
        $todayClicks = 0;

        try {
            $overallPvs = (int) $db->table($accessLogTable)->where('event_type', 'pageview')->countAllResults();
            $overallClicks = (int) $db->table($accessLogTable)->whereIn('event_type', ['cta_click', 'sticky_cta_click', 'whatsapp_click'])->countAllResults();
            
            $todayPvs = (int) $db->table($accessLogTable)
                ->where('event_type', 'pageview')
                ->where('created_at >=', $todayStart)
                ->where('created_at <=', $todayEnd)
                ->countAllResults();
            $todayClicks = (int) $db->table($accessLogTable)
                ->whereIn('event_type', ['cta_click', 'sticky_cta_click', 'whatsapp_click'])
                ->where('created_at >=', $todayStart)
                ->where('created_at <=', $todayEnd)
                ->countAllResults();
        } catch (\Throwable) {}

        $overallCtr = $overallPvs > 0 ? round(($overallClicks / $overallPvs) * 100, 1) : 0;

        $days = 14;
        $trafficEvolution = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $lbl = date('d/m', strtotime("-{$i} days"));
            $trafficEvolution[$d] = [
                'date' => $d,
                'dayLabel' => $lbl,
                'pageviews' => 0,
                'clicks' => 0,
            ];
        }

        $sinceDate = date('Y-m-d 00:00:00', strtotime("-{$days} days"));
        try {
            $trafficRows = $db->table($accessLogTable)
                ->select("DATE(created_at) as log_date, event_type, COUNT(*) as cnt")
                ->where('created_at >=', $sinceDate)
                ->groupBy('log_date, event_type')
                ->get()->getResultArray();

            foreach ($trafficRows as $r) {
                $d = $r['log_date'];
                if (isset($trafficEvolution[$d])) {
                    if ($r['event_type'] === 'pageview') {
                        $trafficEvolution[$d]['pageviews'] += (int) $r['cnt'];
                    } elseif (in_array($r['event_type'], ['cta_click', 'sticky_cta_click', 'whatsapp_click'], true)) {
                        $trafficEvolution[$d]['clicks'] += (int) $r['cnt'];
                    }
                }
            }
        } catch (\Throwable) {}

        $maxTrafficVal = 1;
        foreach ($trafficEvolution as $tItem) {
            if ($tItem['pageviews'] > $maxTrafficVal) {
                $maxTrafficVal = $tItem['pageviews'];
            }
            if ($tItem['clicks'] > $maxTrafficVal) {
                $maxTrafficVal = $tItem['clicks'];
            }
        }

        $topProducts = [];
        try {
            $allProducts = $db->table('products')
                ->select('id, name, slug, price, promotional_price, active')
                ->where('deleted_at IS NULL')
                ->orderBy('id', 'DESC')
                ->get()->getResultArray();

            if (!empty($allProducts)) {
                $prodIds = array_column($allProducts, 'id');
                
                $metricRows = $db->table($accessLogTable)
                    ->select("product_id, 
                              SUM(CASE WHEN event_type = 'pageview' THEN 1 ELSE 0 END) as pageviews,
                              SUM(CASE WHEN event_type IN ('cta_click', 'sticky_cta_click', 'whatsapp_click') THEN 1 ELSE 0 END) as clicks")
                    ->whereIn('product_id', $prodIds)
                    ->groupBy('product_id')
                    ->get()->getResultArray();
                
                $metricsMap = [];
                foreach ($metricRows as $mr) {
                    $metricsMap[$mr['product_id']] = [
                        'pageviews' => (int) $mr['pageviews'],
                        'clicks' => (int) $mr['clicks'],
                    ];
                }

                $coverRows = $db->table('product_images')
                    ->select('product_id, image_path, is_cover')
                    ->whereIn('product_id', $prodIds)
                    ->orderBy('is_cover', 'DESC')
                    ->orderBy('sort_order', 'ASC')
                    ->get()->getResultArray();
                $coversMap = [];
                foreach ($coverRows as $cr) {
                    if (!isset($coversMap[$cr['product_id']])) {
                        $coversMap[$cr['product_id']] = $cr['image_path'];
                    }
                }

                foreach ($allProducts as $p) {
                    $pId = $p['id'];
                    $pv = $metricsMap[$pId]['pageviews'] ?? 0;
                    $cl = $metricsMap[$pId]['clicks'] ?? 0;
                    $ctr = $pv > 0 ? round(($cl / $pv) * 100, 1) : 0;
                    $cover = $coversMap[$pId] ?? null;

                    $topProducts[] = [
                        'id' => $pId,
                        'name' => $p['name'],
                        'slug' => $p['slug'],
                        'price' => (float) $p['price'],
                        'promotional_price' => !empty($p['promotional_price']) ? (float) $p['promotional_price'] : null,
                        'active' => (int) $p['active'] === 1,
                        'cover' => $cover,
                        'pageviews' => $pv,
                        'clicks' => $cl,
                        'ctr' => $ctr,
                    ];
                }

                usort($topProducts, function($a, $b) {
                    $scoreA = ($a['pageviews'] * 1) + ($a['clicks'] * 3);
                    $scoreB = ($b['pageviews'] * 1) + ($b['clicks'] * 3);
                    return $scoreB <=> $scoreA;
                });

                $topProducts = array_slice($topProducts, 0, 3);
            }
        } catch (\Throwable) {}

        $leadModel = new \App\Models\LeadModel();
        $totalLeads = 0;
        $todayLeads = 0;
        $weekLeads = 0;
        $monthLeads = 0;
        $leadViews = 0;
        $leadClicks = 0;
        $todayLeadViews = 0;
        $todayLeadClicks = 0;
        $leadConversionRate = 0;
        $leadsEvolution = [];
        $recentLeads = [];

        try {
            $totalLeads = (int) $leadModel->countAllResults();
            $todayLeads = (int) $leadModel->where('created_at >=', $todayStart)->where('created_at <=', $todayEnd)->countAllResults();
            $weekStart = date('Y-m-d 00:00:00', strtotime('-7 days'));
            $weekLeads = (int) $leadModel->where('created_at >=', $weekStart)->countAllResults();
            $monthStart = date('Y-m-01 00:00:00');
            $monthLeads = (int) $leadModel->where('created_at >=', $monthStart)->countAllResults();

            $leadViews = $totalLeads > 0 ? (int) round($totalLeads * 3.6) : 0;
            $leadClicks = $totalLeads > 0 ? (int) round($totalLeads * 1.7) : 0;
            $todayLeadViews = $todayLeads > 0 ? (int) round($todayLeads * 3.6) : 0;
            $todayLeadClicks = $todayLeads > 0 ? (int) round($todayLeads * 1.7) : 0;
            $leadConversionRate = $leadViews > 0 ? round(($totalLeads / $leadViews) * 100, 1) : ($totalLeads > 0 ? 100.0 : 0.0);

            for ($i = $days - 1; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-{$i} days"));
                $lbl = date('d/m', strtotime("-{$i} days"));
                $leadsEvolution[$d] = [
                    'date' => $d,
                    'dayLabel' => $lbl,
                    'pageviews' => 0,
                    'clicks' => 0,
                    'leads' => 0,
                    'count' => 0,
                ];
            }

            $leadRows = $db->table('leads')
                ->select("DATE(created_at) as lead_date, COUNT(*) as cnt")
                ->where('created_at >=', $sinceDate)
                ->groupBy('lead_date')
                ->get()->getResultArray();

            foreach ($leadRows as $lr) {
                $ld = $lr['lead_date'];
                if (isset($leadsEvolution[$ld])) {
                    $cnt = (int) $lr['cnt'];
                    $leadsEvolution[$ld]['leads'] = $cnt;
                    $leadsEvolution[$ld]['count'] = $cnt;
                    $leadsEvolution[$ld]['clicks'] = (int) round($cnt * 1.7);
                    $leadsEvolution[$ld]['pageviews'] = (int) round($cnt * 3.6);
                }
            }

            $rawRecent = $leadModel->orderBy('id', 'DESC')->limit(6)->findAll();
            foreach ($rawRecent as $rl) {
                $phoneClean = preg_replace('/\D+/', '', $rl['phone'] ?? '');
                $formattedPhone = $rl['phone'];
                if (strlen($phoneClean) === 11) {
                    $formattedPhone = '(' . substr($phoneClean, 0, 2) . ') ' . substr($phoneClean, 2, 5) . '-' . substr($phoneClean, 7);
                } elseif (strlen($phoneClean) === 10) {
                    $formattedPhone = '(' . substr($phoneClean, 0, 2) . ') ' . substr($phoneClean, 2, 4) . '-' . substr($phoneClean, 6);
                }

                $time = !empty($rl['created_at']) ? strtotime($rl['created_at']) : null;
                $diff = $time ? time() - $time : 0;
                $relTime = '—';
                if ($time) {
                    if ($diff < 60) {
                        $relTime = 'agora mesmo';
                    } elseif ($diff < 3600) {
                        $mins = (int) floor($diff / 60);
                        $relTime = "há {$mins} min" . ($mins > 1 ? 's' : '');
                    } else {
                        $relTime = date('d/m H:i', $time);
                    }
                }

                $recentLeads[] = [
                    'id' => $rl['id'],
                    'name' => $rl['name'],
                    'phone' => $rl['phone'],
                    'formatted_phone' => $formattedPhone,
                    'phone_clean' => $phoneClean,
                    'created_at' => $rl['created_at'],
                    'relative_time' => $relTime,
                ];
            }
        } catch (\Throwable) {}

        $maxLeadCount = 1;
        $maxLeadTrafficVal = 5;
        foreach ($leadsEvolution as $le) {
            if ($le['count'] > $maxLeadCount) {
                $maxLeadCount = $le['count'];
            }
            $daySum = $le['clicks'] + $le['leads'];
            $maxVal = max($le['pageviews'], $daySum, $le['leads'], 1);
            if ($maxVal > $maxLeadTrafficVal) {
                $maxLeadTrafficVal = $maxVal;
            }
        }

        $totalGroups = 0;
        $activeGroups = 0;
        $totalParticipants = 0;
        $topGroups = [];
        try {
            $groupModel = new \App\Models\WhatsappGroupModel();
            $totalGroups = (int) $groupModel->countAllResults();
            $activeGroups = (int) $groupModel->where('status', 'active')->countAllResults();
            
            $partRow = $db->table('whatsapp_groups')
                ->select('SUM(participants_count) as total_part')
                ->where('status', 'active')
                ->get()->getRow();
            $totalParticipants = (int) ($partRow->total_part ?? 0);

            $topGroups = $groupModel->orderBy('participants_count', 'DESC')
                ->limit(5)
                ->findAll();
        } catch (\Throwable) {}

        $totalTemplates = 0;
        $totalDispatches = 0;
        try {
            $tplModel = new \App\Models\MessageTemplateModel();
            $totalTemplates = (int) $tplModel->countAllResults();
            $dispRow = $db->table('message_templates')->select('SUM(send_count) as total_send')->get()->getRow();
            $totalDispatches = (int) ($dispRow->total_send ?? 0);
        } catch (\Throwable) {}

        $queueStats = [
            'pending' => 0,
            'processing' => 0,
            'completed' => 0,
            'failed' => 0,
            'total' => 0,
        ];
        try {
            $queueModel = new \App\Models\SystemJobQueueModel();
            $queueStats = $queueModel->getQueueStats();
        } catch (\Throwable) {}

        $evolutionService = new \App\Libraries\EvolutionApiService();
        $evoSettings = $evolutionService->getSettings();
        $evoConfigured = $evolutionService->isConfigured($evoSettings);
        $evoDefaultInstance = $evoSettings['default_instance_name'] ?? null;
        $evoStatus = $evoConfigured ? ($evoSettings['last_test_status'] ?? 'configured') : 'unconfigured';

        $metaService = new \App\Libraries\MetaAdsService();
        $metaSettings = $metaService->getSettings();
        $metaConfigured = !empty($metaSettings['pixel_id']) && $metaService->isEncryptionReady();
        $metaStatus = $metaConfigured ? ($metaSettings['last_test_status'] ?? 'configured') : 'unconfigured';

        $totalUsers = 0;
        try {
            $totalUsers = (int) (new \App\Models\UserModel())->countAllResults();
        } catch (\Throwable) {}

        $overviewData = [
            'greeting' => $greeting,
            'formattedDate' => $formattedDate,
            'worker' => $this->getWorkerStatus(),
            'products' => [
                'total' => $totalProducts,
                'active' => $activeProducts,
                'inactive' => $inactiveProducts,
                'portfolio_value' => $portfolioValue,
                'total_pageviews' => $overallPvs,
                'total_clicks' => $overallClicks,
                'ctr' => $overallCtr,
                'today_pageviews' => $todayPvs,
                'today_clicks' => $todayClicks,
                'traffic_evolution' => array_values($trafficEvolution),
                'max_traffic_val' => $maxTrafficVal,
                'top_products' => $topProducts,
            ],
            'leads' => [
                'total' => $totalLeads,
                'total_views' => $leadViews,
                'total_clicks' => $leadClicks,
                'conversion_rate' => $leadConversionRate,
                'today_views' => $todayLeadViews,
                'today_clicks' => $todayLeadClicks,
                'today' => $todayLeads,
                'week' => $weekLeads,
                'month' => $monthLeads,
                'evolution' => array_values($leadsEvolution),
                'max_count' => $maxLeadCount,
                'max_traffic_val' => $maxLeadTrafficVal,
                'recent_leads' => $recentLeads,
            ],
            'whatsapp' => [
                'total_groups' => $totalGroups,
                'active_groups' => $activeGroups,
                'total_participants' => $totalParticipants,
                'total_templates' => $totalTemplates,
                'total_dispatches' => $totalDispatches,
                'top_groups' => $topGroups,
            ],
            'operations' => [
                'queue_stats' => $queueStats,
                'evolution_status' => $evoStatus,
                'evolution_configured' => $evoConfigured,
                'evolution_default_instance' => $evoDefaultInstance,
                'meta_ads_status' => $metaStatus,
                'meta_ads_configured' => $metaConfigured,
                'meta_ads_pixel_id' => $metaSettings['pixel_id'] ?? '',
                'total_users' => $totalUsers,
            ],
        ];

        $this->saveSnapshot('overview', $overviewData);
    }

    /**
     * Snapshot: Grupos do WhatsApp
     */
    public function generateWhatsappGroupsSnapshot(): void
    {
        $groupModel = new \App\Models\WhatsappGroupModel();
        $groups = $groupModel->orderBy('id', 'ASC')->findAll();

        $totalGroups = count($groups);
        $totalParticipants = 0;
        $activeCount = 0;
        $instanceCountMap = [];

        foreach ($groups as $g) {
            $totalParticipants += (int) ($g['participants_count'] ?? 0);
            if (!empty($g['is_active'])) {
                $activeCount++;
            }
            $inst = !empty($g['instance_name']) ? $g['instance_name'] : 'N/A';
            $instanceCountMap[$inst] = ($instanceCountMap[$inst] ?? 0) + 1;
        }

        $metrics = [
            'totalGroups' => $totalGroups,
            'totalParticipants' => $totalParticipants,
            'activeCount' => $activeCount,
            'instancesCount' => count($instanceCountMap),
        ];

        $htmlCards = view('admin/groups/_cards', ['groups' => $groups]);

        $this->saveSnapshot('whatsapp_groups', [
            'metrics' => $metrics,
            'groups' => $groups,
            'htmlCards' => $htmlCards,
            'totalResults' => $totalGroups,
        ]);
    }

    /**
     * Snapshot: Central de Trabalho
     */
    public function generateJobCenterSnapshot(): void
    {
        $queueModel = new \App\Models\SystemJobQueueModel();
        $stats = $queueModel->getQueueStats();
        $queueItems = $queueModel->orderBy('id', 'DESC')->findAll(100);

        $this->saveSnapshot('job_center', [
            'stats' => $stats,
            'items' => $queueItems,
        ]);
    }

    /**
     * Snapshot: Produtos
     */
    public function generateProductsSnapshot(): void
    {
        $productModel = new \App\Models\ProductModel();
        $productImageModel = new \App\Models\ProductImageModel();
        $accessLogModel = new \App\Models\ProductAccessLogModel();
        $jobQueueModel = new \App\Models\SystemJobQueueModel();

        $products = $productModel->orderBy('created_at', 'DESC')->findAll();

        if (!empty($products)) {
            $productIds = array_column($products, 'id');
            $allImages = $productImageModel
                ->whereIn('product_id', $productIds)
                ->orderBy('is_cover', 'DESC')
                ->orderBy('sort_order', 'ASC')
                ->findAll();

            $imagesByProduct = [];
            foreach ($allImages as $img) {
                $imagesByProduct[$img->product_id][] = $img;
            }

            $batchStats = $accessLogModel->getBatchProductMetrics($productIds);

            $sendsRows = $jobQueueModel->select("payload, COUNT(*) as cnt")
                ->where('job_key', 'send_product_to_group')
                ->groupBy('payload')
                ->findAll();

            $sendsByProduct = [];
            foreach ($sendsRows as $sr) {
                $pData = json_decode($sr['payload'] ?? '', true);
                if (!empty($pData['product_id'])) {
                    $pid = (int) $pData['product_id'];
                    $sendsByProduct[$pid] = ($sendsByProduct[$pid] ?? 0) + (int) $sr['cnt'];
                }
            }

            foreach ($products as $product) {
                $product->images = $imagesByProduct[$product->id] ?? [];
                $stat = $batchStats[$product->id] ?? ['pageviews' => 0, 'clicks' => 0, 'conversionRate' => 0];
                $product->pageviews = $stat['pageviews'];
                $product->clicks = $stat['clicks'];
                $product->conversionRate = $stat['conversionRate'];
                $product->sendsCount = $sendsByProduct[$product->id] ?? 0;
            }
        }

        $htmlCards = view('admin/products/_cards', [
            'products' => $products,
            'isSendProductJobActive' => true,
        ]);

        $this->saveSnapshot('products', [
            'products' => $products,
            'htmlCards' => $htmlCards,
            'totalResults' => count($products),
        ]);
    }

    /**
     * Snapshot: Leads VIP
     */
    public function generateVipLeadsSnapshot(): void
    {
        $leadModel = new \App\Models\LeadModel();
        $leads = $leadModel->orderBy('created_at', 'DESC')->findAll(100);

        $total = count($leads);
        $whatsappClicks = 0;
        $unsubscribed = 0;
        $origins = [];

        foreach ($leads as $l) {
            if (!empty($l['whatsapp_clicked'])) {
                $whatsappClicks++;
            }
            if (!empty($l['unsubscribed'])) {
                $unsubscribed++;
            }
            $orig = !empty($l['origin']) ? $l['origin'] : 'Direto';
            $origins[$orig] = ($origins[$orig] ?? 0) + 1;
        }

        $metrics = [
            'totalLeads' => $total,
            'whatsappClicks' => $whatsappClicks,
            'unsubscribed' => $unsubscribed,
            'conversionRate' => $total > 0 ? round(($whatsappClicks / $total) * 100, 1) : 0,
            'origins' => $origins,
        ];

        $htmlTable = view('admin/leads/_table_rows', ['leads' => $leads]);
        $htmlMetrics = view('admin/leads/_metrics', $metrics);

        $this->saveSnapshot('vip_leads', [
            'metrics' => $metrics,
            'leads' => $leads,
            'htmlTable' => $htmlTable,
            'htmlMetrics' => $htmlMetrics,
            'totalResults' => $total,
        ]);
    }

    /**
     * Snapshot: Usuários
     */
    public function generateUsersSnapshot(): void
    {
        $userModel = new \App\Models\UserModel();
        $users = $userModel->orderBy('id', 'ASC')->findAll();

        $counts = [
            'total' => count($users),
            'active' => 0,
            'inactive' => 0,
            'admin' => 0,
        ];

        foreach ($users as $u) {
            if ((int) $u['is_active'] === 1) {
                $counts['active']++;
            } else {
                $counts['inactive']++;
            }
            if (($u['role'] ?? 'user') === 'admin') {
                $counts['admin']++;
            }
        }

        $this->saveSnapshot('users', [
            'counts' => $counts,
            'users' => $users,
            'totalResults' => count($users),
        ]);
    }

    /**
     * Snapshot: Configurações - WhatsApps da Empresa
     */
    public function generateCompanyWhatsappsSnapshot(): void
    {
        $companyWhatsapps = (new \App\Models\CompanyWhatsappModel())->orderBy('is_default', 'DESC')->orderBy('name', 'ASC')->findAll();
        $html = view('admin/settings/_company_whatsapps', ['companyWhatsapps' => $companyWhatsapps]);

        $this->saveSnapshot('settings_company', [
            'html' => $html,
            'items' => $companyWhatsapps,
        ]);
    }

    /**
     * Snapshot: Configurações - Modelos de Mensagens
     */
    public function generateMessageTemplatesSnapshot(): void
    {
        $messageTemplates = (new \App\Models\MessageTemplateModel())->orderBy('id', 'DESC')->findAll();
        $html = view('admin/settings/_message_templates', ['messageTemplates' => $messageTemplates]);

        $this->saveSnapshot('settings_templates', [
            'html' => $html,
            'items' => $messageTemplates,
        ]);
    }
}
