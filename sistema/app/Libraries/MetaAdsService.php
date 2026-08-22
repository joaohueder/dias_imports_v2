<?php

namespace App\Libraries;

use App\Models\MetaAdsSettingModel;
use CodeIgniter\HTTP\CURLRequest;
use Config\Services;
use RuntimeException;
use Throwable;

class MetaAdsService
{
    private const GRAPH_API_VERSION = 'v20.0';
    private const GRAPH_API_BASE = 'https://graph.facebook.com';
    private MetaAdsSettingModel $settings;

    public function __construct()
    {
        $this->settings = new MetaAdsSettingModel();
    }

    public function getSettings(): array
    {
        return $this->settings->first() ?? [
            'pixel_id' => '',
            'access_token_encrypted' => '',
            'test_event_code' => '',
            'last_test_status' => null,
            'last_tested_at' => null,
        ];
    }

    public function isEncryptionReady(): bool
    {
        return trim((string) config('Encryption')->key) !== '';
    }

    public function isConfigured(?array $settings = null): bool
    {
        $settings ??= $this->getSettings();
        return ($settings['pixel_id'] ?? '') !== ''
            && ($settings['access_token_encrypted'] ?? '') !== ''
            && $this->isEncryptionReady();
    }

    public function saveSettings(string $pixelId, string $accessToken, ?string $testEventCode): void
    {
        $pixelId = trim($pixelId);
        $testEventCode = trim((string) $testEventCode);

        if ($pixelId === '') {
            throw new RuntimeException('Informe o Pixel ID.');
        }

        if (! preg_match('/^\d{10,25}$/', $pixelId)) {
            throw new RuntimeException('O Pixel ID deve conter apenas números (geralmente entre 10 e 25 dígitos).');
        }

        $current = $this->getSettings();
        $data = [
            'pixel_id' => $pixelId,
            'test_event_code' => $testEventCode !== '' ? $testEventCode : null,
        ];

        if ($accessToken !== '') {
            if (! $this->isEncryptionReady()) {
                throw new RuntimeException('Configure encryption.key no ambiente antes de salvar o Token da API.');
            }
            $data['access_token_encrypted'] = base64_encode(service('encrypter')->encrypt($accessToken));
        } elseif (($current['access_token_encrypted'] ?? '') === '') {
            throw new RuntimeException('Informe o Token da API do Pixel (Conversions API).');
        }

        isset($current['id']) ? $this->settings->update($current['id'], $data) : $this->settings->insert($data);
    }

    public function getDecryptedToken(): string
    {
        $settings = $this->getSettings();
        $encrypted = $settings['access_token_encrypted'] ?? '';
        if ($encrypted === '') {
            throw new RuntimeException('Nenhum token da API configurado.');
        }

        if (! $this->isEncryptionReady()) {
            throw new RuntimeException('Chave de criptografia não configurada.');
        }

        try {
            $raw = base64_decode($encrypted, true);
            if ($raw === false) {
                throw new RuntimeException('Falha ao decodificar credencial.');
            }
            return service('encrypter')->decrypt($raw);
        } catch (Throwable $e) {
            throw new RuntimeException('Não foi possível descriptografar o Token da API: ' . $e->getMessage());
        }
    }

