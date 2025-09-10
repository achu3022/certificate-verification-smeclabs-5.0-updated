<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AdminModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/login');
    }

    public function authenticate()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $admin = $this->adminModel->where('email', $email)
                                ->where('status', 'active')
                                ->first();

        if ($admin && password_verify($password, $admin['password'])) {
            $sessionData = [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'email' => $admin['email'],
                'role' => $admin['role'],
                'isLoggedIn' => true
            ];

            session()->set($sessionData);
            return redirect()->to('/admin/dashboard')->with('success', 'Welcome back!');
        }

        return redirect()->back()->with('error', 'Invalid credentials or account inactive');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login')->with('success', 'Successfully logged out');
    }
}
