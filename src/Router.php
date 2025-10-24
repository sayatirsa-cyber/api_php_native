<?php

namespace Src;

class Router {
    
    private array $routes = []; 

    public function add(string $method, string $path, callable $handler) {
        $this->routes[] = compact('method', 'path', 'handler'); 
    }

    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // **PERBAIKAN BASE PATH DAN INDEX.PHP**
        $basePath = '/api_php_native/public';
        
        // Hapus path folder proyek
        if (strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        // Hapus '/index.php'
        if (strpos($uri, '/index.php') === 0) {
            $uri = substr($uri, strlen('/index.php'));
        }

        // **PERBAIKAN TRAILING SLASH**
        $uri = rtrim($uri, '/'); 

        // Penyesuaian Garis Miring (Slash)
        if (empty($uri)) {
             $uri = '';
        } 
        elseif ($uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        header('Content-Type: application/json');

        foreach ($this->routes as $route) { 
            if ($route['method'] === $method && $route['path'] === $uri) {
                call_user_func($route['handler']);
                return; 
            }
        }

        // Rute Tidak Ditemukan (404)
        http_response_code(404); 
        echo json_encode(["success" => false, "error" => "Route not found"]); 
    }
}