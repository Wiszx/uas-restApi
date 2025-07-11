-- Buat database
CREATE DATABASE IF NOT EXISTS db_stikom;
USE db_stikom;

-- Tabel kelas
CREATE TABLE IF NOT EXISTS kelas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_kelas VARCHAR(10) NOT NULL UNIQUE,
    nama_kelas VARCHAR(50) NOT NULL
);

-- Tabel mahasiswa
CREATE TABLE IF NOT EXISTS mahasiswa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL UNIQUE,
    nama_lengkap VARCHAR(100) NOT NULL,
    alamat TEXT NOT NULL,
    id_kelas INT NOT NULL,
    FOREIGN KEY (id_kelas) REFERENCES kelas(id)
);

-- Tabel users untuk autentikasi
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, 
    token VARCHAR(255) NULL, 
    role VARCHAR(20) NOT NULL DEFAULT 'user' 
);

-- Insert superadmin otomatis
INSERT INTO users (username, password, role) VALUES ('admin', SHA2('admin123', 256), 'superadmin');

-- Insert sample data kelas
INSERT INTO kelas (kode_kelas, nama_kelas) VALUES 
('SI', 'Sistem Informasi'),
('SK', 'Sistem Komputer'),
('TI', 'Teknologi Informasi'),
('BD', 'Bisnis Digital'),
('MI', 'Manajemen Informatika');

-- Insert sample data mahasiswa
INSERT INTO mahasiswa (nim, nama_lengkap, alamat, id_kelas) VALUES 
('230040112', 'I Nyoman Wisnawa Adi', 'Jl. Merdeka No. 123, Badung', 1),
('230040113', 'Jovian', 'Jl. Sudirman No. 456, Denpasar', 2),
('230040114', 'Ketut Suardika', 'Jl. Gatot Subroto No. 789, Buleleng', 3),
('230040115', 'Adi', 'Jl. Thamrin No. 321, Klungkung', 3);