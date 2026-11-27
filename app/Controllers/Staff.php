<?php
namespace App\Controllers;

use App\Models\StaffModel;
use App\Services\UploadedImage;
use Throwable;

class Staff extends BaseController
{
    public function index()
    {
        return view('staff/index', ['title' => 'Staff', 'staff' => (new StaffModel())->orderBy('is_active','DESC')->orderBy('id','DESC')->findAll()]);
    }
    public function new()
    {
        return view('staff/form', ['title' => 'Add staff', 'staffMember' => null]);
    }
    public function create()
    {
        return $this->persist(null);
    }
    public function edit(int $id)
    {
        $row = (new StaffModel())->find($id);
        if (! $row) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return view('staff/form', ['title' => 'Edit staff', 'staffMember' => $row]);
    }
    public function update(int $id)
    {
        if (! (new StaffModel())->find($id)) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return $this->persist($id);
    }
    private function persist(?int $id)
    {
        $password = (string)$this->request->getPost('password');
        $rules = [
            'username' => 'required|alpha_dash|min_length[3]|max_length[50]',
            'full_name' => 'required|min_length[2]|max_length[100]',
        ] + $this->optionalImageRule('avatar');
        if (! $id || $password !== '') {
            $rules['password'] = 'required|min_length[10]|max_length[72]';
            $rules['password_confirm'] = 'required|matches[password]';
        }
        if (! $this->validate($rules)) return $this->rejectValidation();
        $model = new StaffModel();
        $username = trim((string)$this->request->getPost('username'));
        $check = $model->where('username', $username);
        if ($id) $check = $check->where('id !=', $id);
        if ($check->first()) {
            return redirect()->back()->withInput()->with('errors', ['username' => 'This username is already in use.']);
        }
        $old = $id ? $model->find($id) : null;
        $active = $this->request->getPost('is_active') === '0' ? 0 : 1;
        if ($id && (int)session()->get('staff_id') === $id && $active === 0) {
            return redirect()->back()->withInput()->with('error', 'You cannot disable your own account.');
        }
        if ($id && $old && (int)$old['is_active'] === 1 && $active === 0
            && (new StaffModel())->where('is_active', 1)->countAllResults() <= 1) {
            return redirect()->back()->withInput()->with('error', 'The last active staff account cannot be disabled.');
        }
        $data = ['username'=>$username,'full_name'=>trim((string)$this->request->getPost('full_name')),'is_active'=>$active];
        if ($password !== '') $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $avatar = $this->request->getFile('avatar');
        $newAvatar = null;
        try {
            if ($avatar && $avatar->getError() !== UPLOAD_ERR_NO_FILE) {
                $newAvatar = (new UploadedImage())->prepare($avatar, 'avatars');
                $data['avatar'] = $newAvatar;
            }
            $saved = $id ? $model->update($id, $data) : $model->insert($data);
            if (! $saved) {
                throw new \RuntimeException('Database rejected the staff member.');
            }
        } catch (Throwable $e) {
            if ($newAvatar) (new UploadedImage())->remove($newAvatar);
            log_message('error', 'Staff save failure: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Could not save staff. Check the image file and whether PHP GD is enabled.');
        }
        if ($old && $newAvatar) (new UploadedImage())->remove($old['avatar']);
        if ($id && (int)session()->get('staff_id') === $id) session()->set('staff_name', $data['full_name']);
        return redirect()->to(site_url('staff'))->with('success', $id ? 'Staff account updated.' : 'Staff account created.');
    }
    public function delete(int $id)
    {
        $model = new StaffModel();
        $user = $model->find($id);
        if (! $user) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        if ((int) session()->get('staff_id') === $id) {
            return redirect()->to(site_url('staff'))->with('error', 'You cannot delete your own account.');
        }
        if ((int)$user['is_active'] === 1 && (new StaffModel())->where('is_active', 1)->countAllResults() <= 1) {
            return redirect()->to(site_url('staff'))->with('error', 'The last active staff account cannot be deleted.');
        }
        if (db_connect()->table('sales')->where('sold_by',$id)->countAllResults() > 0) {
            $model->update($id, ['is_active' => 0]);
            return redirect()->to(site_url('staff'))->with('success', 'Staff account archived to preserve sales history.');
        }
        $model->delete($id);
        (new UploadedImage())->remove($user['avatar']);
        return redirect()->to(site_url('staff'))->with('success', 'Staff account deleted.');
    }
}
