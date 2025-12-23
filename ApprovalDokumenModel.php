<?php

namespace App\Models;

use CodeIgniter\Model;

class ApprovalDokumenModel extends Model
{
    protected $table      = 'aict4u106m_approval_dokumen';
    protected $primaryKey = 'iddoc';
    protected $allowedFields = [
        'idservis', 'nama', 'namafail', 'mime', 'descdoc',
        'is_approved', 'approved_by', 'approved_at', 'deleted_at'
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