    public function testConnection(): array
    {
        $settings = $this->getSettings();
        if (($settings['pixel_id'] ?? '') === '') {
            throw new RuntimeException('Pixel ID não configurado.');
        }

        $token = $this->getDecryptedToken();
        $pixelId = $settings['pixel_id'];

        // Send a test event to Facebook Conversions API
        $eventId = 'test_' . bin2hex(random_bytes(8));
        $eventTime = time();

        $eventData = [
            'event_name' => 'TestConnection',
            'event_time' => $eventTime,
            'event_id' => $eventId,
            'action_source' => 'system_generated',
            'user_data' => [
                'client_ip_address' => '127.0.0.1',
                'client_user_agent' => 'DiasImports-System-Test/1.0',
            ],
            'custom_data' => [
                'source' => 'Dias Imports Marketing v2 Admin Test',
            ],
        ];

        $payload = [
            'data' => [$eventData],
        ];

        if (! empty($settings['test_event_code'])) {
            $payload['test_event_code'] = $settings['test_event_code'];
        }

        try {
            $url = self::GRAPH_API_BASE . '/' . self::GRAPH_API_VERSION . '/' . $pixelId . '/events?access_token=' . urlencode($token);
            
            $client = Services::curlrequest([
                'timeout' => 15,
                'http_errors' => false,
            ]);

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($payload),
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string) $response->getBody();
            $data = json_decode($body, true);

            if ($statusCode >= 200 && $statusCode < 300 && isset($data['events_received'])) {
                $this->updateTestStatus('success');
                return [
                    'success' => true,
                    'events_received' => $data['events_received'] ?? 1,
                    'fbtrace_id' => $data['fbtrace_id'] ?? null,
                ];
            }

            $errorMessage = $data['error']['message'] ?? 'Erro desconhecido na resposta da Meta Ads API (HTTP ' . $statusCode . ')';
            $this->updateTestStatus('failed');
            throw new RuntimeException($errorMessage);
        } catch (Throwable $e) {
            $this->updateTestStatus('failed');
            throw new RuntimeException('Falha na comunicação com a API da Meta: ' . $e->getMessage());
        }
    }

    public function sendConversionEvent(string $eventName, array $userData = [], array $customData = [], ?string $eventId = null, ?string $eventSourceUrl = null): bool
    {
        $settings = $this->getSettings();
        if (! $this->isConfigured($settings)) {
            return false;
        }

        try {
            $token = $this->getDecryptedToken();
            $pixelId = $settings['pixel_id'];

            $formattedUserData = [];
            
            // Format & hash user data per Meta requirements if present
            if (! empty($userData['client_ip_address'])) {
                $formattedUserData['client_ip_address'] = $userData['client_ip_address'];
            }
            if (! empty($userData['client_user_agent'])) {
                $formattedUserData['client_user_agent'] = $userData['client_user_agent'];
            }
            if (! empty($userData['ph'])) {
                $phone = preg_replace('/\D+/', '', $userData['ph']);
                if (strlen($phone) >= 10 && ! str_starts_with($phone, '55')) {
                    $phone = '55' . $phone;
                }
                $formattedUserData['ph'] = [hash('sha256', $phone)];
            }
            if (! empty($userData['fn'])) {
                $firstName = mb_strtolower(trim($userData['fn']));
                $formattedUserData['fn'] = [hash('sha256', $firstName)];
            }
            if (! empty($userData['em'])) {
                $email = mb_strtolower(trim($userData['em']));
                $formattedUserData['em'] = [hash('sha256', $email)];
            }
            if (! empty($userData['fbp'])) {
                $formattedUserData['fbp'] = $userData['fbp'];
            }
            if (! empty($userData['fbc'])) {
                $formattedUserData['fbc'] = $userData['fbc'];
            }

            $event = [
                'event_name' => $eventName,
                'event_time' => time(),
                'action_source' => 'website',
                'user_data' => $formattedUserData,
            ];

            if ($eventId !== null && $eventId !== '') {
                $event['event_id'] = $eventId;
            }
            if ($eventSourceUrl !== null && $eventSourceUrl !== '') {
                $event['event_source_url'] = $eventSourceUrl;
            }
            if (! empty($customData)) {
                $event['custom_data'] = $customData;
            }

            $payload = [
                'data' => [$event],
            ];

            if (! empty($settings['test_event_code'])) {
                $payload['test_event_code'] = $settings['test_event_code'];
            }

            $url = self::GRAPH_API_BASE . '/' . self::GRAPH_API_VERSION . '/' . $pixelId . '/events?access_token=' . urlencode($token);

            $client = Services::curlrequest([
                'timeout' => 5,
                'http_errors' => false,
            ]);

            $response = $client->post($url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'body' => json_encode($payload),
            ]);

            return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
        } catch (Throwable) {
            return false;
        }
    }

    private function updateTestStatus(string $status): void
    {
        $current = $this->getSettings();
        if (isset($current['id'])) {
            $this->settings->update($current['id'], [
                'last_test_status' => $status,
                'last_tested_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
