<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemJobQueueModel extends Model
{
    protected $table            = 'system_job_queue';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'job_key',
        'item_reference',
        'payload',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'attempts',
        'error_message',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $dateFormat       = 'datetime';

    public function getQueueStats(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $counts = $builder->select('status, COUNT(*) as total')
                          ->groupBy('status')
                          ->get()
                          ->getResultArray();

        $stats = [
            'pending'    => 0,
            'processing' => 0,
            'completed'  => 0,
            'failed'     => 0,
            'total'      => 0,
        ];

        foreach ($counts as $row) {
            $st = $row['status'];
            $tot = (int) $row['total'];
            if (isset($stats[$st])) {
                $stats[$st] = $tot;
            }
            $stats['total'] += $tot;
        }

        return $stats;
    }
}
