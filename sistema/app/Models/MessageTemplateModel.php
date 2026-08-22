<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageTemplateModel extends Model
{
    protected $table = 'message_templates';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'name',
        'content',
        'send_count',
        'is_active',
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'name' => 'required|max_length[120]',
        'content' => 'required',
        'is_active' => 'in_list[0,1]',
    ];
}
