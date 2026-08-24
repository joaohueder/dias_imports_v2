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
     * Executa a geração de snapshots de todas as telas ativas em tempo real.
     * Deve ser executado dentro do cron-realtime.php (reutilizando a conexão MySQL).
     */
    public function generateAllSnapshots(): void
    {
        $realtimeModel = new \App\Models\RealtimeScreenSettingModel();

        // 1. Visão Geral (Overview)
        if ($realtimeModel->isScreenActive('overview')) {
            $this->generateOverviewSnapshot();
        }

        // 2. Grupos do WhatsApp
        if ($realtimeModel->isScreenActive('whatsapp_groups')) {
            $this->generateWhatsappGroupsSnapshot();
        }

        // 3. Central de Trabalho
        if ($realtimeModel->isScreenActive('job_center')) {
            $this->generateJobCenterSnapshot();
        }

        // 4. Produtos
        if ($realtimeModel->isScreenActive('products')) {
            $this->generateProductsSnapshot();
        }

        // 5. Leads VIP
        if ($realtimeModel->isScreenActive('vip_leads')) {
            $this->generateVipLeadsSnapshot();
        }

        // 6. Usuários
        if ($realtimeModel->isScreenActive('users')) {
            $this->generateUsersSnapshot();
        }

        // 7. Configurações (Empresa e Modelos)
        if ($realtimeModel->isScreenActive('settings_company')) {
            $this->generateCompanyWhatsappsSnapshot();
        }
        if ($realtimeModel->isScreenActive('settings_templates')) {
            $this->generateMessageTemplatesSnapshot();
        }
    }

    /**
     * Snapshot: Visão Geral
     */
    public function generateOverviewSnapshot(): void
    {
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

        $todayHourly = [];
        for ($h = 0; $h < 24; $h++) {
            $todayHourly[$h] = ['hour' => $h, 'pageviews' => 0, 'clicks' => 0];
        }
        try {
            $hourlyRows = $db->table($accessLogTable)
                ->select("HOUR(created_at) as h, event_type, COUNT(*) as cnt")
                ->where('created_at >=', $todayStart)
                ->where('created_at <=', $todayEnd)
                ->groupBy('h, event_type')
                ->get()->getResultArray();

            foreach ($hourlyRows as $hr) {
                $h = (int) $hr['h'];
                if (isset($todayHourly[$h])) {
                    if ($hr['event_type'] === 'pageview') {
                        $todayHourly[$h]['pageviews'] += (int) $hr['cnt'];
                    } elseif (in_array($hr['event_type'], ['cta_click', 'sticky_cta_click', 'whatsapp_click'], true)) {
                        $todayHourly[$h]['clicks'] += (int) $hr['cnt'];
                    }
                }
            }
        } catch (\Throwable) {}

        $topProducts = [];
        try {
            $topProductRows = $db->table($accessLogTable . ' a')
                ->select('a.product_id, p.title, p.price, p.slug, COUNT(a.id) as total_events, 
                          SUM(CASE WHEN a.event_type = "pageview" THEN 1 ELSE 0 END) as pvs,
                          SUM(CASE WHEN a.event_type IN ("cta_click", "sticky_cta_click", "whatsapp_click") THEN 1 ELSE 0 END) as clks')
                ->join('products p', 'p.id = a.product_id', 'inner')
                ->where('p.deleted_at IS NULL')
                ->groupBy('a.product_id')
                ->orderBy('total_events', 'DESC')
                ->limit(5)
                ->get()->getResultArray();

            foreach ($topProductRows as $tp) {
                $pvs = (int) ($tp['pvs'] ?? 0);
                $clks = (int) ($tp['clks'] ?? 0);
                $ctr = $pvs > 0 ? round(($clks / $pvs) * 100, 1) : 0;
                $coverImg = $db->table('product_images')
                    ->where('product_id', $tp['product_id'])
                    ->orderBy('is_cover', 'DESC')
                    ->orderBy('sort_order', 'ASC')
                    ->get()->getFirstRow();

                $topProducts[] = [
                    'id' => (int) $tp['product_id'],
                    'title' => $tp['title'],
                    'price' => (float) $tp['price'],
                    'slug' => $tp['slug'],
                    'pageviews' => $pvs,
                    'clicks' => $clks,
                    'ctr' => $ctr,
                    'image' => $coverImg ? $coverImg->image_url : null,
                ];
            }
        } catch (\Throwable) {}

        $leadsModel = new \App\Models\LeadModel();
        $totalLeads = (int) $leadsModel->countAllResults(false);
        $todayLeads = (int) $leadsModel->where('created_at >=', $todayStart)->where('created_at <=', $todayEnd)->countAllResults(false);

        $leadsEvolution = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $lbl = date('d/m', strtotime("-{$i} days"));
            $leadsEvolution[$d] = ['date' => $d, 'dayLabel' => $lbl, 'leads' => 0];
        }
        try {
            $leadRows = $db->table('leads')
                ->select("DATE(created_at) as log_date, COUNT(*) as cnt")
                ->where('created_at >=', $sinceDate)
                ->groupBy('log_date')
                ->get()->getResultArray();

            foreach ($leadRows as $lr) {
                $d = $lr['log_date'];
                if (isset($leadsEvolution[$d])) {
                    $leadsEvolution[$d]['leads'] = (int) $lr['cnt'];
                }
            }
        } catch (\Throwable) {}

        $groupModel = new \App\Models\WhatsappGroupModel();
        $totalGroups = (int) $groupModel->countAllResults(false);
        $totalParticipants = 0;
        try {
            $partRow = $db->table('whatsapp_groups')->select('SUM(participants_count) as total_part')->get()->getRow();
            $totalParticipants = (int) ($partRow->total_part ?? 0);
        } catch (\Throwable) {}

        $queueModel = new \App\Models\SystemJobQueueModel();
        $queueStats = $queueModel->getQueueStats();

        $overviewData = [
            'greeting' => $greeting,
            'formattedDate' => $formattedDate,
            'products' => [
                'total' => $totalProducts,
                'active' => $activeProducts,
                'inactive' => $inactiveProducts,
                'portfolioValue' => $portfolioValue,
                'overallPvs' => $overallPvs,
                'overallClicks' => $overallClicks,
                'overallCtr' => $overallCtr,
                'todayPvs' => $todayPvs,
                'todayClicks' => $todayClicks,
                'trafficEvolution' => array_values($trafficEvolution),
                'todayHourly' => array_values($todayHourly),
                'topProducts' => $topProducts,
            ],
            'leads' => [
                'total' => $totalLeads,
                'today' => $todayLeads,
                'evolution' => array_values($leadsEvolution),
            ],
            'whatsapp' => [
                'totalGroups' => $totalGroups,
                'totalParticipants' => $totalParticipants,
            ],
            'jobs' => $queueStats,
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
