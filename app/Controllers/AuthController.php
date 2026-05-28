<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use Config\Services;

class AuthController extends BaseController
{
    public function login()
    {
        $redirect = $this->request->getGet('redirect');
        if ($redirect) {
            session()->set('redirect_after_login', $redirect);
        }

        // If already logged in, redirect to dashboard or master user
        if (session()->get('isLoggedIn')) {
            $redirectUrl = session()->get('redirect_after_login') ?? '/master/users';
            session()->remove('redirect_after_login');
            return redirect()->to($redirectUrl);
        }

        return view('auth/v_login');
    }

    public function process()
    {
        $throttler = Services::throttler();
        $isAjax = $this->request->isAJAX();

        // Rate Limiter: 5 attempts per minute based on IP address
        if ($throttler->check($this->request->getIPAddress(), 5, MINUTE) === false) {
            $msg = 'Too many login attempts. Please try again later.';
            return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->with('error', $msg);
        }

        $rules = [
            'email'    => 'required|min_length[3]', // Email or username
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            $msg = 'Please fill all required fields correctly.';
            return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->with('error', $msg)->withInput();
        }

        $username = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        // Check by username (although field is named email in form)
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            if ((int) $user->status !== 1) {
                $msg = 'Your account is inactive.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->with('error', $msg);
            }

            if (password_verify($password, $user->user_password)) {
                $sessionData = [
                    'user_id'   => $user->id,
                    'username'  => $user->username,
                    'full_name' => $user->full_name,
                    'role'      => $user->role,
                    'isLoggedIn' => true
                ];

                // Update last login
                $userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);

                session()->set($sessionData);
                $redirectUrl = session()->get('redirect_after_login') ?? base_url('/');
                session()->remove('redirect_after_login');

                return $isAjax ? $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Welcome back, ' . $user->full_name,
                    'redirect' => $redirectUrl
                ]) : redirect()->to($redirectUrl)->with('success', 'Welcome back, ' . $user->full_name);
            } else {
                $msg = 'Invalid password.';
                return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->with('error', $msg);
            }
        } else {
            $msg = 'User not found.';
            return $isAjax ? $this->response->setJSON(['status' => 'error', 'message' => $msg]) : redirect()->back()->with('error', $msg);
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out.');
    }
}
