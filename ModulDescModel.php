<?php
namespace App\Models;
use CodeIgniter\Model;

class ModulDescModel extends Model
{
    protected $table = 'aict4u108mdes';
    protected $primaryKey = 'iddesc';

    protected $allowedFields = [
        '108idservis',
        'description'
    ];

    protected $protectFields = false; // allow weird column names

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

   protected $useSoftDeletes = true;
    protected $deletedField  = 'deleted_at';

}
