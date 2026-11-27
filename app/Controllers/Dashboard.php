<?php
namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CustomerModel;
use App\Models\SaleModel;
use App\Models\StaffModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $totals = $db->table('sales')->select('COUNT(*) AS sale_count, COALESCE(SUM(total_price),0) AS revenue')->get()->getRowArray();
        $recent = (new SaleModel())->withDetails(5);

        return view('dashboard/index', [
            'title' => 'Overview',
            'products' => (new ProductModel())->where('is_active', 1)->countAllResults(),
            'customers' => (new CustomerModel())->countAllResults(),
            'staff' => (new StaffModel())->where('is_active', 1)->countAllResults(),
            'sales' => (int) $totals['sale_count'],
            'revenue' => $totals['revenue'],
            'lowStock' => (new ProductModel())->where('is_active', 1)->where('stock_quantity <=', 5)->orderBy('stock_quantity','ASC')->findAll(),
            'recent' => $recent,
        ]);
    }
}
