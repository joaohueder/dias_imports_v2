<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCtaIconDirectToProducts extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('cta_icon', 'products')) {
            $this->db->query("ALTER TABLE `products` ADD COLUMN `cta_icon` VARCHAR(50) NULL DEFAULT 'ti-brand-whatsapp' AFTER `button_text`");
        }
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `products` DROP COLUMN `cta_icon`");
    }
}
