<?php
namespace Src\Controllers;

class UserController {
    
    // Endpoint untuk mendapatkan daftar semua user
    public function index() {
        echo json_encode([ 
            "success" => true,
            "data" => [
                ["id" => 1, "name" => "Admin", "email" => "admin@example.com"],
                ["id" => 2, "name" => "Tirsa", "email" => "tirsa@example.com"]
            ]
        ]);
    }

    // Endpoint untuk mendapatkan detail user berdasarkan ID
    public function show($id) {
        echo json_encode([ 
            "success" => true,
            "data" => ["id" => $id, "name" => "User " . $id]
        ]);
    }
}