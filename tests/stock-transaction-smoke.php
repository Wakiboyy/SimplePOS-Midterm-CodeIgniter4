<?php
// This isolated test double checks checkout's core transaction behavior even when
// MySQL and CodeIgniter PHP extensions are unavailable to the developer.
// Execute: php tests/stock-transaction-smoke.php
namespace CodeIgniter\Database {
    class BaseConnection {}
}
namespace {
    require_once __DIR__ . '/../app/Services/SaleRecorder.php';

    final class FakeResult
    {
        public function __construct(private ?array $row) {}
        public function getRowArray(): ?array { return $this->row; }
    }
    final class FakeTable
    {
        private array $conditions = [];
        public function __construct(private FakeDb $db, private string $table) {}
        public function where(string $key, mixed $value): self { $this->conditions[$key] = $value; return $this; }
        public function countAllResults(): int
        {
            if ($this->table === 'customers') return array_key_exists($this->conditions['id'] ?? -1, $this->db->customers) ? 1 : 0;
            if ($this->table === 'users') return (($this->conditions['id'] ?? null) === 9 && ($this->conditions['is_active'] ?? 1) === 1) ? 1 : 0;
            return 0;
        }
        public function insert(array $row): void
        {
            if ($this->table !== 'sales') throw new \LogicException('Unexpected table');
            $this->db->sales[] = $row;
        }
    }
    final class FakeDb extends \CodeIgniter\Database\BaseConnection
    {
        public array $product = ['id'=>1, 'price'=>'50.00', 'stock_quantity'=>10, 'is_active'=>1];
        public array $customers = [2=>true];
        public array $sales = [];
        private array $snapshot = [];
        private int $rows = 0;
        public function transBegin(): void { $this->snapshot = [$this->product, $this->sales]; }
        public function transCommit(): void { $this->snapshot = []; }
        public function transRollback(): void { [$this->product, $this->sales] = $this->snapshot; }
        public function transStatus(): bool { return true; }
        public function insertID(): int { return count($this->sales); }
        public function affectedRows(): int { return $this->rows; }
        public function table(string $table): FakeTable { return new FakeTable($this, $table); }
        public function query(string $sql, array $bindings): FakeResult|bool
        {
            if (str_starts_with($sql, 'SELECT')) {
                return new FakeResult($bindings[0] === 1 ? $this->product : null);
            }
            if (str_starts_with($sql, 'UPDATE')) {
                [$qty, $productId, $check] = $bindings;
                $ok = $productId === 1 && $this->product['is_active'] === 1 && $this->product['stock_quantity'] >= $check;
                $this->rows = $ok ? 1 : 0;
                if ($ok) $this->product['stock_quantity'] -= $qty;
                return true;
            }
            throw new \LogicException('Unexpected query');
        }
    }
    function check(bool $condition, string $description): void
    {
        if (!$condition) throw new \RuntimeException('FAILED: ' . $description);
        echo "PASS: {$description}\n";
    }
    $db = new FakeDb();
    $sales = new \App\Services\SaleRecorder($db);
    check($sales->record(1, 2, 9, 3) === 1, 'First sale ID');
    check($db->product['stock_quantity'] === 7, 'Stock 10 -> 7 after 3 sold');
    check($db->sales[0]['total_price'] === '150.00', 'Price comes from server database (3 × 50)');
    try { $sales->record(1, 2, 9, 8); check(false, 'Reject sale of 8 when stock is 7'); }
    catch (\DomainException $e) { check(str_contains($e->getMessage(),'Insufficient stock'), 'Oversell rejected with clear message'); }
    check($db->product['stock_quantity'] === 7 && count($db->sales) === 1, 'Oversell rolls back stock and sale insert');
    check($sales->record(1, null, 9, 2) === 2, 'Guest sale recorded');
    check($db->product['stock_quantity'] === 5 && $db->sales[1]['total_price'] === '100.00', 'Second sale correctly leaves stock 5');
    echo "All checkout smoke tests passed.\n";
}
