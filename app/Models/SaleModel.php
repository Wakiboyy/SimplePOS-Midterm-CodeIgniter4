<?php
namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sales';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['product_id', 'customer_id', 'sold_by', 'quantity', 'total_price'];
    protected $useTimestamps = false;

    private function detailsQuery(): BaseBuilder
    {
        return $this->db->table('sales s')
            ->select('s.*, p.name AS product_name, c.full_name AS customer_name, u.full_name AS staff_name')
            ->join('products p', 'p.id = s.product_id')
            ->join('customers c', 'c.id = s.customer_id', 'left')
            ->join('users u', 'u.id = s.sold_by');
    }

    public function withDetails(?int $limit = null): array
    {
        $builder = $this->detailsQuery()->orderBy('s.created_at', 'DESC')->orderBy('s.id', 'DESC');
        if ($limit !== null) $builder->limit($limit);
        return $builder->get()->getResultArray();
    }

    public function oneWithDetails(int $id): ?array
    {
        return $this->detailsQuery()->where('s.id', $id)->get()->getRowArray();
    }
}
