<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PosDemoSeeder extends Seeder
{
    public function run()
    {
        if ((int) $this->db->table('products')->countAllResults() === 0) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('products')->insertBatch(array_map(static fn (array $row): array => $row + ['created_at' => $now], [
                ['name' => 'Classic Milk Tea', 'price' => '95.00', 'stock_quantity' => 42, 'is_active' => 1],
                ['name' => 'Caramel Latte', 'price' => '125.00', 'stock_quantity' => 28, 'is_active' => 1],
                ['name' => 'Chocolate Muffin', 'price' => '75.00', 'stock_quantity' => 36, 'is_active' => 1],
                ['name' => 'Ham and Cheese Sandwich', 'price' => '110.00', 'stock_quantity' => 18, 'is_active' => 1],
                ['name' => 'Iced Matcha', 'price' => '135.00', 'stock_quantity' => 23, 'is_active' => 1],
                ['name' => 'Bottled Water 500ml', 'price' => '25.00', 'stock_quantity' => 80, 'is_active' => 1],
                ['name' => 'Butter Croissant', 'price' => '85.00', 'stock_quantity' => 25, 'is_active' => 1],
                ['name' => 'Fresh Lemonade', 'price' => '65.00', 'stock_quantity' => 40, 'is_active' => 1],
            ]));
        }

        if ((int) $this->db->table('customers')->countAllResults() === 0) {
            $now = date('Y-m-d H:i:s');
            $this->db->table('customers')->insertBatch(array_map(static fn (array $row): array => $row + ['created_at' => $now], [
                ['full_name' => 'Maria Santos', 'email' => 'maria@example.com', 'phone' => '09171234567'],
                ['full_name' => 'Juan Dela Cruz', 'email' => 'juan@example.com', 'phone' => '09181234567'],
                ['full_name' => 'Anna Reyes', 'email' => 'anna@example.com', 'phone' => '09191234567'],
                ['full_name' => 'Carlo Mendoza', 'email' => 'carlo@example.com', 'phone' => '09201234567'],
                ['full_name' => 'Bea Garcia', 'email' => 'bea@example.com', 'phone' => '09211234567'],
                ['full_name' => 'Paolo Rivera', 'email' => 'paolo@example.com', 'phone' => '09221234567'],
            ]));
        }
    }
}
