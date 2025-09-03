<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Recipe extends BaseController
{
    protected $breadcrumb = [['text' => 'tableau de bord', 'url' => '/admin/dashboard']];
    public function index()
    {
        return $this->view('admin/recipe/index');
    }


        public function create() {
            $this->addBreadcrumb('recettes', "/admin/recipe");
            $this->addBreadcrumb('création d\'une recette', "/admin/recipe/create");
            return $this->view('admin/recipe/form');
        }
}
