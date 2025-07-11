<?php
// Set header agar response-nya dalam format JSON
header('Content-Type: application/json');

// Izinkan akses dari semua origin (biar bisa diakses dari mana aja, misal frontend beda domain)
header('Access-Control-Allow-Origin: *');

// Izinkan metode HTTP yang bisa dipakai oleh client
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Izinkan header tertentu seperti Authorization dan Content-Type
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Tangani request OPTIONS (preflight dari browser)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Load konfigurasi dan controller yang diperlukan
require_once 'config/database.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/StudentController.php';
require_once 'middleware/AuthMiddleware.php';

// Ambil URL yang diminta
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Buang base path jika perlu (misalnya /api)
$path = str_replace('/api', '', $path);

// Routing berdasarkan path dan method
try {
    switch (true) {
        // Endpoint login
        case $path === '/auth/login' && $method === 'POST':
            $controller = new AuthController();
            $controller->login();
            break;
        
        // Endpoint untuk mengambil data user yang sedang login
        case $path === '/auth/me' && $method === 'GET':
            AuthMiddleware::authenticate();
            $controller = new AuthController();
            $controller->me();
            break;
        
        // Ambil semua data mahasiswa
        case $path === '/students' && $method === 'GET':
            AuthMiddleware::authenticate();
            $controller = new StudentController();
            $controller->index();
            break;
        
        // Ambil detail mahasiswa berdasarkan ID
        case preg_match('/^\/students\/(\d+)$/', $path, $matches) && $method === 'GET':
            AuthMiddleware::authenticate();
            $controller = new StudentController();
            $controller->show($matches[1]);
            break;
        
        // Tambah data mahasiswa baru
        case $path === '/students' && $method === 'POST':
            AuthMiddleware::authenticate();
            $controller = new StudentController();
            $controller->store();
            break;
        
        // Update data mahasiswa berdasarkan ID
        case preg_match('/^\/students\/(\d+)$/', $path, $matches) && $method === 'PUT':
            AuthMiddleware::authenticate();
            $controller = new StudentController();
            $controller->update($matches[1]);
            break;
        
        // Hapus data mahasiswa berdasarkan ID
        case preg_match('/^\/students\/(\d+)$/', $path, $matches) && $method === 'DELETE':
            AuthMiddleware::authenticate();
            $controller = new StudentController();
            $controller->destroy($matches[1]);
            break;
        
        // Jika route tidak ditemukan
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Route tidak ditemukan']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
