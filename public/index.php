<?php
use Src\Router;
use Src\Controllers\UserController;

// Memuat (require) file Router dan Controller
require __DIR__ . '/../src/Router.php';
require __DIR__ . '/../src/Controllers/UserController.php';

// Inisialisasi
$router = new Router();
$userController = new UserController();

// ----------------------------------------------------
// DAFTAR ROUTE
// ----------------------------------------------------

// 1. Rute Index (GET /api/v1/users) - DAFTAR PENGGUNA
$router->add('GET', '/api/v1/users', [$userController, 'index']); 

// 2. Rute Show (GET /api/v1/users/1) - DETAIL PENGGUNA
$router->add('GET', '/api/v1/users/1', function() use ($userController) { 
    $userController->show(1);
});

// Jalankan Router
$router->run();