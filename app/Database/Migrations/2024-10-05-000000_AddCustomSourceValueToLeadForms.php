<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomSourceValueToLeadForms extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('custom_source_value', 'lead_forms')) {
            $fields = [
                'custom_source_value' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'custom_fields',
                ],
            ];

            $this->forge->addColumn('lead_forms', $fields);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('custom_source_value', 'lead_forms')) {
            $this->forge->dropColumn('lead_forms', 'custom_source_value');
        }
    }
}
