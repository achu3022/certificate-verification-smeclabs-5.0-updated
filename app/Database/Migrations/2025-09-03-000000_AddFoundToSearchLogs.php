<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFoundToSearchLogs extends Migration
{
    public function up()
    {
        $fields = [
            'found' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'user_agent'
            ]
        ];
        $this->forge->addColumn('search_logs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('search_logs', 'found');
    }
}
