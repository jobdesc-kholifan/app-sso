<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OauthClientSeeder extends Seeder
{
    public function run()
    {
        $this->db->query('SET search_path TO oauth');

        $data = [
            'client_identifier' => 'testclient',
            'client_secret'     => password_hash('testsecret', PASSWORD_BCRYPT),
            'name'              => 'Test Client App',
            'redirect_uri'      => 'http://localhost:8080/callback',
            'is_confidential'   => 1,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('clients')->where('client_identifier', 'testclient')->get()->getRow();
        if (!$existing) {
            $this->db->table('clients')->insert($data);
        }

        $this->db->query('SET search_path TO public');
    }
}
