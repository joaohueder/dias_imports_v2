<?php

namespace App\Controllers;

use App\Libraries\UserPermissions;
use App\Models\AppSettingModel;
use App\Models\LeadModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class VipLeads extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (! UserPermissions::hasPermission('vip_leads', 'view')) {
            header('Location: ' . site_url('/'));
            exit;
        }
    }

    public function index(): string
    {
        $days = (int) ($this->request->getGet('period') ?? 7);
        if (! in_array($days, [7, 14, 21, 30], true)) {
            $days = 7;
        }

        $search = trim((string) ($this->request->getGet('q') ?? ''));
        $dateFilter = trim((string) ($this->request->getGet('date') ?? ''));

        $data = $this->getLeadsData($days, $search, $dateFilter);

        $layoutMaxWidth = $this->getLayoutMaxWidth();

        $userName = (string) session()->get('user_name');
        $userEmail = (string) session()->get('user_email');
        $firstName = trim(explode(' ', $userName)[0] ?? 'Usuário');
        $userInitials = $this->extractInitials($userName);

        $realtimeModel = new \App\Models\RealtimeScreenSettingModel();
        $isRealtimeActive = $realtimeModel->isScreenActive('vip_leads');
        $realtimeInterval = $realtimeModel->getInterval('vip_leads');

        return view('admin/leads/index', array_merge($data, [
            'pageTitle' => 'Leads VIP',
            'pageDescription' => 'Gestão e acompanhamento dos contatos captados pela Landing Page de Leads.',
            'pageIcon' => 'ti-crown',
            'activePage' => 'vip',
            'layoutMaxWidth' => $layoutMaxWidth,
            'hasLeadsJs' => true,
            'isRealtimeActive' => $isRealtimeActive,
            'realtimeInterval' => $realtimeInterval,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'firstName' => $firstName,
            'userInitials' => $userInitials,
            'navigation' => Home::getNavigationList(),
            'currentPeriod' => $days,
            'searchQuery' => $search,
            'dateFilter' => $dateFilter,
        ]));
    }

    public function feed(): ResponseInterface
    {
        $days = (int) ($this->request->getGet('period') ?? 7);
        if (! in_array($days, [7, 14, 21, 30], true)) {
            $days = 7;
        }

        $search = trim((string) ($this->request->getGet('q') ?? ''));
        $dateFilter = trim((string) ($this->request->getGet('date') ?? ''));

        // Se for a consulta padrão (7 dias, sem filtro e sem busca), usa snapshot pré-processado
        if ($days === 7 && $search === '' && $dateFilter === '') {
            $snapshotService = new \App\Services\RealtimeSnapshotService();
            $snapshot = $snapshotService->getSnapshot('vip_leads');

            if ($snapshot !== null && !empty($snapshot['data'])) {
                helper('telemetry');
                $telemetry = get_footer_telemetry();

                return $this->response->setJSON([
                    'success' => true,
                    'metrics' => $snapshot['data']['metrics'],
                    'htmlTable' => $snapshot['data']['htmlTable'],
                    'htmlMetrics' => $snapshot['data']['htmlMetrics'],
                    'totalResults' => $snapshot['data']['totalResults'],
                    'footerHtml' => $telemetry['html'],
                    'telemetry' => [
                        'connectionsLastHour' => $telemetry['connectionsLastHour'],
                        'maxConnectionsPerHour' => $telemetry['maxConnectionsPerHour'],
                        'loadTime' => $telemetry['loadTime'],
                    ],
                ]);
            }
        }

        $data = $this->getLeadsData($days, $search, $dateFilter);

        $htmlTable = view('admin/leads/_table_rows', ['leads' => $data['leads']]);
        $htmlMetrics = view('admin/leads/_metrics', $data['metrics']);

        helper('telemetry');
        $telemetry = get_footer_telemetry();

        return $this->response->setJSON([
            'success' => true,
            'metrics' => $data['metrics'],
            'htmlTable' => $htmlTable,
            'htmlMetrics' => $htmlMetrics,
            'totalResults' => count($data['leads']),
            'footerHtml' => $telemetry['html'],
            'telemetry' => [
                'connectionsLastHour' => $telemetry['connectionsLastHour'],
                'maxConnectionsPerHour' => $telemetry['maxConnectionsPerHour'],
                'loadTime' => $telemetry['loadTime'],
            ],
        ]);
    }

    public function update(int $id): RedirectResponse|ResponseInterface
    {
        if (! UserPermissions::hasPermission('vip_leads', 'edit')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão.'])->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'Sem permissão para editar leads.');
        }

        $leadModel = new LeadModel();
        $lead = $leadModel->find($id);
        if (! $lead) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lead não encontrado.'])->setStatusCode(404);
            }
            return redirect()->back()->with('error', 'Lead não encontrado.');
        }

        $name = trim((string) $this->request->getPost('name'));
        $rawPhone = (string) $this->request->getPost('phone');
        $phone = preg_replace('/\D+/', '', $rawPhone) ?? '';

        if ($name === '' || mb_strlen($name) > 120) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Nome inválido.'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'Nome inválido.');
        }

        if (! preg_match('/^(?:55)?\d{10,11}$/', $phone)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'WhatsApp inválido.'])->setStatusCode(400);
            }
            return redirect()->back()->withInput()->with('error', 'WhatsApp inválido.');
        }

        $leadModel->update($id, [
            'name' => $name,
            'phone' => $phone,
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Lead atualizado com sucesso!',
                'csrfHash' => csrf_hash(),
            ]);
        }

        return redirect()->to('/leads-vip')->with('success', 'Lead atualizado com sucesso!');
    }

    public function delete(int $id): RedirectResponse|ResponseInterface
    {
        if (! UserPermissions::hasPermission('vip_leads', 'delete')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão.', 'csrfHash' => csrf_hash()])->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'Sem permissão para excluir leads.');
        }

        $leadModel = new LeadModel();
        $lead = $leadModel->find($id);
        if (! $lead) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Lead não encontrado.', 'csrfHash' => csrf_hash()])->setStatusCode(404);
            }
            return redirect()->back()->with('error', 'Lead não encontrado.');
        }

        $leadModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => true, 'message' => 'Lead excluído com sucesso!', 'csrfHash' => csrf_hash()]);
        }

        return redirect()->to('/leads-vip')->with('success', 'Lead excluído com sucesso!');
    }

    private function getLeadsData(int $days, string $search = '', string $dateFilter = ''): array
    {
        $db = \Config\Database::connect();
        $leadModel = new LeadModel();

        $todayStr = date('Y-m-d');
        $yesterdayStr = date('Y-m-d', strtotime('-1 day'));

        // Consolidar totalLeads, todayCount e yesterdayCount em uma única consulta
        $overviewStats = $db->table('leads')
            ->select("
                COUNT(*) as totalLeads,
                SUM(CASE WHEN DATE(created_at) = '{$todayStr}' THEN 1 ELSE 0 END) as todayCount,
                SUM(CASE WHEN DATE(created_at) = '{$yesterdayStr}' THEN 1 ELSE 0 END) as yesterdayCount
            ")
            ->get()
            ->getRowArray();

        $totalLeads = (int) ($overviewStats['totalLeads'] ?? 0);
        $todayCount = (int) ($overviewStats['todayCount'] ?? 0);
        $yesterdayCount = (int) ($overviewStats['yesterdayCount'] ?? 0);

        // Evolução dos últimos $days dias
        $evolutionDays = [];
        $startDate = date('Y-m-d', strtotime("-" . ($days - 1) . " days"));

        $dailyStatsRaw = $db->table('leads')
            ->select("DATE(created_at) as lead_date, COUNT(id) as total")
            ->where("DATE(created_at) >=", $startDate)
            ->groupBy("DATE(created_at)")
            ->get()
            ->getResultArray();

        $dailyMap = [];
        foreach ($dailyStatsRaw as $row) {
            $dailyMap[$row['lead_date']] = (int) $row['total'];
        }

        $maxDaily = 1;
        for ($i = $days - 1; $i >= 0; $i--) {
            $currentD = date('Y-m-d', strtotime("-{$i} days"));
            $count = $dailyMap[$currentD] ?? 0;
            if ($count > $maxDaily) {
                $maxDaily = $count;
            }
            $evolutionDays[] = [
                'date' => $currentD,
                'dayLabel' => date('d/m', strtotime($currentD)),
                'weekday' => date('D', strtotime($currentD)),
                'count' => $count,
            ];
        }

        foreach ($evolutionDays as &$ed) {
            $ed['percentage'] = round(($ed['count'] / $maxDaily) * 100);
        }
        unset($ed);

        // Busca e listagem dos leads
        $builder = $leadModel->orderBy('created_at', 'DESC');

        if ($search !== '') {
            $cleanDigits = preg_replace('/\D+/', '', $search);
            $builder->groupStart()
                ->like('name', $search);
            if ($cleanDigits !== '') {
                $builder->orLike('phone', $cleanDigits);
            }
            $builder->groupEnd();
        }

        if ($dateFilter !== '') {
            $builder->where("DATE(created_at)", $dateFilter);
        }

        $leads = $builder->findAll(500);

        return [
            'metrics' => [
                'totalLeads' => $totalLeads,
                'todayCount' => $todayCount,
                'yesterdayCount' => $yesterdayCount,
                'periodDays' => $days,
                'evolution' => $evolutionDays,
                'maxDaily' => $maxDaily,
            ],
            'leads' => $leads,
        ];
    }

    private function extractInitials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        if (!$parts || $parts[0] === '') return 'US';
        if (count($parts) === 1) return mb_strtoupper(mb_substr($parts[0], 0, 2));
        return mb_strtoupper(mb_substr($parts[0], 0, 1) . mb_substr(end($parts), 0, 1));
    }
}
