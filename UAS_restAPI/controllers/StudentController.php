<?php
class StudentController {
    private $db;
    
    public function __construct() {
        // Ambil koneksi database dari variabel global
        global $db;
        $this->db = $db;
    }
    
    // GET /api/students - Ambil semua data mahasiswa
    public function index() {
        $query = "SELECT m.*, k.nama_kelas 
                  FROM mahasiswa m 
                  LEFT JOIN kelas k ON m.id_kelas = k.id 
                  ORDER BY m.id DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        $students = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $students[] = $row;
        }
        
        http_response_code(200);
        echo json_encode([
            'message' => 'Data mahasiswa berhasil diambil',
            'data' => $students
        ]);
    }
    
    // GET /api/students/{id} - Ambil detail mahasiswa berdasarkan ID
    public function show($id) {
        $query = "SELECT m.*, k.nama_kelas 
                  FROM mahasiswa m 
                  LEFT JOIN kelas k ON m.id_kelas = k.id 
                  WHERE m.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            http_response_code(200);
            echo json_encode([
                'message' => 'Data mahasiswa ditemukan',
                'data' => $student
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Mahasiswa tidak ditemukan']);
        }
    }
    
    // POST /api/students - Tambah data mahasiswa baru
    public function store() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['nim']) || !isset($data['nama_lengkap']) || 
            !isset($data['alamat']) || !isset($data['id_kelas'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Semua field wajib diisi (nim, nama_lengkap, alamat, id_kelas)']);
            return;
        }
        
        // Cek apakah ID kelas valid
        $kelasQuery = "SELECT id FROM kelas WHERE id = :id_kelas";
        $kelasStmt = $this->db->prepare($kelasQuery);
        $kelasStmt->bindParam(':id_kelas', $data['id_kelas']);
        $kelasStmt->execute();
        
        if ($kelasStmt->rowCount() === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Kelas tidak ditemukan']);
            return;
        }
        
        $query = "INSERT INTO mahasiswa (nim, nama_lengkap, alamat, id_kelas) 
                  VALUES (:nim, :nama_lengkap, :alamat, :id_kelas)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nim', $data['nim']);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':id_kelas', $data['id_kelas']);
        
        if ($stmt->execute()) {
            $lastId = $this->db->lastInsertId();
            http_response_code(201);
            echo json_encode([
                'message' => 'Mahasiswa berhasil ditambahkan',
                'data' => [
                    'id' => $lastId,
                    'nim' => $data['nim'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'alamat' => $data['alamat'],
                    'id_kelas' => $data['id_kelas']
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menambahkan mahasiswa']);
        }
    }
    
    // PUT /api/students/{id} - Update data mahasiswa
    public function update($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['nim']) || !isset($data['nama_lengkap']) || 
            !isset($data['alamat']) || !isset($data['id_kelas'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Semua field wajib diisi (nim, nama_lengkap, alamat, id_kelas)']);
            return;
        }
        
        // Cek apakah mahasiswa dengan ID tersebut ada
        $checkQuery = "SELECT id FROM mahasiswa WHERE id = :id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Mahasiswa tidak ditemukan']);
            return;
        }
        
        // Cek apakah ID kelas valid
        $kelasQuery = "SELECT id FROM kelas WHERE id = :id_kelas";
        $kelasStmt = $this->db->prepare($kelasQuery);
        $kelasStmt->bindParam(':id_kelas', $data['id_kelas']);
        $kelasStmt->execute();
        
        if ($kelasStmt->rowCount() === 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Kelas tidak ditemukan']);
            return;
        }
        
        $query = "UPDATE mahasiswa 
                  SET nim = :nim, nama_lengkap = :nama_lengkap, alamat = :alamat, id_kelas = :id_kelas 
                  WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nim', $data['nim']);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':id_kelas', $data['id_kelas']);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                'message' => 'Data mahasiswa berhasil diupdate',
                'data' => [
                    'id' => $id,
                    'nim' => $data['nim'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'alamat' => $data['alamat'],
                    'id_kelas' => $data['id_kelas']
                ]
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal mengupdate data mahasiswa']);
        }
    }
    
    // DELETE /api/students/{id} - Hapus data mahasiswa
    public function destroy($id) {
        // Cek apakah mahasiswa ada
        $checkQuery = "SELECT id FROM mahasiswa WHERE id = :id";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() === 0) {
            http_response_code(404);
            echo json_encode(['error' => 'Mahasiswa tidak ditemukan']);
            return;
        }
        
        $query = "DELETE FROM mahasiswa WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['message' => 'Mahasiswa berhasil dihapus']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menghapus mahasiswa']);
        }
    }
}
?>
