<?php

namespace App\Models;

use CodeIgniter\Model;

class LandingLeadSettingModel extends Model
{
    protected $table = 'landing_lead_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'template_model',
        'color_palette',
        'bg_animation',
        'btn_animation',
        'seo_title',
        'seo_description',
        'headline',
        'subheadline',
        'badge_text',
        'button_text',
        'button_subtext',
        'whatsapp_group_link',
        'card1_icon',
        'card1_title',
        'card1_desc',
        'card2_icon',
        'card2_title',
        'card2_desc',
        'card3_icon',
        'card3_title',
        'card3_desc',
        'modal_title',
        'modal_desc',
        'modal_button_text',
    ];
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
