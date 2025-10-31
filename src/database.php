<?php
/**
 * src/database.php
 * Class yang bertanggung jawab membuat objek PDO
 */
 
// Panggil file konfigurasi 
require_once __DIR__ . '/api_php_native.php';

class Database {
    private $pdo;

    public function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        // Opsi PDO untuk penanganan error dan fetching
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Membuat koneksi PDO
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (\PDOException $e) {
            // Melemparkan error jika koneksi gagal
            throw new \PDOException("Koneksi Database Gagal: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Mendapatkan objek PDO yang sudah terinisialisasi
     * @return PDO
     */
    public function getPdo() {
        return $this->pdo;
    }
}
?>