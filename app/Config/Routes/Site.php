<?php
$routes->get('/', 'Home::index');

$routes->get('/sign-in', 'Auth::signIn');
$routes->post('/auth/login', 'Auth::login');
$routes->get('/auth/logout', 'Auth::logout');
