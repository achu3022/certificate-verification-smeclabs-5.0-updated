<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSearchLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'search_term' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'ip_address' => [
                'type'       => 'VARCHAR',
                'constraint' => 45,
            ],
            'user_agent' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('search_logs');
    }

    public function down()
    {
        $this->forge->dropTable('search_logs');
    }
}
