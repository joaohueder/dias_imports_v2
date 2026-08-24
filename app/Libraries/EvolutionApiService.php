<?php

namespace App\Libraries;

use App\Models\EvolutionSettingModel;
use RuntimeException;
use Throwable;

class EvolutionApiService
{
    private const MAX_RESPONSE_BYTES = 1048576;
    private EvolutionSettingModel $settings;

    public function __construct()
    {
        $this->settings = new EvolutionSettingModel();
    }

    public function getSettings(): array
    {
        return $this->settings->first() ?? [
            'base_url' => '',
            'api_key_encrypted' => '',
            'min_delay_seconds' => 5,
            'max_delay_seconds' => 30,
            'default_instance_name' => null,
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
        return $this->isEncryptionReady()
            && ($settings['base_url'] ?? '') !== ''
            && ($settings['api_key_encrypted'] ?? '') !== '';
    }

    public function saveSettings(string $baseUrl, string $apiKey, ?int $minDelay = null, ?int $maxDelay = null): void
    {
        $baseUrl = $this->validateBaseUrl($baseUrl);
        $current = $this->getSettings();

        $data = [
            'base_url' => $baseUrl,
        ];

        if ($minDelay !== null && $maxDelay !== null) {
            if ($minDelay < 1 || $maxDelay > 3600 || $maxDelay < $minDelay) {
                throw new RuntimeException('Informe esperas entre 1 e 3600 segundos, com o máximo igual ou maior que o mínimo.');
            }
            $data['min_delay_seconds'] = $minDelay;
            $data['max_delay_seconds'] = $maxDelay;
        }

        if ($apiKey !== '') {
            if (strlen($apiKey) > 1000) {
                throw new RuntimeException('A Global API Key excede o tamanho permitido.');
            }
            if (! $this->isEncryptionReady()) {
                throw new RuntimeException('Configure encryption.key no ambiente antes de salvar a Global API Key.');
            }
            $data['api_key_encrypted'] = base64_encode(service('encrypter')->encrypt($apiKey));
        } elseif (($current['api_key_encrypted'] ?? '') === '') {
            throw new RuntimeException('Informe a Global API Key.');
        }

        isset($current['id']) ? $this->settings->update($current['id'], $data) : $this->settings->insert($data);
    }

    public function fetchInstances(): array
    {
        $payload = $this->request('GET', '/instance/fetchInstances');
        $rows = array_is_list($payload) ? $payload : ($payload['instances'] ?? []);
        if (! is_array($rows)) {
            return [];
        }

        $instances = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $source = is_array($row['instance'] ?? null) ? $row['instance'] : $row;
            $name = trim((string) ($source['instanceName'] ?? $source['name'] ?? $row['instanceName'] ?? ''));
            if ($name === '') {
                continue;
            }
            $state = mb_strtolower(trim((string) ($source['connectionStatus'] ?? $source['state'] ?? $row['connectionStatus'] ?? 'unknown')));
            $number = (string) ($source['ownerJid'] ?? $row['ownerJid'] ?? $source['number'] ?? '');
            $number = preg_replace('/@.*$/', '', $number) ?? '';
            $instances[] = [
                'name' => $name,
                'profile_name' => trim((string) ($source['profileName'] ?? $row['profileName'] ?? $name)),
                'profile_picture' => $this->safeImageUrl((string) ($source['profilePicUrl'] ?? $row['profilePicUrl'] ?? '')),
                'state' => $state,
                'connected' => in_array($state, ['open', 'connected'], true),
                'number' => $number,
            ];
        }

        return $instances;
    }

    public function testConnection(): int
    {
        return count($this->fetchInstances());
    }

    public function createInstance(string $name): bool
    {
        $name = $this->validateInstanceName($name);
        $isFirstInstance = $this->fetchInstances() === [];
        $this->request('POST', '/instance/create', [
            'instanceName' => $name,
            'integration' => 'WHATSAPP-BAILEYS',
            'qrcode' => true,
        ]);

        if ($isFirstInstance) {
            $settings = $this->getSettings();
            if (! isset($settings['id'])) {
                throw new RuntimeException('Instância criada, mas as configurações não foram encontradas para defini-la como padrão.');
            }
            $this->settings->update($settings['id'], ['default_instance_name' => $name]);
        }

        return $isFirstInstance;
    }

