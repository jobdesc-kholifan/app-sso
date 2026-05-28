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
            'name'              => 'Test Client App (Confidential)',
            'redirect_uri'      => 'http://localhost:8080/callback',
            'is_confidential'   => 1,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('clients')->where('client_identifier', 'testclient')->get()->getRow();
        if (!$existing) {
            $this->db->table('clients')->insert($data);
        }

        $spaData = [
            'client_identifier' => 'testspa',
            'client_secret'     => password_hash('spasecret', PASSWORD_BCRYPT), // dummy secret to satisfy not-null
            'name'              => 'Test SPA App (Public PKCE)',
            'redirect_uri'      => 'http://localhost:8080/callback-spa',
            'is_confidential'   => 0, // 0 = public
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];

        $existingSpa = $this->db->table('clients')->where('client_identifier', 'testspa')->get()->getRow();
        if (!$existingSpa) {
            $this->db->table('clients')->insert($spaData);
        }

        $this->db->query('SET search_path TO public');
    }
}
