<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumenModel extends Model
{
    protected $table = 'aict4u106mdoc';
    protected $primaryKey = 'iddoc';

    protected $allowedFields = [
        'idservis',
        'nama',
        'namafail',
        'mime',
        'descdoc',
        'status',
        'uploaded_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';
}
