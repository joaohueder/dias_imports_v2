<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyWhatsappModel extends Model
{
    protected $table = 'company_whatsapps';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'phone',
        'is_default',
        'is_active',
    ];
}
