<?php
namespace App\Models;

use CodeIgniter\Model;

class StaffModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['username', 'full_name', 'password', 'avatar', 'is_active'];
    protected $useTimestamps = true;
}
