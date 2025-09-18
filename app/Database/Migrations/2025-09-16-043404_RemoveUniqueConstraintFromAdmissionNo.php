<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUniqueConstraintFromAdmissionNo extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('certificates', [
            'admission_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'unique' => false
            ]
        ]);
    }

    public function down()
    {
        // Note: This will fail if there are duplicate admission numbers
        $this->forge->modifyColumn('certificates', [
            'admission_no' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'unique' => true
            ]
        ]);
    }
}
