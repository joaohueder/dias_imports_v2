<?php

namespace App\Models;

use CodeIgniter\Model;

class RealtimeScreenSettingModel extends Model
{
    protected $table = 'realtime_screen_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'screen_key',
        'screen_name',
        'screen_description',
        'route_path',
        'refresh_interval_seconds',
        'is_active',
    ];

    /**
     * Retorna a configuração de uma tela pela chave
     */
    public function getByKey(string $key): ?array
    {
        return $this->where('screen_key', $key)->first();
    }

    /**
     * Verifica se a atualização em tempo real está ativa para uma determinada tela
     */
    public function isScreenActive(string $key): bool
    {
        $screen = $this->getByKey($key);
        return !empty($screen) && (int)$screen['is_active'] === 1;
    }

    /**
     * Retorna o intervalo padrão unificado em segundos (baseado no sleep do worker)
     */
    public function getInterval(string $key = ''): int
    {
        try {
            $appSetting = (new \App\Models\AppSettingModel())->where('setting_key', 'realtime_sleep_seconds')->first();
            if ($appSetting && !empty($appSetting['setting_value'])) {
                $val = (int)$appSetting['setting_value'];
                if ($val > 0) {
                    return $val;
                }
            }
        } catch (\Throwable) {}

        return 5;
    }
}
