<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyProfileModel extends Model
{
    protected $table = 'company_profile';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'address',
        'number',
        'district',
        'city',
        'state',
        'public_url',
    ];
}
