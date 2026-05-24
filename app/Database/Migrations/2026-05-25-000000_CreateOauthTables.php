<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOauthTables extends Migration
{
    public function up()
    {
        // Ensure the schema exists
        $this->db->query('CREATE SCHEMA IF NOT EXISTS oauth');
        $this->db->query('SET search_path TO oauth');

        // 1. oauth.clients
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'client_identifier' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'client_secret' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'redirect_uri' => [
                'type'       => 'TEXT',
            ],
            'is_confidential' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('clients', true);

        // 2. oauth.access_tokens
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'client_identifier' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'scopes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'revoked' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'expires_at' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('access_tokens', true);

        // 3. oauth.auth_codes
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'client_identifier' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'scopes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'revoked' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'expires_at' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('auth_codes', true);

        // 4. oauth.refresh_tokens
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'access_token_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'revoked' => [
                'type'       => 'INT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'expires_at' => [
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('refresh_tokens', true);

        // Reset search path
        $this->db->query('SET search_path TO public');
    }

    public function down()
    {
        $this->db->query('SET search_path TO oauth');
        $this->forge->dropTable('refresh_tokens', true);
        $this->forge->dropTable('auth_codes', true);
        $this->forge->dropTable('access_tokens', true);
        $this->forge->dropTable('clients', true);
        $this->db->query('DROP SCHEMA IF EXISTS oauth');
        $this->db->query('SET search_path TO public');
    }
}
