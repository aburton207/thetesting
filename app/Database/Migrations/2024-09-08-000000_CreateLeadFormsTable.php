<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadFormsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'owner_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'lead_source_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
            ],
            'labels' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'custom_fields' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'deleted' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('lead_forms', true);
    }

    public function down()
    {
        $this->forge->dropTable('lead_forms', true);
    }
}

