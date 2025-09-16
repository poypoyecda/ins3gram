<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Ingredient extends BaseController
{

    protected $breadcrumb = [['text' => 'Tableau de Bord','url' => '/admin/dashboard']];
    public function index()
    {
        $this->addBreadcrumb('Ingredient', "");
        return $this->view('admin/ingredient/index');
    }
    public function search()
    {
        $request = $this->request;

        // Vérification AJAX
        if (!$request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requête non autorisée']);
        }

        $im = Model('IngredientModel');

        // Paramètres de recherche
        $search = $request->getGet('search') ?? '';
        $page = (int)($request->getGet('page') ?? 1);
        $limit = 20;

        // Utilisation de la méthode du Model (via le trait)
        $result = $im->quickSearchForSelect2($search, $page, $limit);

        // Réponse JSON
        return $this->response->setJSON($result);
    }
    public function create() {
        helper('form');
        $this->addBreadcrumb('Ingredient', "/admin/ingredient");
        $this->addBreadcrumb('Création d\'un ingrédient', "");
        $tags = Model('IngredientModel')->findAll();
        return $this->view('admin/ingredient/form');
    }
}
