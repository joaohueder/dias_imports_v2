<?php

namespace App\Controllers;

use App\Libraries\EvolutionApiService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use RuntimeException;

class Evolution extends BaseController
{
    protected $helpers = ['form', 'url'];

    public function saveSettings(): RedirectResponse
    {
        try {
            (new EvolutionApiService())->saveSettings(
                (string) $this->request->getPost('base_url'),
                (string) $this->request->getPost('api_key'),
            );
            return $this->back()->with('success', 'Credenciais da Evolution API salvas com segurança.');
        } catch (RuntimeException $exception) {
            return $this->back()->withInput()->with('error', $exception->getMessage());
        }
    }

    public function testConnection(): RedirectResponse
    {
        $service = new EvolutionApiService();
        try {
            $count = $service->testConnection();
            $service->recordTest('success');
            return $this->back()->with('success', "Conexão realizada. {$count} instância(s) encontrada(s).");
        } catch (RuntimeException $exception) {
            $service->recordTest('error');
            return $this->back()->with('error', $exception->getMessage());
        }
    }

    public function createInstance(): RedirectResponse
    {
        try {
            $isDefault = (new EvolutionApiService())->createInstance((string) $this->request->getPost('instance_name'));
            $message = $isDefault
                ? 'Primeira instância criada e definida como padrão. Use Conectar para obter o QR Code.'
                : 'Instância criada. Use Conectar para obter o QR Code.';

            return $this->back()->with('success', $message);
        } catch (RuntimeException $exception) {
            return $this->back()->with('error', $exception->getMessage());
        }
    }

    public function instanceStatuses(): ResponseInterface
    {
        try {
            $service = new EvolutionApiService();
            $settings = $service->getSettings();
            $instances = $service->fetchInstances();

            $statuses = array_map(static fn (array $instance): array => [
                'name' => $instance['name'],
                'state' => $instance['state'],
                'connected' => $instance['connected'],
            ], $instances);

            $html = view('admin/settings/_evolution_instances', [
                'evolutionInstances' => $instances,
                'evolutionSettings' => $settings,
            ]);

            helper('telemetry');
            $telemetry = get_footer_telemetry();

            return $this->response
                ->setHeader('Cache-Control', 'no-store')
                ->setJSON([
                    'instances' => $statuses,
                    'html' => $html,
                    'footerHtml' => $telemetry['html'],
                ]);
        } catch (RuntimeException $exception) {
            return $this->response
                ->setStatusCode(503)
                ->setHeader('Cache-Control', 'no-store')
                ->setJSON(['error' => $exception->getMessage()]);
        }
    }

    public function connectInstance(): RedirectResponse|ResponseInterface
    {
        $service = new EvolutionApiService();
        $instanceName = (string) $this->request->getPost('instance_name');

        try {
            $instance = $service->findInstance($instanceName);
            $forceDisconnect = ! $this->request->isAJAX()
                || $this->request->getPost('force_disconnect') === '1';

            if ($forceDisconnect) {
                $service->logoutInstance($instanceName);
            } elseif ($instance['connected'] ?? false) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'connected' => true,
                        'qrCode' => null,
                        'csrfHash' => csrf_hash(),
                    ]);
                }
                return $this->back()->with('success', 'A instância já está conectada.');
            }

            $qrCode = $this->extractQrCode($service->connectInstance($instanceName));
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'connected' => false,
                    'qrCode' => $qrCode,
                    'csrfHash' => csrf_hash(),
                ]);
            }

            $response = $this->back()->with('success', $qrCode === null ? 'Conexão solicitada. Atualize o status em instantes.' : 'Leia o QR Code com o WhatsApp.');
            return $qrCode === null ? $response : $response->with('evolution_qr', $qrCode);
        } catch (RuntimeException $exception) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'error' => $exception->getMessage(),
                    'csrfHash' => csrf_hash(),
                ]);
            }
            return $this->back()->with('error', $exception->getMessage());
        }
    }

    public function setDefaultInstance(): RedirectResponse
    {
        try {
            (new EvolutionApiService())->setDefaultInstance((string) $this->request->getPost('instance_name'));
            return $this->back()->with('success', 'Instância padrão atualizada.');
        } catch (RuntimeException $exception) {
            return $this->back()->with('error', $exception->getMessage());
        }
    }

    public function sendTestMessage(): RedirectResponse
    {
        try {
            (new EvolutionApiService())->sendTestMessage(
                (string) $this->request->getPost('instance_name'),
                (string) $this->request->getPost('whatsapp'),
            );
            return $this->back()->with('success', 'Mensagem de teste enviada com sucesso.');
        } catch (RuntimeException $exception) {
            return $this->back()->with('error', $exception->getMessage());
        }
    }

    public function logoutInstance(): RedirectResponse
    {
        try {
            (new EvolutionApiService())->logoutInstance((string) $this->request->getPost('instance_name'));
            return $this->back()->with('success', 'Instância desconectada do WhatsApp.');
        } catch (RuntimeException $exception) {
            return $this->back()->with('error', $exception->getMessage());
        }
    }

    public function deleteInstance(): RedirectResponse
    {
        try {
            (new EvolutionApiService())->deleteInstance((string) $this->request->getPost('instance_name'));
            return $this->back()->with('success', 'Instância excluída da Evolution API.');
        } catch (RuntimeException $exception) {
            return $this->back()->with('error', $exception->getMessage());
        }
    }

    private function back(): RedirectResponse
    {
        return redirect()->to('/configuracoes?tab=evolution');
    }

    private function extractQrCode(array $payload): ?string
    {
        $value = $payload['base64'] ?? $payload['qrcode']['base64'] ?? null;
        if (! is_string($value) || strlen($value) > 500000) {
            return null;
        }
        if (preg_match('#^data:image/(?:png|jpeg);base64,[A-Za-z0-9+/=]+$#', $value)) {
            return $value;
        }
        if (preg_match('#^[A-Za-z0-9+/=]+$#', $value)) {
            return 'data:image/png;base64,' . $value;
        }
        return null;
    }
}
