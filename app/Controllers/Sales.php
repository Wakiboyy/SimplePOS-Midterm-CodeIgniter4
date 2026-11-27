<?php
namespace App\Controllers;

use App\Models\SaleModel;
use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Services\SaleRecorder;
use DomainException;
use Throwable;

class Sales extends BaseController
{
    public function index()
    {
        return view('sales/index', ['title' => 'Sales history', 'sales' => (new SaleModel())->withDetails()]);
    }
    public function new()
    {
        return view('sales/form', [
            'title' => 'Record sale',
            'products' => (new ProductModel())->where('is_active', 1)->where('stock_quantity >', 0)->orderBy('name')->findAll(),
            'customers' => (new CustomerModel())->orderBy('full_name')->findAll(),
        ]);
    }
    public function create()
    {
        if (! $this->validate([
            'product_id' => 'required|is_natural_no_zero',
            'quantity' => 'required|is_natural_no_zero',
            'customer_id' => 'permit_empty|is_natural_no_zero',
        ])) return $this->rejectValidation();

        $quantity = (int) $this->request->getPost('quantity');
        $customer = trim((string)$this->request->getPost('customer_id'));
        try {
            $id = (new SaleRecorder(db_connect()))->record(
                (int)$this->request->getPost('product_id'),
                $customer === '' ? null : (int)$customer,
                (int)session()->get('staff_id'),
                $quantity,
            );
            return redirect()->to(site_url('sales/' . $id))->with('success', 'Sale recorded. Inventory updated.');
        } catch (DomainException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            log_message('error', 'Sale transaction error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Could not record the sale. Please retry.');
        }
    }
    public function show(int $id)
    {
        $sale = (new SaleModel())->oneWithDetails($id);
        if (! $sale) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        return view('sales/show', ['title' => 'Sale #' . $id, 'sale' => $sale]);
    }
}