    public function connectInstance(string $name): array
    {
        return $this->request('GET', '/instance/connect/' . rawurlencode($this->validateInstanceName($name)));
    }

    public function findInstance(string $name): array
    {
        $name = $this->validateInstanceName($name);
        foreach ($this->fetchInstances() as $instance) {
            if ($instance['name'] === $name) {
                return $instance;
            }
        }

        throw new RuntimeException('Instância não encontrada na Evolution API.');
    }

    public function sendTestMessage(string $name, string $phone): void
    {
        $name = $this->validateInstanceName($name);
        $phone = $this->normalizeBrazilianPhone($phone);
        $instance = $this->findInstance($name);
        if (! ($instance['connected'] ?? false)) {
            throw new RuntimeException('Conecte a instância antes de testar o envio.');
        }

        $this->request('POST', '/message/sendText/' . rawurlencode($name), [
            'number' => $phone,
            'text' => 'Mensagem de teste enviada pela integração da Dias Imports.',
        ]);
    }

    public function fetchAllGroups(string $name, bool $getParticipants = true): array
    {
        $name = $this->validateInstanceName($name);
        $query = $getParticipants ? '?getParticipants=true' : '?getParticipants=false';
        $payload = $this->request('GET', '/group/fetchAllGroups/' . rawurlencode($name) . $query);
        $rows = array_is_list($payload) ? $payload : ($payload['groups'] ?? $payload['data'] ?? []);
        return is_array($rows) ? $rows : [];
    }

    public function findGroupPicture(string $name, string $groupJid): string
    {
        try {
            $name = $this->validateInstanceName($name);
            $payload = $this->request('GET', '/group/findGroupPictureUrl/' . rawurlencode($name) . '?groupJid=' . rawurlencode($groupJid));
            $url = $payload['pictureUrl'] ?? $payload['profilePicUrl'] ?? $payload['url'] ?? '';
            return $this->safeImageUrl((string) $url);
        } catch (Throwable) {
            return '';
        }
    }

    public function sendGroupTestMessage(string $name, string $groupJid, string $text = 'Mensagem de teste enviada pelo painel Dias Imports.'): void
    {
        $name = $this->validateInstanceName($name);
        $instance = $this->findInstance($name);
        if (! ($instance['connected'] ?? false)) {
            throw new RuntimeException('A instância precisa estar conectada para enviar mensagens.');
        }

        $this->request('POST', '/message/sendText/' . rawurlencode($name), [
            'number' => $groupJid,
            'text' => $text,
        ]);
    }

    public function sendGroupMessage(string $name, string $groupJid, string $text): array
    {
        $name = $this->validateInstanceName($name);
        return $this->request('POST', '/message/sendText/' . rawurlencode($name), [
            'number' => $groupJid,
            'text'   => $text,
        ]);
    }

    public function sendGroupMedia(string $name, string $groupJid, string $mediaUrl, string $caption = '', string $mediaType = 'image'): array
    {
        $name = $this->validateInstanceName($name);
        $fileName = basename(parse_url($mediaUrl, PHP_URL_PATH) ?: 'imagem.jpg');
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'image/jpeg',
        };

