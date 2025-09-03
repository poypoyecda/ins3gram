<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Unit extends BaseController
{
    public function index()
    {
        helper(['form']);
        return $this->view('admin/unit');
    }

    public function insert(){
        $um = model("UnitModel");
        $data = $this->request->getPost();
        if ($um->insert($data)) {
            $this->success = "Unité ajoutée";
        } else {
            foreach ($um->errors() as $error) {
                $this->error($error);
            }
        }
        return $this->redirect('admin/unit');
    }

    public function update()
    {
        $um = model("UnitModel");
        $data = $this->request->getPost();
        $id = $data['id'];
        unset($data['id']);
        if ($um->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $um->errors(),
            ]);
        }
    }

    public function delete()
    {
        $um = model("UnitModel");
        $id = $this->request->getPost('id');
        if ($um->delete($id))
        {
            return $this->response->setJSON([
                'success' => true,
                'message' => "l'unité a été supprimée avec succès",
            ]);
        } else
        {
            return $this->response->setJSON([
               "success" => false,
               "message" => $um->errors(),
            ]);
        }
    }
}