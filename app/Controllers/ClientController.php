<?php

namespace App\Controllers;

use App\Models\ClientModel;
use Hermawan\DataTables\DataTable;

class ClientController extends BaseController
{
    public function index()
    {
        return view('master/clients/v_client_index');
    }

    public function datatable()
    {
        $db = db_connect();

        $builder = $db->table('oauth.clients')->select('id, client_identifier, name, redirect_uri, is_confidential');

        return DataTable::of($builder)
            ->add('action', function ($row) {
                return '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
            })
            ->format('is_confidential', function ($value) {
                return $value == 1 ? '<span class="badge bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300">Confidential</span>' : '<span class="badge bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300">Public</span>';
            })
            ->toJson(true);
    }

    public function store()
    {
        $model = new ClientModel();

        // Unique validation
        $clientIdentifier = $this->request->getPost('client_identifier');
        $existing = $model->where('client_identifier', $clientIdentifier)->first();
        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['client_identifier' => 'Client Identifier must be unique.']
            ]);
        }

        $data = [
            'client_identifier' => $clientIdentifier,
            'client_secret'     => password_hash($this->request->getPost('client_secret'), PASSWORD_BCRYPT),
            'name'              => $this->request->getPost('name'),
            'redirect_uri'      => $this->request->getPost('redirect_uri'),
            'is_confidential'   => $this->request->getPost('is_confidential') ?? 1,
        ];

        if ($model->insert($data)) {
            \Config\Services::cache()->delete('oauth_allowed_origins');
            return $this->response->setJSON(['success' => true, 'message' => 'Client created successfully']);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $model->errors()]);
    }

    public function edit($id)
    {
        $model = new ClientModel();
        $client = $model->find($id);

        if ($client) {
            // Do not send password/secret back for security, but allow it to be updated if field is provided
            unset($client->client_secret);
            return $this->response->setJSON(['success' => true, 'data' => $client]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Client not found']);
    }

    public function update($id)
    {
        $model = new ClientModel();

        // Unique validation excluding current client
        $clientIdentifier = $this->request->getPost('client_identifier');
        $existing = $model->where('client_identifier', $clientIdentifier)->where('id !=', $id)->first();
        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['client_identifier' => 'Client Identifier must be unique.']
            ]);
        }

        $data = [
            'client_identifier' => $clientIdentifier,
            'name'              => $this->request->getPost('name'),
            'redirect_uri'      => $this->request->getPost('redirect_uri'),
            'is_confidential'   => $this->request->getPost('is_confidential'),
        ];

        if ($this->request->getPost('client_secret')) {
            $data['client_secret'] = password_hash($this->request->getPost('client_secret'), PASSWORD_BCRYPT);
        }

        if ($model->update($id, $data)) {
            \Config\Services::cache()->delete('oauth_allowed_origins');
            return $this->response->setJSON(['success' => true, 'message' => 'Client updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $model->errors()]);
    }

    public function delete($id)
    {
        $model = new ClientModel();
        if ($model->delete($id)) {
            \Config\Services::cache()->delete('oauth_allowed_origins');
            return $this->response->setJSON(['success' => true, 'message' => 'Client deleted successfully']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete client']);
    }
}
