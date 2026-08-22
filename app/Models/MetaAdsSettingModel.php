<?php

namespace App\Models;

use CodeIgniter\Model;

class MetaAdsSettingModel extends Model
{
    protected $table = 'meta_ads_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'pixel_id',
        'access_token_encrypted',
        'test_event_code',
        'last_test_status',
        'last_tested_at',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
