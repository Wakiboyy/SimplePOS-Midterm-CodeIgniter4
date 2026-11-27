<?php
namespace App\Controllers;

use App\Models\StaffModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('staff_id')) {
            return redirect()->to(site_url('dashboard'));
        }
        return view('auth/login', ['title' => 'Sign in']);
    }

    public function attempt()
    {
        if (! $this->validate([
            'username' => 'required|max_length[50]',
            'password' => 'required',
        ])) {
            return $this->rejectValidation();
        }

        $user = (new StaffModel())->where('username', trim((string) $this->request->getPost('username')))->first();
        if (! $user || (int) $user['is_active'] !== 1
            || ! password_verify((string) $this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Incorrect username or password.');
        }
        session()->regenerate(true);
        session()->set([
            'staff_id' => (int) $user['id'],
            'staff_name' => $user['full_name'],
            'staff_username' => $user['username'],
        ]);
        return redirect()->to(site_url('dashboard'))->with('success', 'Welcome back, ' . $user['full_name'] . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'You have signed out.');
    }
}
