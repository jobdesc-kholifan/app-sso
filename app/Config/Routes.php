<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'HomeController::index');

// Authentication Routes
$routes->get('login', 'AuthController::login');
$routes->post('login/process', 'AuthController::process');
$routes->get('logout', 'AuthController::logout');

// Master User Management
$routes->group('master/users', function($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('ajaxList', 'UserController::ajaxList');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('delete/(:num)', 'UserController::delete/$1');
});
