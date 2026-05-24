<?php

namespace App\Controllers;

class OAuthSessionController extends BaseController
{
    public function index()
    {
        return view('master/sessions/v_sessions_index');
    }

    public function datatable()
    {
        $db = db_connect();

        $builder = $db->table('oauth.access_tokens at')
            ->select('at.id, at.client_identifier, at.user_id, at.scopes, at.revoked, at.expires_at, at.created_at, u.full_name, u.username, c.name AS client_name')
            ->join('master.users u', 'u.id = at.user_id', 'left')
            ->join('oauth.clients c', 'c.client_identifier = at.client_identifier', 'left')
            ->orderBy('at.created_at', 'DESC');

        // Manual DataTable handling since we need custom SQL
        $draw   = (int) $this->request->getGet('draw');
        $start  = (int) $this->request->getGet('start');
        $length = (int) $this->request->getGet('length');
        $search = $this->request->getGet('search')['value'] ?? '';

        $totalRecords = $db->table('oauth.access_tokens')->countAll();

        if ($search) {
            $builder->groupStart()
                ->like('u.full_name', $search)
                ->orLike('u.username', $search)
                ->orLike('c.name', $search)
                ->orLike('at.client_identifier', $search)
                ->groupEnd();
        }

        $filteredCount = $builder->countAllResults(false);
        $rows = $builder->limit($length, $start)->get()->getResultArray();

        $now = new \DateTime();

        $data = array_map(function ($row) use ($now) {
            // Status: active, expired, atau revoked
            $expiresAt = new \DateTime($row['expires_at']);
            $isExpired = $now > $expiresAt;

            if ($row['revoked'] == 1) {
                $statusBadge = '<span class="badge bg-rose-100 text-rose-700">Revoked</span>';
            } elseif ($isExpired) {
                $statusBadge = '<span class="badge bg-amber-100 text-amber-700">Expired</span>';
            } else {
                $statusBadge = '<span class="badge bg-emerald-100 text-emerald-700">Active</span>';
            }

            $scopes = implode(', ', (array) json_decode($row['scopes'] ?? '[]'));
            if (!$scopes) {
                $scopes = $row['scopes'] ?? '-';
            }

            return [
                'full_name'       => htmlspecialchars($row['full_name'] ?? 'Unknown'),
                'username'        => htmlspecialchars($row['username'] ?? '-'),
                'client_name'     => htmlspecialchars($row['client_name'] ?? $row['client_identifier']),
                'scopes'          => $scopes,
                'status'          => $statusBadge,
                'expires_at'      => $row['expires_at'],
                'created_at'      => $row['created_at'],
                'action'          => '<button class="btn btn-sm btn-danger revoke-btn" data-id="' . $row['id'] . '" ' . ($row['revoked'] == 1 ? 'disabled' : '') . '>Revoke</button>',
            ];
        }, $rows);

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredCount,
            'data'            => $data,
        ]);
    }

    public function revoke($id)
    {
        $db = db_connect();
        $db->query('SET search_path TO oauth');
        $result = $db->table('access_tokens')
            ->where('id', $id)
            ->update(['revoked' => 1]);
        $db->query('SET search_path TO public');

        if ($result) {
            return $this->response->setJSON(['success' => true, 'message' => 'Token berhasil direvoke.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal merevoke token.']);
    }
}
