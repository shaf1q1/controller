<?php
namespace App\Controllers\Servis;

use App\Controllers\BaseController;
use App\Models\ServisModel;

class ServisKelulusanController extends BaseController
{
    protected $servisModel;

    public function __construct()
    {
        $this->servisModel = new ServisModel();
        helper(['url','form']);
    }

    // Main page
    public function index()
    {
        return view('servis/servisKelulusan', [
            'title' => 'Sistem Servis Kelulusan'
        ]);
    }

    // Get all servis (AJAX) with optional status filter
    public function getAll()
    {
        $status = $this->request->getGet('status') ?? 'all';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 10;

        $builder = $this->servisModel->select('idservis, namaservis AS nama, status, infourl, mohonurl, infoperincian, created_at');

        if ($status !== 'all') {
            $builder = $builder->where('status', $status === 'available' ? 1 : 0);
        }

        $total = $builder->countAllResults(false);
        $data = $builder->orderBy('created_at', 'desc')
                        ->findAll($limit, ($page-1)*$limit);

        return $this->response->setJSON([
            'data' => $data,
            'page' => $page,
            'limit' => $limit,
            'total' => $total
        ]);
    }

    // Get single servis detail
    public function getServis($id)
    {
        $servis = $this->servisModel
                       ->select('idservis, namaservis AS nama, status, infourl, mohonurl, infoperincian, created_at')
                       ->find($id);

        if (!$servis) {
            return $this->response->setJSON(['status'=>false,'message'=>'Servis tidak ditemui']);
        }

        // Tukar status integer jadi string
        $servis['status'] = $servis['status'] == 1 ? 'available' : 'unavailable';

        return $this->response->setJSON($servis);
    }

    // Toggle status available / unavailable
    public function changeStatus($id, $status)
    {
        if (!in_array($status, ['available','unavailable'])) {
            return $this->response->setJSON(['status'=>false,'message'=>'Status tidak sah']);
        }

        $servis = $this->servisModel->find($id);
        if (!$servis) {
            return $this->response->setJSON(['status'=>false,'message'=>'Servis tidak ditemui']);
        }

        $this->servisModel->update($id, ['status'=>$status==='available' ? 1 : 0]);

        return $this->response->setJSON([
            'status'=>true,
            'message'=>"Servis telah diubah ke status: $status"
        ]);
    }
}
