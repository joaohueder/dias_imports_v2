<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'price',
        'promotional_price',
        'active',
        'whatsapp_number',
        'meta_ads_active',
        'layout',
        'color_palette',
        'bg_animation',
        'btn_animation',
        'headline',
        'subheadline',
        'button_text',
        'cta_icon',
        'badge_text',
        'urgency_text',
        'benefits',
        'shipping_info',
        'payment_info',
        'guarantee_info',
        'about_title',
        'about_content',
        'about_cta_btn',
        'faq',
        'checkout_title',
        'checkout_subtitle',
        'slug'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'int',
        'price' => 'float',
        'promotional_price' => '?float',
        'active' => 'boolean',
        'meta_ads_active' => 'boolean',
        'benefits' => '?json-array',
        'faq' => '?json-array'
    ];

    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|max_length[255]',
        'price' => 'required|numeric',
        'slug' => 'required|max_length[255]|is_unique[products.slug,id,{id}]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
