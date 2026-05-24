<?php

namespace App\Controllers;

use App\Models\UserModel;
use Hermawan\DataTables\DataTable;

class UserController extends BaseController
{
    public function index()
    {
        return view('master/users/v_user_index');
    }

    public function datatable()
    {
        $db = db_connect();

        // To ensure DataTables works correctly, we should query with the search path set, 
        // or we use the builder with the exact table. Hermawan datatables handles it via the builder.
        $builder = $db->table('master.users')->select('id, full_name, username, role, status, last_login');

        return DataTable::of($builder)
            ->add('action', function ($row) {
                return '<button class="btn btn-sm btn-primary edit-btn" data-id="' . $row->id . '">Edit</button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="' . $row->id . '">Delete</button>';
            })
            ->format('status', function ($value) {
                return $value == 1 ? '<span class="badge bg-emerald-100 text-emerald-700">Active</span>' : '<span class="badge bg-rose-100 text-rose-700">Inactive</span>';
            })
            ->toJson(true);
    }

    public function store()
    {
        $model = new UserModel();

        $data = [
            'full_name'     => $this->request->getPost('full_name'),
            'username'      => $this->request->getPost('username'),
            'user_password' => $this->request->getPost('user_password'), // the model handles hashing
            'role'          => $this->request->getPost('role'),
            'status'        => $this->request->getPost('status') ?? 1,
        ];

        if ($model->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User created successfully']);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $model->errors()]);
    }

    public function edit($id)
    {
        $model = new UserModel();
        $user = $model->find($id);

        if ($user) {
            return $this->response->setJSON(['success' => true, 'data' => $user]);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
    }

    public function update($id)
    {
        $model = new UserModel();

        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'username'  => $this->request->getPost('username'),
            'role'      => $this->request->getPost('role'),
            'status'    => $this->request->getPost('status'),
        ];

        if ($this->request->getPost('user_password')) {
            $data['user_password'] = $this->request->getPost('user_password');
        }

        if ($model->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'errors' => $model->errors()]);
    }

    public function delete($id)
    {
        $model = new UserModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User deleted successfully']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user']);
    }
}
