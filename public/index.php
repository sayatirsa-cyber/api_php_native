<?php
/**
 * index.php
 * File utama yang menangani inisialisasi Router dan pemetaan semua rute.
 */

// Menggunakan namespace untuk kelas
use Src\Router;
use Src\Controllers\UserController;

// ----------------------------------------------------
// PENGATURAN AWAL
// ----------------------------------------------------

// Memuat (require) file Router dan Controller
// Pastikan path sudah benar
require __DIR__ . '/../src/Router.php';
require __DIR__ . '/../src/Controllers/UserController.php';

// Inisialisasi Router dan Controller
$router = new Router();
// Instansiasi UserController dilakukan secara otomatis di Router.php yang sudah diperbaiki, 
// tetapi kita akan menyederhanakan cara add rutenya di sini.
// $userController = new UserController(); // TIDAK perlu jika Router.php sudah diperbaiki

// ----------------------------------------------------
// DAFTAR ROUTE LENGKAP (CRUD)
// ----------------------------------------------------
$controllerClass = 'UserController'; // Nama kelas yang akan dipanggil oleh Router

// Base Path untuk sumber daya users
$baseRoute = '/api/v1/users';

// 1. Rute Index (GET /api/v1/users) - DAFTAR PENGGUNA (R - Read All)
$router->add('GET', $baseRoute, [$controllerClass, 'index']); 

// 2. Rute Show (GET /api/v1/users/{id}) - DETAIL PENGGUNA (R - Read One)
// Menggunakan parameter dinamis {id}
$router->add('GET', $baseRoute . '/{id}', [$controllerClass, 'show']);

// 3. Rute Create (POST /api/v1/users) - BUAT PENGGUNA BARU (C - Create)
$router->add('POST', $baseRoute, [$controllerClass, 'create']);

// 4. Rute Update (PUT /api/v1/users/{id}) - UBAH DATA PENGGUNA (U - Update)
// Menggunakan parameter dinamis {id}
$router->add('PUT', $baseRoute . '/{id}', [$controllerClass, 'update']);
$router->add('PATCH', $baseRoute . '/{id}', [$controllerClass, 'update']); // PATCH untuk update parsial

// 5. Rute Delete (DELETE /api/v1/users/{id}) - HAPUS PENGGUNA (D - Delete)
// Menggunakan parameter dinamis {id}
$router->add('DELETE', $baseRoute . '/{id}', [$controllerClass, 'delete']);


// Jalankan Router
$router->run();