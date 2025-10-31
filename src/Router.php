<?php
/**
 * src/Router.php
 * Router yang diperbaiki untuk mendukung parameter dinamis (e.g., /users/{id}).
 */

namespace Src;

class Router {
    
    private array $routes = []; 

    /**
     * Menambahkan rute baru
     * Handler kini bisa berupa array ['ControllerClass', 'methodName']
     */
    public function add(string $method, string $path, callable|array $handler) {
        // Pastikan path selalu diawali slash untuk konsistensi
        if ($path !== '' && $path[0] !== '/') {
            $path = '/' . $path;
        }
        $this->routes[] = compact('method', 'path', 'handler'); 
    }

    /**
     * Menjalankan proses routing
     */
    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // --- PRE-PROCESSING URI ---
        $basePath = '/api_php_native/public';
        
        // 1. Hapus path folder proyek dari URI
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // 2. Hapus '/index.php'
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, strlen('/index.php'));
        }

        // 3. Bersihkan URI: Pastikan slash tunggal di awal dan tanpa trailing slash
        $uri = rtrim($uri, '/'); 
        if (empty($uri)) {
            $uri = '/'; // Rute root
        } elseif ($uri[0] !== '/') {
            $uri = '/' . $uri;
        }
        
        // --- PENANGANAN ROUTE ---
        header('Content-Type: application/json');
        
        foreach ($this->routes as $route) { 
            // 1. Cek Metode HTTP
            if ($route['method'] !== $method) {
                continue; // Lanjut ke rute berikutnya jika metode tidak cocok
            }

            // 2. Ubah Path Rute menjadi pola Regex
            // Mengganti {parameter} menjadi (.*) untuk menangkap nilai
            $routePattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $route['path']);
            
            // Tambahkan delimiter untuk regex dan pastikan cocok dari awal sampai akhir
            $routePattern = '#^' . $routePattern . '$#';

            // 3. Cocokkan URI dengan Pola Regex
            if (preg_match($routePattern, $uri, $matches)) {
                
                // Hapus elemen pertama ($matches[0] adalah string URI lengkap)
                array_shift($matches);
                
                // Panggil Handler (Controller dan Metode) dengan Parameter
                $handler = $route['handler'];
                
                // Panggil metode pada controller dengan argumen (parameters)
                // Menggunakan reflection untuk instansiasi jika handler adalah array class/method
                if (is_array($handler) && count($handler) === 2) {
                    $controllerClass = "Src\\Controllers\\" . $handler[0]; // Tambahkan namespace
                    $methodName = $handler[1];
                    
                    // Instansiasi Controller
                    if (class_exists($controllerClass)) {
                        $controller = new $controllerClass();
                        
                        // Panggil metode dengan parameter yang ditangkap ($matches)
                        call_user_func_array([$controller, $methodName], $matches);
                        return;
                    }
                }
                
                // Jika Handler adalah callable/fungsi anonim
                call_user_func_array($handler, $matches); 
                return;
            }
        }

        // Rute Tidak Ditemukan (404)
        http_response_code(404); 
        echo json_encode(["success" => false, "error" => "Route not found"]); 
    }
}