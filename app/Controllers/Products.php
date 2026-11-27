<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Services\UploadedImage;
use Throwable;

class Products extends BaseController
{
    public function index()
    {
        return view('products/index', ['title' => 'Products', 'products' => (new ProductModel())->orderBy('is_active','DESC')->orderBy('id','DESC')->findAll()]);
    }

    public function new()
    {
        return view('products/form', ['title' => 'Add product', 'product' => null]);
    }

    public function create()
    {
        return $this->persist(null);
    }

    public function edit(int $id)
    {
        $product = (new ProductModel())->find($id);
        if (! $product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return view('products/form', ['title' => 'Edit product', 'product' => $product]);
    }

    public function update(int $id)
    {
        if (! (new ProductModel())->find($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        return $this->persist($id);
    }

    private function persist(?int $id)
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'price' => 'required|decimal',
            'stock_quantity' => 'required|is_natural|max_length[8]',
        ] + $this->optionalImageRule('image');
        if (! $this->validate($rules)) {
            return $this->rejectValidation();
        }

        $price = trim((string) $this->request->getPost('price'));
        $stock = (int) $this->request->getPost('stock_quantity');
        if (! preg_match('/^\d{1,7}(?:\.\d{1,2})?$/', $price) || (float) $price <= 0 || $stock > 1000000) {
            return redirect()->back()->withInput()->with('errors', ['price' => 'Price must be between ₱0.01 and ₱9,999,999.99; stock cannot exceed 1,000,000.']);
        }
        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'price' => number_format((float)$price, 2, '.', ''),
            'stock_quantity' => $stock,
            'is_active' => $this->request->getPost('is_active') === '0' ? 0 : 1,
        ];
        $model = new ProductModel();
        $old = $id ? $model->find($id) : null;
        $image = $this->request->getFile('image');
        $newImage = null;
        try {
            if ($image && $image->getError() !== UPLOAD_ERR_NO_FILE) {
                $newImage = (new UploadedImage())->prepare($image, 'products');
                $data['image'] = $newImage;
            }
            $saved = $id ? $model->update($id, $data) : $model->insert($data);
            if (! $saved) {
                throw new \RuntimeException('Database rejected the product.');
            }
        } catch (Throwable $e) {
            if ($newImage) (new UploadedImage())->remove($newImage);
            log_message('error', 'Product save failure: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Could not save the product. ' . ($image && $image->getError() !== UPLOAD_ERR_NO_FILE ? 'Check that PHP GD is enabled and the image is valid.' : 'Please check your fields.'));
        }
        if ($old && $newImage) (new UploadedImage())->remove($old['image']);
        return redirect()->to(site_url('products'))->with('success', $id ? 'Product updated.' : 'Product added.');
    }

    public function delete(int $id)
    {
        $model = new ProductModel();
        $product = $model->find($id);
        if (! $product) throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        $saleCount = db_connect()->table('sales')->where('product_id', $id)->countAllResults();
        if ($saleCount > 0) {
            $model->update($id, ['is_active' => 0]);
            return redirect()->to(site_url('products'))->with('success', 'Product archived to preserve sales history.');
        }
        $model->delete($id);
        (new UploadedImage())->remove($product['image']);
        return redirect()->to(site_url('products'))->with('success', 'Product deleted.');
    }
}
