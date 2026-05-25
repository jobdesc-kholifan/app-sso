<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'HomeController::index');

// Authentication Routes
$routes->get('login', 'AuthController::login');
$routes->post('login/process', 'AuthController::process');
$routes->get('logout', 'AuthController::logout');

// Master User Management
$routes->group('master/users', function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('datatable', 'UserController::datatable');
    $routes->post('store', 'UserController::store');
    $routes->get('edit/(:num)', 'UserController::edit/$1');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('delete/(:num)', 'UserController::delete/$1');
});

// OAuth Sessions Monitoring
$routes->group('oauth/sessions', function ($routes) {
    $routes->get('/', 'OAuthSessionController::index');
    $routes->get('datatable', 'OAuthSessionController::datatable');
    $routes->post('revoke/(:segment)', 'OAuthSessionController::revoke/$1');
});

// OAuth & OIDC Routes
$routes->get('oauth/login', 'OAuthController::login');
$routes->post('oauth/login/process', 'OAuthController::processLogin');

$routes->get('oauth/authorize', 'OAuthController::authorize');
$routes->post('oauth/authorize', 'OAuthController::authorizeProcess');
$routes->get('oauth/logout', 'OAuthController::logout');

$routes->post('oauth/token', 'OAuthController::token');
$routes->get('oauth/userinfo', 'OAuthController::userinfo');
$routes->post('oauth/userinfo', 'OAuthController::userinfo');
$routes->get('oauth/tutorial', 'OAuthController::tutorial');
