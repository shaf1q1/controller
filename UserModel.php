<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'status'
    ];

    /* =====================
     * TIMESTAMPS
     * ===================== */
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /* =====================
     * PASSWORD HASHING
     * ===================== */
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Hash password before insert/update
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password']) && !empty($data['data']['password'])) {
            // Only hash if it's not already hashed
            if (password_get_info($data['data']['password'])['algo'] === 0) {
                $data['data']['password'] = password_hash(
                    $data['data']['password'],
                    PASSWORD_DEFAULT
                );
            }
        } else {
            unset($data['data']['password']); // Prevent overwriting with empty password
        }

        return $data;
    }

    /* =====================
     * DASHBOARD COUNTS
     * ===================== */

    public function countAllUsers(): int
    {
        return $this->countAllResults();
    }

    public function countByRole(string $role): int
    {
        return $this->where('role', $role)->countAllResults();
    }

    public function countByStatus(string $status): int
    {
        return $this->where('status', $status)->countAllResults();
    }
}
