<?php
namespace App\Controllers;

use App\Models\CustomerModel;
use Throwable;

class Customers extends BaseController
{
    public function index()
    {
        return view('customers/index', ['title' => 'Customers', 'customers' => (new CustomerModel())->orderBy('id','DESC')->findAll()]);
    }
    public function new()
    {
        return view('customers/form', ['title' => 'Add customer', 'customer' => null]);
    }
    public function create()
    {
        return $this->persist(null);
    }
    public function edit(int $id)
    {
        $row = (new CustomerModel())->find($id);
        if (! $row) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return view('customers/form', ['title' => 'Edit customer', 'customer' => $row]);
    }
    public function update(int $id)
    {
        if (! (new CustomerModel())->find($id)) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return $this->persist($id);
    }
    private function persist(?int $id)
    {
        if (! $this->validate([
            'full_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
        ])) return $this->rejectValidation();

        $data = [
            'full_name' => trim((string)$this->request->getPost('full_name')),
            'email' => trim((string)$this->request->getPost('email')),
            'phone' => trim((string)$this->request->getPost('phone')) ?: null,
        ];
        try {
            $model = new CustomerModel();
            $saved = $id ? $model->update($id, $data) : $model->insert($data);
            if (! $saved) {
                throw new \RuntimeException('Database rejected the customer.');
            }
        } catch (Throwable $e) {
            log_message('error', 'Customer save failure: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Could not save the customer.');
        }
        return redirect()->to(site_url('customers'))->with('success', $id ? 'Customer updated.' : 'Customer added.');
    }
    public function delete(int $id)
    {
        $model = new CustomerModel();
        if (! $model->find($id)) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        // Foreign key ON DELETE SET NULL keeps past transactions as guest purchases.
        $model->delete($id);
        return redirect()->to(site_url('customers'))->with('success', 'Customer deleted; their past sales remain in history.');
    }
}
