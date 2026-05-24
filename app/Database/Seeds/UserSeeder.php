<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET search_path TO master');

        $data = [
            'full_name'     => 'Super Administrator',
            'username'      => 'superadmin',
            'user_password' => password_hash('superadmin123', PASSWORD_ARGON2ID),
            'role'          => 'superadmin',
            'status'        => 1,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        // Ensure we don't duplicate
        $existing = $this->db->table('users')->where('username', 'superadmin')->get()->getRow();
        if (!$existing) {
            $this->db->table('users')->insert($data);
        }

        $this->db->query('SET search_path TO public');
    }
}
