<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --------------------------------------------------------------------
// Default Route
// --------------------------------------------------------------------
$routes->get('/', 'Dashboard\DashboardController::index');

// --------------------------------------------------------------------
// Dashboard Routes
// --------------------------------------------------------------------
$routes->group('dashboard', function($routes) {
    $routes->get('/', 'Dashboard\DashboardController::index');
    $routes->get('loadPage/(:segment)', 'Dashboard\DashboardController::loadPage/$1');
});

// --------------------------------------------------------------------
// Perincian Modul Routes
// --------------------------------------------------------------------
$routes->group('perincianmodul', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'PerincianModulController::index');
    $routes->get('getServis/(:num)', 'PerincianModulController::getServis/$1');
    $routes->post('save', 'PerincianModulController::save');
    $routes->get('delete/(:num)', 'PerincianModulController::delete/$1');
});

// --------------------------------------------------------------------
// Tambahan Perincian Routes
// --------------------------------------------------------------------
$routes->group('tambahanperincian', ['namespace' => 'App\Controllers'], function ($routes) {
    $routes->get('/', 'TambahanPerincianController::index');
    $routes->get('add', 'TambahanPerincianController::index');
    $routes->get('getServis/(:num)', 'TambahanPerincianController::getServis/$1');
    $routes->post('save', 'TambahanPerincianController::save');
    $routes->post('saveServis', 'TambahanPerincianController::saveServis');
    $routes->post('deleteServis', 'TambahanPerincianController::deleteServis');
    $routes->get('getAll', 'TambahanPerincianController::getAll');
});

// --------------------------------------------------------------------
// Dokumen Management Routes
// --------------------------------------------------------------------
$routes->group('dokumen', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'DokumenController::index');
    $routes->get('getDokumen/(:num)', 'DokumenController::getDokumen/$1');
    $routes->post('tambah', 'DokumenController::tambah');
    $routes->post('kemaskini/(:num)', 'DokumenController::kemaskini/$1');
    $routes->post('softDelete/(:num)', 'DokumenController::softDelete/$1');
    $routes->post('restore/(:num)', 'DokumenController::restore/$1');
    $routes->post('remove/(:num)', 'DokumenController::remove/$1');
    $routes->get('edit/(:num)', 'DokumenController::edit/$1');
});

// --------------------------------------------------------------------
// Approval Dokumen Routes
// --------------------------------------------------------------------
$routes->group('approvaldokumen', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'ApprovalDokumenController::index');
    $routes->get('getAll', 'ApprovalDokumenController::getAll');
    $routes->get('getDokumen/(:num)', 'ApprovalDokumenController::getDokumen/$1');
    $routes->post('changeStatus/(:num)/(:segment)', 'ApprovalDokumenController::changeStatus/$1/$2');
});

// --------------------------------------------------------------------
// User Management Routes
// --------------------------------------------------------------------
$routes->group('users', ['namespace' => 'App\Controllers'], function($routes){
    $routes->get('/', 'UserController::index');
    $routes->get('getAll', 'UserController::getAll');
    $routes->get('(:num)', 'UserController::show/$1');
    $routes->post('add', 'UserController::add');
    $routes->post('update/(:num)', 'UserController::update/$1');
    $routes->post('delete/(:num)', 'UserController::delete/$1');
});

// --------------------------------------------------------------------
// Auth Routes
// --------------------------------------------------------------------
$routes->group('auth', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('login', 'AuthController::login');
    $routes->get('register', 'AuthController::register');
    $routes->post('processLogin', 'AuthController::processLogin');
    $routes->post('processRegister', 'AuthController::processRegister');
    $routes->post('change-password', 'AuthController::changePassword');
    $routes->get('logout', 'AuthController::logout');
});

// --------------------------------------------------------------------
// Servis Kelulusan Routes
// --------------------------------------------------------------------
$routes->group('serviskelulusan', ['namespace' => 'App\Controllers\Servis'], function($routes){
    $routes->get('/', 'ServisKelulusanController::index');
    $routes->get('getAll', 'ServisKelulusanController::getAll');
    $routes->get('getServis/(:num)', 'ServisKelulusanController::getServis/$1');
    $routes->post('changeStatus/(:num)/(:segment)', 'ServisKelulusanController::changeStatus/$1/$2');
});

// --------------------------------------------------------------------
// Frontend Routes
// --------------------------------------------------------------------
$routes->group('', ['namespace' => 'App\Controllers\frontend'], function($routes) {

    // Dashboard
    $routes->get('frontend', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // Perincian
    $routes->group('perincian', function($routes) {
        $routes->get('/', 'PerincianController::index');
        $routes->get('getServis/(:num)', 'PerincianController::getServis/$1');
        $routes->post('save', 'PerincianController::save');
    });

    // Dokumen Pengurusan
    $routes->group('pengurusan', function($routes) {
        $routes->get('/', 'DokumenPengurusanController::index');
        $routes->get('getDokumen/(:num)', 'DokumenPengurusanController::getDokumen/$1');
        $routes->get('getDokumenById/(:num)', 'DokumenPengurusanController::getDokumenById/$1');
        $routes->post('tambah', 'DokumenPengurusanController::tambah');
        $routes->post('kemaskini/(:num)', 'DokumenPengurusanController::kemaskini/$1');
        $routes->get('remove/(:num)', 'DokumenPengurusanController::remove/$1');
    });

});
