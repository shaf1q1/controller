<?php

namespace App\Controllers;

use App\Models\DokumenModel;
use App\Models\ServisModel;
use App\Models\ModulDescModel;
use CodeIgniter\HTTP\ResponseInterface;

class ApprovalDokumenController extends BaseController
{
    protected $dokumenModel;
    protected $servisModel;
    protected $descModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->dokumenModel = new DokumenModel();
        $this->servisModel  = new ServisModel();
        $this->descModel    = new ModulDescModel();
    }

    /** Main page */
    public function index()
    {
        return view('approvaldokumen');
    }

    /** AJAX: fetch dokumen with optional filter and pagination */
    public function getAll(): ResponseInterface
    {
        $status = $this->request->getGet('status') ?? 'all';
        $page   = max(1, (int)$this->request->getGet('page'));
        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $builder = $this->dokumenModel;

        // Apply status filter
        if ($status !== 'all') {
            $builder = $builder->where('status', $status);
        }

        // Count total items for pagination
        $total = $builder->countAllResults(false);

        // Fetch paginated data
        $dokumen = $builder
            ->orderBy('created_at', 'DESC')
            ->findAll($limit, $offset);

        // Fetch related servis name and description for each dokumen
        foreach ($dokumen as &$d) {
            $servis = $this->servisModel->find($d['106idservis']);
            $d['servis'] = $servis['namaservis'] ?? '-';

            $desc = $this->descModel->where('108idservis', $d['106idservis'])->first();
            $d['descdoc'] = $desc['description'] ?? $d['descdoc'] ?? '-';
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $dokumen,
            'total'  => $total,
            'limit'  => $limit,
            'page'   => $page
        ]);
    }

    /** AJAX: approve or reject a dokumen */
    public function changeStatus(int $iddoc, string $status): ResponseInterface
    {
        $status = strtolower($status);

        if (!in_array($status, ['approved', 'rejected'])) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Status tidak sah'
            ]);
        }

        $dokumen = $this->dokumenModel->find($iddoc);

        if (!$dokumen) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Dokumen tidak dijumpai'
            ]);
        }

        try {
            $this->dokumenModel->update($iddoc, ['status' => $status]);
            return $this->response->setJSON([
                'status' => true,
                'message' => "Dokumen telah diubah ke status '{$status}'",
                'updatedDokumen' => $this->dokumenModel->find($iddoc)
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    /** AJAX: fetch single dokumen details */
    public function getDokumen(int $iddoc): ResponseInterface
    {
        $dokumen = $this->dokumenModel->find($iddoc);

        if (!$dokumen) {
            return $this->response->setJSON([
                'status' => false,
                'message' => 'Dokumen tidak dijumpai'
            ]);
        }

        // Attach related servis and description
        $servis = $this->servisModel->find($dokumen['106idservis']);
        $dokumen['servis'] = $servis['namaservis'] ?? '-';

        $desc = $this->descModel->where('108idservis', $dokumen['106idservis'])->first();
        $dokumen['descdoc'] = $desc['description'] ?? $dokumen['descdoc'] ?? '-';

        return $this->response->setJSON([
            'status' => true,
            'data' => $dokumen
        ]);
    }
}
