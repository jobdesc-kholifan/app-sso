<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMasterUsers extends Migration
{
    public function up()
    {
        $this->db->query('CREATE SCHEMA IF NOT EXISTS master');
        $this->db->query('SET search_path TO master');

        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'user_password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'admin',
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users', true);

        $this->db->query('SET search_path TO public');
    }

    public function down()
    {
        $this->db->query('SET search_path TO master');
        $this->forge->dropTable('users', true);
        $this->db->query('SET search_path TO public');
    }
}
