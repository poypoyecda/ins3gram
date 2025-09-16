<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Unit extends BaseController
{
    public function index()
    {
        helper('form');
        return $this->view('admin/unit');
    }
    public function search()
    {
        $request = $this->request;

        // Vérification AJAX
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête non autorisée']);
        }

        $um = Model('UnitModel');

        // Paramètres de recherche
        $search = $request->getGet('search') ?? '';
        $page = (int)($request->getGet('page') ?? 1);
        $limit = 20;

        // Utilisation de la méthode du Model (via le trait)
        $result = $um->quickSearchForSelect2($search, $page, $limit);

        // Réponse JSON
        return $this->response->setJSON($result);
    }
    public function insert()
    {
        $um = model('UnitModel');
        $data = $this->request->getPost();
        if ($um->insert($data)) {
            $this->success('Unité bien créé');
        } else {
            foreach ($um->errors() as $error) {
                $this->error($error);
            }
        }
        return $this->redirect('admin/unit');
    }

    public function update() {
        $um = model('UnitModel');
        $data = $this->request->getPost();
        $id = $data['id'];
        unset($data['id']);
        if ($um->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Unité modifié avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $um->errors(),
            ]);
        }
    }

    public function delete() {
        $um = model('UnitModel');
        $id = $this->request->getPost('id');
        if ($um->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => "Unité supprimé avec succés !",
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $um->errors(),
            ]);
        }
    }
}
