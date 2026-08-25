<?php

namespace App\Models;

use CodeIgniter\Model;

class LandingLeadAccessLogModel extends Model
{
    protected $table            = 'landing_lead_access_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
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

    public function recordEvent(string $eventType = 'pageview', array $extra = []): bool
    {
        $request = service('request');
        $ip = $request->getIPAddress();
        $userAgent = (string) $request->getUserAgent();

        $cookieVisitor = $request->getCookie('di_lead_visitor_id');
        if (empty($cookieVisitor)) {
            $cookieVisitor = $extra['visitor_id'] ?? hash('sha256', $ip . '|' . $userAgent . '|' . date('Y-m-d'));
        }

        $deviceType = 'desktop';
        if ($request->getUserAgent()->isMobile()) {
            $deviceType = 'mobile';
        }

        $utmSource = $request->getGet('utm_source') ?: ($extra['utm_source'] ?? null);
        $utmMedium = $request->getGet('utm_medium') ?: ($extra['utm_medium'] ?? null);
        $utmCampaign = $request->getGet('utm_campaign') ?: ($extra['utm_campaign'] ?? null);
        $referrer = $request->getServer('HTTP_REFERER') ?: ($extra['referrer'] ?? null);

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
}
