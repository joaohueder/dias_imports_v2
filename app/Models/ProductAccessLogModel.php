<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductAccessLogModel extends Model
{
    protected $table            = 'product_access_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'visitor_id',
        'event_type',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'referrer',
        'device_type',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    public function recordEvent(int $productId, string $eventType, array $extra = []): bool
    {
        $request = service('request');
        $ip = $request->getIPAddress();
        $userAgent = (string) $request->getUserAgent();

        $cookieVisitor = $request->getCookie('di_visitor_id');
        if (empty($cookieVisitor)) {
            $cookieVisitor = $extra['visitor_id'] ?? hash('sha256', $ip . '|' . $userAgent . '|' . date('Y-m-d'));
        }

        // Determina tipo de dispositivo
        $deviceType = 'desktop';
        if ($request->getUserAgent()->isMobile()) {
            $deviceType = 'mobile';
        }

        $utmSource = $request->getGet('utm_source') ?: ($extra['utm_source'] ?? null);
        $utmMedium = $request->getGet('utm_medium') ?: ($extra['utm_medium'] ?? null);
        $utmCampaign = $request->getGet('utm_campaign') ?: ($extra['utm_campaign'] ?? null);
        $referrer = $request->getServer('HTTP_REFERER') ?: ($extra['referrer'] ?? null);

        // Se tiver referrer e não tiver utm_source, tenta deduzir origem
        if (empty($utmSource) && !empty($referrer)) {
            if (stripos($referrer, 'instagram.com') !== false || stripos($referrer, 'l.instagram.com') !== false) {
                $utmSource = 'Instagram';
            } elseif (stripos($referrer, 'facebook.com') !== false || stripos($referrer, 'fb.me') !== false) {
                $utmSource = 'Facebook';
            } elseif (stripos($referrer, 'whatsapp.com') !== false || stripos($referrer, 'wa.me') !== false) {
                $utmSource = 'WhatsApp';
            } elseif (stripos($referrer, 'google.com') !== false) {
                $utmSource = 'Google';
            } else {
                $utmSource = 'Referral';
            }
        } elseif (empty($utmSource)) {
            $utmSource = 'Direto';
        }

        return (bool) $this->insert([
            'product_id'   => $productId,
            'visitor_id'   => $cookieVisitor,
            'event_type'   => $eventType,
            'utm_source'   => mb_substr((string)$utmSource, 0, 100),
            'utm_medium'   => mb_substr((string)$utmMedium, 0, 100),
            'utm_campaign' => mb_substr((string)$utmCampaign, 0, 100),
            'referrer'     => mb_substr((string)$referrer, 0, 500),
            'device_type'  => $deviceType,
            'ip_address'   => $ip,
            'user_agent'   => mb_substr($userAgent, 0, 500),
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }

    public function getProductMetrics(int $productId, int $days = 7): array
    {
        $db = $this->db;
        $sinceDate = date('Y-m-d 00:00:00', strtotime("-{$days} days"));
        $todayDate = date('Y-m-d 00:00:00');
        $yesterdayDate = date('Y-m-d 00:00:00', strtotime("-1 day"));
        $todayEnd = date('Y-m-d 23:59:59');
        $yesterdayEnd = date('Y-m-d 23:59:59', strtotime("-1 day"));

        // 1. Total PageViews
        $totalPageviews = $db->table($this->table)
            ->where('product_id', $productId)
            ->where('event_type', 'pageview')
            ->countAllResults();

        // 2. Visitantes Únicos Totais
        $uniqueVisitorsRow = $db->table($this->table)
            ->select('COUNT(DISTINCT visitor_id) as total_unique')
            ->where('product_id', $productId)
            ->where('event_type', 'pageview')
            ->get()->getRow();
        $uniqueVisitors = (int) ($uniqueVisitorsRow->total_unique ?? 0);

        // 3. Cliques WhatsApp Totais (Purchase / CTA Clicks)
        $totalCtaClicks = $db->table($this->table)
            ->where('product_id', $productId)
            ->whereIn('event_type', ['cta_click', 'sticky_cta_click', 'whatsapp_click'])
            ->countAllResults();

        // 4. Taxa de Conversão
        $conversionRate = $totalPageviews > 0 ? round(($totalCtaClicks / $totalPageviews) * 100, 1) : 0;

        // 5. Comparativo Hoje x Ontem (PageViews)
        $todayPageviews = $db->table($this->table)
            ->where('product_id', $productId)
            ->where('event_type', 'pageview')
            ->where('created_at >=', $todayDate)
            ->where('created_at <=', $todayEnd)
            ->countAllResults();

        $yesterdayPageviews = $db->table($this->table)
            ->where('product_id', $productId)
            ->where('event_type', 'pageview')
            ->where('created_at >=', $yesterdayDate)
            ->where('created_at <=', $yesterdayEnd)
            ->countAllResults();

        // 6. Evolução Diária no período ($days)
        $dailyData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $dayDate = date('Y-m-d', strtotime("-{$i} days"));
            $dayLabel = date('d/m', strtotime("-{$i} days"));
            $dailyData[$dayDate] = [
                'date' => $dayDate,
                'dayLabel' => $dayLabel,
                'pageviews' => 0,
                'clicks' => 0,
                'percentage' => 0,
            ];
        }

        $dailyRows = $db->table($this->table)
            ->select("DATE(created_at) as log_date, event_type, COUNT(*) as cnt")
            ->where('product_id', $productId)
            ->where('created_at >=', $sinceDate)
            ->groupBy('log_date, event_type')
            ->get()->getResultArray();

        $maxDailyPv = 1;
        foreach ($dailyRows as $r) {
            $d = $r['log_date'];
            if (isset($dailyData[$d])) {
                if ($r['event_type'] === 'pageview') {
                    $dailyData[$d]['pageviews'] += (int) $r['cnt'];
                    if ($dailyData[$d]['pageviews'] > $maxDailyPv) {
                        $maxDailyPv = $dailyData[$d]['pageviews'];
                    }
                } else {
                    $dailyData[$d]['clicks'] += (int) $r['cnt'];
                }
            }
        }

        foreach ($dailyData as &$item) {
            $item['percentage'] = round(($item['pageviews'] / $maxDailyPv) * 100);
        }
        unset($item);

        // 7. Distribuição de Fontes de Tráfego
        $sourcesRows = $db->table($this->table)
            ->select('utm_source, COUNT(*) as total')
            ->where('product_id', $productId)
            ->where('event_type', 'pageview')
            ->groupBy('utm_source')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $sources = [];
        $totalSourcesCount = array_sum(array_column($sourcesRows, 'total')) ?: 1;
        foreach ($sourcesRows as $s) {
            $srcName = !empty($s['utm_source']) ? $s['utm_source'] : 'Direto';
            $sources[] = [
                'name' => $srcName,
                'total' => (int) $s['total'],
                'percentage' => round(((int)$s['total'] / $totalSourcesCount) * 100, 1),
            ];
        }

        // 8. Distribuição de Dispositivos
        $deviceRows = $db->table($this->table)
            ->select('device_type, COUNT(*) as total')
            ->where('product_id', $productId)
            ->groupBy('device_type')
            ->get()->getResultArray();

        $devices = ['mobile' => 0, 'desktop' => 0, 'tablet' => 0];
        $totalDevices = 0;
        foreach ($deviceRows as $dev) {
            $devices[$dev['device_type']] = (int) $dev['total'];
            $totalDevices += (int) $dev['total'];
        }
        $mobilePct = $totalDevices > 0 ? round((($devices['mobile'] + $devices['tablet']) / $totalDevices) * 100, 1) : 100;

        // 9. Últimos Eventos Registrados (Top 15)
        $recentLogs = $db->table($this->table)
            ->where('product_id', $productId)
            ->orderBy('created_at', 'DESC')
            ->limit(15)
            ->get()->getResultArray();

        return [
            'totalPageviews'     => $totalPageviews,
            'uniqueVisitors'     => $uniqueVisitors,
            'totalCtaClicks'     => $totalCtaClicks,
            'conversionRate'     => $conversionRate,
            'todayPageviews'     => $todayPageviews,
            'yesterdayPageviews' => $yesterdayPageviews,
            'evolution'          => array_values($dailyData),
            'sources'            => $sources,
            'mobilePct'          => $mobilePct,
            'recentLogs'         => $recentLogs,
            'period'             => $days,
        ];
    }
}
