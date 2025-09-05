<?php
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'auth:administrateur'], function ($routes) {
    //Routes vers le tableau de bord
    $routes->get('dashboard', 'Admin::dashboard');

    $routes->group('user', function ($routes) {
        $routes->get('/', 'User::index');
        $routes->get('(:num)', 'User::edit/$1');
        $routes->get('new', 'User::create');
        $routes->post('update', 'User::update');
        $routes->post('insert', 'User::insert');
        $routes->post('switch-active','User::switchActive');
    });

    $routes->group('user-permission', function ($routes) {
       $routes->get('/', 'UserPermission::index');
       $routes->post('update', 'UserPermission::update');
       $routes->post('insert', 'UserPermission::insert');
       $routes->post('delete', 'UserPermission::delete');
    });

    $routes->group('brand', function ($routes) {
        $routes->get('/', 'Brand::index');
        $routes->post('update', 'Brand::update');
        $routes->post('insert', 'Brand::insert');
        $routes->post('delete', 'Brand::delete');
    });

    $routes->group('category-ingredient', function ($routes) {
       $routes->get('/', 'CategIng::index');
        $routes->post('update', 'CategIng::update');
        $routes->post('insert', 'CategIng::insert');
        $routes->post('delete', 'CategIng::delete');
    });
});