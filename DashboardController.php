<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class UserController extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['url', 'form', 'session']);
    }

    /* =========================
       MAIN PAGE
    ========================= */
    public function index()
    {
        return view('users/index', $this->getDashboardCounts());
    }

    /* =========================
       GET ALL USERS FOR DATATABLE
    ========================= */
    public function getAll(): ResponseInterface
    {
        $users = $this->userModel
            ->select('id, username, email, role, status')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON(['data' => $users]);
    }

    /* =========================
       GET SINGLE USER (EDIT MODAL)
    ========================= */
    public function show($id): ResponseInterface
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => false, 'message' => 'User not found']);
        }

        return $this->response->setJSON([
            'id'       => $user['id'],
            'username' => $user['username'],
            'email'    => $user['email'],
            'role'     => $user['role'],
            'status'   => $user['status'],
            'csrfHash' => csrf_hash()
        ]);
    }

    /* =========================
       ADD USER
    ========================= */
    public function add(): ResponseInterface
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,uploader]',
            'status'   => 'required|in_list[active,inactive]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => false, 'message' => $validation->listErrors(), 'csrfHash' => csrf_hash()]);
        }

        $data = [
            'username' => trim($this->request->getPost('username')),
            'email'    => trim($this->request->getPost('email')),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
            'status'   => $this->request->getPost('status'),
        ];

        $this->userModel->insert($data);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'User berjaya ditambah',
            'csrfHash' => csrf_hash()
        ]);
    }

    /* =========================
       UPDATE USER
    ========================= */
    public function update($id): ResponseInterface
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => false, 'message' => 'User not found', 'csrfHash' => csrf_hash()]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'username' => 'required|min_length[3]|max_length[50]',
            'email'    => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role'     => 'required|in_list[admin,uploader]',
            'status'   => 'required|in_list[active,inactive]',
            'password' => 'permit_empty|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => false, 'message' => $validation->listErrors(), 'csrfHash' => csrf_hash()]);
        }

        $data = [
            'username' => trim($this->request->getPost('username')),
            'email'    => trim($this->request->getPost('email')),
            'role'     => $this->request->getPost('role'),
            'status'   => $this->request->getPost('status'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $data);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'User berjaya dikemaskini',
            'csrfHash' => csrf_hash()
        ]);
    }

    /* =========================
       DELETE USER
    ========================= */
    public function delete($id): ResponseInterface
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setStatusCode(404)
                ->setJSON(['status' => false, 'message' => 'User not found', 'csrfHash' => csrf_hash()]);
        }

        $this->userModel->delete($id);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'User berjaya dipadam',
            'csrfHash' => csrf_hash()
        ]);
    }

    /* =========================
       DASHBOARD COUNTS
    ========================= */
    private function getDashboardCounts(): array
    {
        return [
            'totalUsers'    => $this->userModel->countAll(),
            'totalAdmin'    => $this->userModel->where('role', 'admin')->countAllResults(),
            'totalUploader' => $this->userModel->where('role', 'uploader')->countAllResults()
        ];
    }
}
