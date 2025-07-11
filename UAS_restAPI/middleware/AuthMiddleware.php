<?php
class AuthMiddleware {
    public static function authenticate() {
        global $db;
        
        // Ambil token dari header Authorization
        $token = self::getTokenFromHeader();
        
        if (!$token) {
            http_response_code(401);
            echo json_encode(['error' => 'Token tidak tersedia']);
            exit();
        }
        
        // Cek token di database
        $query = "SELECT id, username, role FROM users WHERE token = :token";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        
        if ($stmt->rowCount() === 0) {
            http_response_code(401);
            echo json_encode(['error' => 'Token tidak valid']);
            exit();
        }
        
        // Simpan data user ke session untuk keperluan selanjutnya (opsional)
        $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
        return true;
    }
    
    // Ambil token dari header Authorization
    private static function getTokenFromHeader() {
        $headers = getallheaders();
        
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
