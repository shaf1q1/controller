<?php

namespace App\Models;

use CodeIgniter\Model;

class PerincianModulModel extends Model
{
    protected $table      = 'aict4u103dperincianmodul';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'idservis',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get perincian by servis ID
     */
    public function getByServis($idservis)
    {
        return $this->where('idservis', $idservis)->first();
    }
}
