<?php

namespace App\Controllers;

use App\Libraries\MetaAdsService;
use CodeIgniter\HTTP\RedirectResponse;
use RuntimeException;
use Throwable;

class MetaAds extends BaseController
{
    public function saveSettings(): RedirectResponse
    {
        $pixelId = trim((string) $this->request->getPost('pixel_id'));
        $accessToken = trim((string) $this->request->getPost('access_token'));
        $testEventCode = trim((string) $this->request->getPost('test_event_code'));

        $service = new MetaAdsService();

        try {
            $service->saveSettings($pixelId, $accessToken, $testEventCode);
            return redirect()->to('/configuracoes?tab=meta-ads')->with('success', 'Configurações do Meta Ads salvas com sucesso.');
        } catch (RuntimeException $exception) {
            return redirect()->to('/configuracoes?tab=meta-ads')->withInput()->with('error', $exception->getMessage());
        } catch (Throwable) {
            return redirect()->to('/configuracoes?tab=meta-ads')->withInput()->with('error', 'Não foi possível salvar as configurações do Meta Ads.');
        }
    }

    public function testConnection(): RedirectResponse
    {
        $service = new MetaAdsService();

        if (! $service->isEncryptionReady()) {
            return redirect()->to('/configuracoes?tab=meta-ads')->with('error', 'Configure encryption.key no ambiente antes de testar.');
        }

        try {
            $result = $service->testConnection();
            $msg = 'Teste da API do Meta Ads realizado com sucesso! Eventos recebidos: ' . ($result['events_received'] ?? 1);
            return redirect()->to('/configuracoes?tab=meta-ads')->with('success', $msg);
        } catch (RuntimeException $exception) {
            return redirect()->to('/configuracoes?tab=meta-ads')->with('error', $exception->getMessage());
        } catch (Throwable) {
            return redirect()->to('/configuracoes?tab=meta-ads')->with('error', 'Não foi possível conectar à API do Meta Ads.');
        }
    }
}
