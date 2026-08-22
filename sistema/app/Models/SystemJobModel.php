<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemJobModel extends Model
{
    protected $table            = 'system_jobs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'job_key',
        'name',
        'description',
        'is_active',
        'min_delay_seconds',
        'max_delay_seconds',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $dateFormat       = 'datetime';

    public function getByKey(string $jobKey): ?array
    {
        return $this->where('job_key', $jobKey)->first();
    }
}