        return $this->request('POST', '/message/sendMedia/' . rawurlencode($name), [
            'number'    => $groupJid,
            'mediatype' => $mediaType,
            'mimetype'  => $mime,
            'caption'   => $caption,
            'media'     => $mediaUrl,
            'fileName'  => $fileName,
        ]);
    }

    public function createGroup(string $name, string $subject, array $participants, string $description = ''): array
    {
        $name = $this->validateInstanceName($name);
        $subject = trim($subject);
        if ($subject === '') {
            throw new RuntimeException('Informe o nome do grupo.');
        }

        $cleanParticipants = [];
        foreach ($participants as $p) {
            $cleaned = preg_replace('/[^\d]/', '', (string)$p);
            if ($cleaned !== '') {
                $cleanParticipants[] = $cleaned;
            }
        }

        $data = [
            'subject' => $subject,
            'participants' => $cleanParticipants,
        ];
        if ($description !== '') {
            $data['description'] = $description;
        }

        return $this->request('POST', '/group/createGroup/' . rawurlencode($name), $data);
    }

    public function updateGroup(string $name, string $groupJid, string $subject, string $description = ''): void
    {
        $name = $this->validateInstanceName($name);
        $subject = trim($subject);
        if ($subject === '') {
            throw new RuntimeException('Informe o nome do grupo.');
        }

        $this->request('POST', '/group/updateGroupSubject/' . rawurlencode($name) . '?groupJid=' . rawurlencode($groupJid), [
            'subject' => $subject,
        ]);

        if ($description !== '') {
            $this->request('POST', '/group/updateGroupDescription/' . rawurlencode($name) . '?groupJid=' . rawurlencode($groupJid), [
                'description' => $description,
            ]);
        }
    }

    public function logoutInstance(string $name): void
    {
        $this->request('DELETE', '/instance/logout/' . rawurlencode($this->validateInstanceName($name)));
    }

    public function deleteInstance(string $name): void
    {
        $this->request('DELETE', '/instance/delete/' . rawurlencode($this->validateInstanceName($name)));
    }

    public function setDefaultInstance(string $name): void
    {
        $name = $this->validateInstanceName($name);
        $exists = array_filter($this->fetchInstances(), static fn (array $instance): bool => $instance['name'] === $name);
        if ($exists === []) {
            throw new RuntimeException('Instância não encontrada na Evolution API.');
        }
        $settings = $this->getSettings();
        if (! isset($settings['id'])) {
            throw new RuntimeException('Salve as credenciais antes de definir uma instância padrão.');
        }
        $this->settings->update($settings['id'], ['default_instance_name' => $name]);
    }

    public function recordTest(string $status): void
    {
        $settings = $this->getSettings();
        if (isset($settings['id'])) {
            $this->settings->update($settings['id'], [
                'last_test_status' => $status,
                'last_tested_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function request(string $method, string $path, ?array $json = null): array
    {
        $settings = $this->getSettings();
        if (! $this->isConfigured($settings)) {
            throw new RuntimeException('Configure a URL, a Global API Key e encryption.key antes de acessar a Evolution API.');
        }
        $baseUrl = $this->validateBaseUrl((string) $settings['base_url']);
        try {
            $encrypted = base64_decode((string) $settings['api_key_encrypted'], true);
            if ($encrypted === false) {
                throw new RuntimeException('Credencial armazenada inválida.');
            }
            $apiKey = service('encrypter')->decrypt($encrypted);
            $options = [
                'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'apikey' => $apiKey],
                'connect_timeout' => 10,
                'timeout' => 60,
                'http_errors' => false,
                'allow_redirects' => false,
                'verify' => $this->resolveTlsVerification(),
            ];
            if ($json !== null) {
                $options['json'] = $json;
            }
            $response = service('curlrequest')->request($method, $baseUrl . $path, $options);
        } catch (Throwable $exception) {
            [$category, $message] = $this->classifyTransportFailure($exception);
            log_message('error', 'Evolution API transport failed: {category} ({type})', [
                'category' => $category,
                'type' => $exception::class,
            ]);
            throw new RuntimeException($message);
        }

        $status = $response->getStatusCode();
        $body = $response->getBody();
        if (strlen($body) > self::MAX_RESPONSE_BYTES) {
            throw new RuntimeException('A Evolution API retornou uma resposta maior que o permitido.');
        }
        $decoded = $body === '' ? [] : json_decode($body, true);
        if ($status < 200 || $status >= 300) {
            $message = match ($status) {
                400, 422 => 'A Evolution API rejeitou os dados enviados.',
                401, 403 => 'A Evolution API recusou a credencial configurada.',
                404 => 'Recurso ou instância não encontrado na Evolution API.',
                409 => 'A operação entrou em conflito com o estado atual da instância.',
                429 => 'Limite temporário da Evolution API atingido. Tente novamente mais tarde.',
                default => 'A Evolution API está indisponível ou retornou uma falha inesperada.',
            };
            throw new RuntimeException($message);
        }
        if ($body !== '' && ! is_array($decoded)) {
            throw new RuntimeException('A Evolution API retornou uma resposta inválida.');
        }
        return $decoded ?? [];
    }

    private function resolveTlsVerification(): bool|string
    {
        $configured = trim((string) ini_get('curl.cainfo'));
        if ($configured !== '' && is_file($configured)) {
            return $configured;
        }
        $configured = trim((string) ini_get('openssl.cafile'));
        if ($configured !== '' && is_file($configured)) {
            return $configured;
        }
        $bundled = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'cacert.pem';

        return is_file($bundled) ? $bundled : true;
    }

    private function classifyTransportFailure(Throwable $exception): array
    {
        $detail = mb_strtolower($exception->getMessage());
        if (preg_match('/(?:curl error|erro curl).*?(?:60|77)\b/', $detail)
            || str_contains($detail, 'certificate')
            || str_contains($detail, 'ssl')
            || str_contains($detail, 'tls')
        ) {
            return ['tls', 'Falha ao validar o certificado HTTPS da Evolution API. Verifique o certificado do servidor e o arquivo CA do PHP.'];
        }
        if (preg_match('/(?:curl error|erro curl).*?28\b/', $detail)
            || str_contains($detail, 'timed out')
            || str_contains($detail, 'timeout')
        ) {
            return ['timeout', 'A Evolution API não respondeu a tempo (timeout). Verifique servidor, proxy e firewall.'];
        }
        if (preg_match('/(?:curl error|erro curl).*?6\b/', $detail)
            || str_contains($detail, 'resolve host')
            || str_contains($detail, 'could not resolve')
        ) {
            return ['dns', 'O domínio da Evolution API não pôde ser resolvido durante a conexão. Verifique o DNS.'];
        }
        if (preg_match('/(?:curl error|erro curl).*?7\b/', $detail)
            || str_contains($detail, 'connect to host')
            || str_contains($detail, 'failed to connect')
            || str_contains($detail, 'connection refused')
        ) {
            return ['connection', 'A conexão com a Evolution API foi recusada. Verifique host, porta, proxy e firewall.'];
        }

        return ['unknown', 'Não foi possível comunicar com a Evolution API. Verifique a URL, a porta e a disponibilidade do servidor.'];
    }

    private function validateBaseUrl(string $url): string
    {
        $url = rtrim(trim($url), '/');
        $parts = parse_url($url);
        if (! is_array($parts)
            || ($parts['scheme'] ?? '') !== 'https'
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['query'])
            || isset($parts['fragment'])
            || (($parts['path'] ?? '') !== '')
        ) {
            throw new RuntimeException('Informe somente a URL base HTTPS pública, sem /api, caminho, credenciais ou parâmetros.');
        }
        $host = (string) $parts['host'];
        $addresses = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        if ($addresses === []) {
            throw new RuntimeException('O domínio da Evolution API não pôde ser resolvido.');
        }
        foreach ($addresses as $address) {
            if (filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                throw new RuntimeException('A URL da Evolution API deve apontar para um endereço público.');
            }
        }
        return $url;
    }

    private function validateInstanceName(string $name): string
    {
        $name = trim($name);
        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_-]{2,79}$/', $name)) {
            throw new RuntimeException('Use de 3 a 80 caracteres no nome da instância: letras, números, hífen ou sublinhado.');
        }
        return $name;
    }

    private function normalizeBrazilianPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '55') && strlen($digits) > 11) {
            $digits = substr($digits, 2);
        }
        if (! preg_match('/^[1-9]{2}(?:9\d{8}|[2-8]\d{7})$/', $digits)) {
            throw new RuntimeException('Informe um WhatsApp brasileiro válido com DDD.');
        }

        return '55' . $digits;
    }

    private function safeImageUrl(string $url): string
    {
        return filter_var($url, FILTER_VALIDATE_URL) && str_starts_with($url, 'https://') ? $url : '';
    }
}
