<?php

namespace App\Models;

use CodeIgniter\Model;

class EvolutionSettingModel extends Model
{
    protected $table = 'evolution_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'base_url',
        'api_key_encrypted',
        'min_delay_seconds',
        'max_delay_seconds',
        'default_instance_name',
        'last_test_status',
        'last_tested_at',
    ];
}
