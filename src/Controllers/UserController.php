<?php
/**
 * src/Controllers/UserController.php
 * Controller yang dimodifikasi untuk menggunakan koneksi PDO.
 */

namespace Src\Controllers;

// WAJIB: Memanggil file koneksi database
require_once __DIR__ . '/../database.php'; 

class UserController
{
    private $db;

    // 1. Tambahkan fungsi __construct untuk menginisialisasi koneksi PDO
    public function __construct()
    {
        try {
            // Membuat objek Database dan mendapatkan koneksi PDO
            $database = new \Database();
            $this->db = $database->getPdo();
        } catch (\PDOException $e) {
            // Jika koneksi gagal, hentikan eksekusi dan kirim respons error
            http_response_code(500);
            exit(json_encode([
                "success" => false, 
                "message" => "Server Error: Koneksi database tidak tersedia. Detail: " . $e->getMessage()
            ]));
        }
    }
    
    // Endpoint untuk mendapatkan daftar semua user (CRUD - Read All)
    public function index(): void
    {
        try {
            // Query sederhana untuk mengambil semua data (TANPA password)
            $stmt = $this->db->query("SELECT id, nama, email FROM users");
            $users = $stmt->fetchAll();

            http_response_code(200);
            echo json_encode([
                "success" => true,
                "data" => $users // Data diambil dari tabel 'users'
            ]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Gagal mengambil data: " . $e->getMessage()
            ]);
        }
    }

    // Endpoint untuk mendapatkan detail user berdasarkan ID (CRUD - Read One)
    public function show(string $id): void
    {
        try {
            // Menggunakan Prepared Statement untuk mencegah SQL Injection
            $stmt = $this->db->prepare("SELECT id, nama, email FROM users WHERE id = :id");
            
            // Bind parameter
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            
            // Eksekusi query
            $stmt->execute();
            
            // Ambil satu baris data
            $user = $stmt->fetch();

            if ($user) {
                http_response_code(200);
                echo json_encode(["success" => true, "data" => $user]);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "User dengan ID " . $id . " tidak ditemukan."]);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Gagal mengambil data detail: " . $e->getMessage()
            ]);
        }
    }
    
    // Anda bisa melanjutkan fungsi CRUD lainnya (create, update, delete) di sini
}
?>