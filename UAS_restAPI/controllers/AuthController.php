<?php
class AuthController {
    private $db;
    
    public function __construct() {
        // Ambil koneksi database dari variabel global
        global $db;
        $this->db = $db;
    }
    
    public function login() {
        // Ambil input dari body request
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username dan password wajib diisi']);
            return;
        }
        
        $username = $data['username'];
        $password = $data['password'];
        
        // Hash password (harus sama dengan yang disimpan di database)
        $hashedPassword = hash('sha256', $password);
        
        // Cek user di database
        $query = "SELECT * FROM users WHERE username = :username AND password = :password";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Generate token random
            $token = bin2hex(random_bytes(32));
            
            // Simpan token ke database
            $updateQuery = "UPDATE users SET token = :token WHERE id = :id";
            $updateStmt = $this->db->prepare($updateQuery);
            $updateStmt->bindParam(':token', $token);
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();
            
            http_response_code(200);
            echo json_encode([
                'message' => 'Login berhasil',
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'role' => $user['role']
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Username atau password salah']);
        }
    }
    
    public function me() {
        // Ambil token dari header Authorization
        $token = $this->getTokenFromHeader();
        
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Token tidak ditemukan']);
            return;
        }
        
        // Cari user berdasarkan token
        $query = "SELECT id, username, role FROM users WHERE token = :token";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode([
                'user' => $user
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Token tidak valid']);
        }
    }
    
    private function getTokenFromHeader() {
        // Ambil semua header
        $headers = getallheaders();
        
        // Ambil token dari header Authorization
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        
        return null;
    }
}
?>
