<?php
/**
 * public/index.php
 * File utama yang menangani inisialisasi Router dan pemetaan semua rute.
 */

// ====================================================
// PENGATURAN HEADER CORS
// ====================================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Menggunakan namespace untuk kelas
use Src\Router;
use Src\Controllers\UserController;

// ----------------------------------------------------
// PENGATURAN AWAL
// ----------------------------------------------------

// Memuat file koneksi database dari folder config/ (Path Perbaikan)
require __DIR__ . '/../config/database.php'; 

// Memuat (require) file Router dan Controller
require __DIR__ . '/../src/Router.php';
require __DIR__ . '/../src/Controllers/UserController.php';

// Inisialisasi Router
$router = new Router();
$controllerClass = 'UserController'; 

// Base Path untuk sumber daya users
$baseRoute = '/api/v1/users';

// ----------------------------------------------------
// DAFTAR ROUTE LENGKAP (CRUD)
// ----------------------------------------------------

// R - Read All
$router->add('GET', $baseRoute, [$controllerClass, 'index']); 

// R - Read One
$router->add('GET', $baseRoute . '/{id}', [$controllerClass, 'show']);

// C - Create
$router->add('POST', $baseRoute, [$controllerClass, 'create']);

// U - Update
$router->add('PUT', $baseRoute . '/{id}', [$controllerClass, 'update']);
$router->add('PATCH', $baseRoute . '/{id}', [$controllerClass, 'update']); 

// D - Delete
$router->add('DELETE', $baseRoute . '/{id}', [$controllerClass, 'delete']);


// Jalankan Router
$router->run();