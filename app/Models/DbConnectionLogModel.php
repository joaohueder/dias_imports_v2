<?php

namespace App\Models;

use CodeIgniter\Model;

class DbConnectionLogModel extends Model
{
    protected $table = 'db_connection_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['created_at'];
    public $timestamps = false;

    /**
     * Registra uma nova conexão no histórico (com throttle de segurança).
     */
    public function logConnection(): void
    {
        try {
            $now = date('Y-m-d H:i:s');
            $this->insert(['created_at' => $now]);

            // Limpa registros mais antigos que 2 horas esporadicamente (1 em cada 20 requisições)
            if (random_int(1, 20) === 1) {
                $cutoff = date('Y-m-d H:i:s', strtotime('-2 hours'));
                $this->where('created_at <', $cutoff)->delete();
            }
        } catch (\Throwable $e) {
            // Ignora falhas de log para não interromper a execução
        }
    }

    /**
     * Retorna o total de conexões registradas na última hora (60 minutos).
     */
    public function getConnectionsLastHour(): int
    {
        try {
            $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
            return (int) $this->where('created_at >=', $oneHourAgo)->countAllResults();
        } catch (\Throwable $e) {
            return 0;
        }
    }
}
