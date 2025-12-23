<?php
namespace App\Models;
use CodeIgniter\Model;

class ServisModel extends Model
{
    protected $table = 'aict4u103dservis';
    protected $primaryKey = 'idservis';

    protected $allowedFields = [
        'namaservis',
        'infourl',
        'mohonurl',
        'status',        // <-- jika guna status
        'created_by',    // <-- penting untuk dashboard user
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
