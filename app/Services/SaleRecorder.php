<?php
namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use DomainException;
use RuntimeException;
use Throwable;

class SaleRecorder
{
    public function __construct(private BaseConnection $db)
    {
    }

    /**
     * Atomic, row-locked, single-product sale. InnoDB is required.
     * Never rely on a browser-supplied price or on an earlier stock count.
     */
    public function record(int $productId, ?int $customerId, int $staffId, int $quantity): int
    {
        if ($productId < 1 || $staffId < 1 || $quantity < 1 || $quantity > 100000) {
            throw new DomainException('Choose a product and a valid positive quantity.');
        }

        $this->db->transBegin();
        try {
            $product = $this->db->query('SELECT id, price, stock_quantity, is_active FROM products WHERE id = ? FOR UPDATE', [$productId])->getRowArray();
            if (! $product || (int) $product['is_active'] !== 1) {
                throw new DomainException('This product is unavailable.');
            }
            if ((int) $product['stock_quantity'] < $quantity) {
                throw new DomainException('Insufficient stock. Available: ' . (int) $product['stock_quantity'] . ' unit(s).');
            }
            if ($customerId !== null && $this->db->table('customers')->where('id', $customerId)->countAllResults() !== 1) {
                throw new DomainException('The selected customer was not found.');
            }
            if ($this->db->table('users')->where('id', $staffId)->where('is_active', 1)->countAllResults() !== 1) {
                throw new DomainException('The staff account is unavailable.');
            }

            $priceCents = (int) round(((float) $product['price']) * 100);
            $totalCents = $priceCents * $quantity;
            if ($priceCents < 0 || $totalCents > 9999999999) {
                throw new DomainException('The requested sale total exceeds the supported amount.');
            }

            // Double-check stock in the UPDATE itself to guard against concurrent requests.
            $this->db->query('UPDATE products SET stock_quantity = stock_quantity - ?, updated_at = NOW() WHERE id = ? AND is_active = 1 AND stock_quantity >= ?', [$quantity, $productId, $quantity]);
            if ($this->db->affectedRows() !== 1) {
                throw new DomainException('Stock changed during checkout. Please try again.');
            }
            $this->db->table('sales')->insert([
                'product_id' => $productId,
                'customer_id' => $customerId,
                'sold_by' => $staffId,
                'quantity' => $quantity,
                'total_price' => number_format($totalCents / 100, 2, '.', ''),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $id = (int) $this->db->insertID();
            if ($id < 1 || $this->db->transStatus() === false) {
                throw new RuntimeException('Could not save this transaction.');
            }
            $this->db->transCommit();
            return $id;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }
}
