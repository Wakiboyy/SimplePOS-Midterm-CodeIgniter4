<?php
namespace App\Database\Seeds;

use CodeIgniter\CLI\CLI;
use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        if ((int) $this->db->table('users')->countAllResults() !== 0) {
            CLI::write('No account created: staff accounts already exist.', 'yellow');
            return;
        }

        $password = 'Pos-' . bin2hex(random_bytes(8)) . '!9';
        $this->db->table('users')->insert([
            'username' => 'admin',
            'full_name' => 'POS Administrator',
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        CLI::write('Administrator account created. SAVE THIS PASSWORD NOW.', 'green');
        CLI::write('Username: admin', 'green');
        CLI::write('Password: ' . $password, 'green');
        CLI::write('This password is not stored anywhere in plaintext.', 'yellow');
    }
}
