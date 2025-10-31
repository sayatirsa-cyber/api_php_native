<?php
/**
 * src/Controllers/UserController.php
 * Controller lengkap dengan operasi CRUD (Create, Read, Update, Delete) menggunakan PDO.
 */

namespace Src\Controllers;

// Memanggil file koneksi database
require_once __DIR__ . '/../database.php'; 

class UserController
{
    private $db;

    public function __construct()
    {
        try {
            // Inisialisasi koneksi PDO
            $database = new \Database();
            $this->db = $database->getPdo();
        } catch (\PDOException $e) {
            http_response_code(500);
            exit(json_encode(["success" => false, "message" => "Server Error: Koneksi database tidak tersedia"]));
        }
    }
    
    // ===============================================
    // C - CREATE (Method: POST)
    // ===============================================

    public function create(): void
    {
        // Ambil data JSON dari body request
        $input = json_decode(file_get_contents('php://input'), true);

        if (!isset($input['nama'], $input['email'], $input['password'])) {
            http_response_code(400); // Bad Request
            exit(json_encode(["success" => false, "message" => "Input nama, email, dan password wajib diisi."]));
        }

        $nama = $input['nama'];
        $email = $input['email'];
        $password_plain = $input['password'];
        
        // HASH Password (WAJIB)
        $password_hash = password_hash($password_plain, PASSWORD_BCRYPT);

        try {
            // Prepared Statement untuk INSERT data
            $stmt = $this->db->prepare("INSERT INTO users (nama, email, password) VALUES (:nama, :email, :password_hash)");
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $password_hash);
            
            $stmt->execute();
            
            $new_id = $this->db->lastInsertId();

            http_response_code(201); // 201 Created
            echo json_encode([
                "success" => true, 
                "message" => "User berhasil dibuat",
                "data" => ["id" => $new_id, "nama" => $nama, "email" => $email]
            ]);

        } catch (\PDOException $e) {
            // Cek error jika email sudah terdaftar
            if ($e->getCode() === '23000') {
                http_response_code(409); // Conflict
                exit(json_encode(["success" => false, "message" => "Email sudah terdaftar."]));
            }
            
            http_response_code(500);
            exit(json_encode(["success" => false, "message" => "Gagal menyimpan data: " . $e->getMessage()]));
        }
    }
    
    // ===============================================
    // R - READ (Method: GET)
    // ===============================================

    // Read All
    public function index(): void
    {
        try {
            $stmt = $this->db->query("SELECT id, nama, email FROM users");
            $users = $stmt->fetchAll();

            http_response_code(200);
            echo json_encode([
                "success" => true,
                "data" => $users
            ]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Gagal mengambil data: " . $e->getMessage()]);
        }
    }

    // Read One
    public function show(string $id): void
    {
        try {
            // Prepared Statement untuk menghindari SQL Injection
            $stmt = $this->db->prepare("SELECT id, nama, email FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                http_response_code(200);
                echo json_encode(["success" => true, "data" => $user]);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "User tidak ditemukan."]);
            }
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Gagal mengambil data detail: " . $e->getMessage()]);
        }
    }

    // ===============================================
    // U - UPDATE (Method: PUT/PATCH)
    // ===============================================

    public function update(string $id): void
    {
        // Ambil data dari body request
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Pastikan ada data yang dikirim untuk diupdate
        if (empty($input)) {
            http_response_code(400);
            exit(json_encode(["success" => false, "message" => "Tidak ada data untuk diupdate."]));
        }

        $fields = [];
        $params = ['id' => $id];
        
        // Membangun query secara dinamis
        if (isset($input['nama'])) {
            $fields[] = "nama = :nama";
            $params['nama'] = $input['nama'];
        }
        if (isset($input['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $input['email'];
        }
        if (isset($input['password'])) {
            // HASH password jika diupdate
            $fields[] = "password = :password";
            $params['password'] = password_hash($input['password'], PASSWORD_BCRYPT);
        }

        if (empty($fields)) {
            http_response_code(400);
            exit(json_encode(["success" => false, "message" => "Tidak ada kolom yang valid untuk diupdate."]));
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                echo json_encode(["success" => true, "message" => "User ID $id berhasil diupdate."]);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "User ID $id tidak ditemukan atau tidak ada perubahan data."]);
            }

        } catch (\PDOException $e) {
            http_response_code(500);
            exit(json_encode(["success" => false, "message" => "Gagal mengupdate data: " . $e->getMessage()]));
        }
    }

    // ===============================================
    // D - DELETE (Method: DELETE)
    // ===============================================

    public function delete(string $id): void
    {
        try {
            // Prepared Statement untuk DELETE data
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                http_response_code(200); // Atau 204 No Content
                echo json_encode(["success" => true, "message" => "User ID $id berhasil dihapus."]);
            } else {
                http_response_code(404);
                echo json_encode(["success" => false, "message" => "User ID $id tidak ditemukan."]);
            }

        } catch (\PDOException $e) {
            http_response_code(500);
            exit(json_encode(["success" => false, "message" => "Gagal menghapus data: " . $e->getMessage()]));
        }
    }
}