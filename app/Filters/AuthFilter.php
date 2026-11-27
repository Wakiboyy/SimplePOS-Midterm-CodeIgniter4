<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\StaffModel;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $id = (int) session()->get('staff_id');
        if ($id < 1) {
            return redirect()->to(site_url('login'))->with('error', 'Please sign in to continue.');
        }

        // Reject disabled/deleted users even if their browser still has an old session.
        $staff = (new StaffModel())->select('id, is_active')->find($id);
        if ($staff === null || (int) $staff['is_active'] !== 1) {
            session()->destroy();
            return redirect()->to(site_url('login'))->with('error', 'Your session is no longer active.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
