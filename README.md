# UAS rest API Backend Smt 4 CA 234

## Anggota Kelompok

-   I Nyoman Wisnawa Adi (230040112)
-   Kadek Jovian Krisnantara (230040120)
-   Ketut Suardika (230040188)

## Fitur

- CRUD untuk data mahasiswa
- Autentikasi login menggunakan token
- Superadmin otomatis tersedia setelah SQL diimpor
- Token sederhana yang disimpan di database

## Struktur Database

- **kelas**: Menyimpan data kelas
- **mahasiswa**: Menyimpan data mahasiswa dengan relasi ke kelas
- **users**: Menyimpan data user untuk autentikasi

## Setup

### 1. Clone dari github / atau bisa didownload lewat zip

```bash
git clone https://github.com/Wiszx/uas-restApi.git
```

### 2. Setup Database

- Cari file `script.sql` di dalam folder `UAS_restAPI`
- Lalu import ke `phpmyadmin` atau ke aplikasi database MySql lainnya
- Bisa langsung import atau salin kodenya lalu tempelkan

### 3. Konfigurasi Database

Edit file `config/database.php` sesuai dengan konfigurasi (biasanya password):

```php
private $host = '127.0.0.1';
private $db_name = 'db_stikom';
private $username = 'root';
private $password = '';
```

### 4. Setup Web Server

#### A. Menggunakan Apache:
- Copy semua file ke folder `htdocs` (XAMPP) atau (MAMP)
- Pastikan mod_rewrite aktif
- File `.htaccess` sudah disertakan

#### B. Menggunakan PHP Built-in Server:
```bash
php -S localhost:8000
```

## Endpoint API

### Base URL
```
http://localhost  # Jika menggunakan Apache
http://localhost:8000  # Jika menggunakan PHP built-in server
```

## Test API di Postman

### 1. Login
- Method: `POST`
- end point: `/api/auth/login` # contoh http://localhost:8000/api/auth/login , dan seterusnya
- Headers
  - Tambahkan header baru:
  - Key: `Content-Type`
  - Value: `application/json`
- Body
  - `raw` JSON
  - masukan data ini:
```json
{
    "username": "admin",
    "password": "admin123"
}
```
- Klik tombol `Send`
  - Jika berhasil, akan menerima token JWT, contoh output:
```json
{
    "message": "Login berhasil",
    "token": "c6f041e98ad9d89cd0681fffc8f934d39afe339dfea3339cdb82e7f1d02d75d0",
    "user": {
        "id": "1",
        "username": "admin",
        "role": "superadmin"
    }
}
```

### 2. Test Endpoint yang Memerlukan Authentication
- Method: `GET`
- end point: `/api/auth/me`
- Headers
  - Tambahkan header baru:
  - Key: `Authorization`
  - Value: `Bearer {token}` # ada space setelah Bearer
- Body 
 - Kosongkan karena GET
- Klik tombol `Send`

### 3. Test GET All Students
- Method: `GET`
- end point: `/api/students`
- Headers
  - Tetap pakai header `Authorization` yang sama:
  - Key: `Authorization`
  - Value: `Bearer {token}` # ada space setelah Bearer
- Body 
 - Kosongkan karena GET
- Klik tombol `Send`

### 4. Test POST - Create New Student
- Method: `POST`
- end point: `/api/students`
- Headers
  - Pastikan masih ada header `Authorization` yang sama
  - Tambahkan headers baru:
    - Key: `Content-Type`
    - Value: `application/json`
- Body
  - `raw` JSON
  - masukan data mahasiswa baru:
```json
{
    "nim": "230040129",
    "nama_lengkap": "Mahasiswa Testing",
    "alamat": "Jl. Testing No. 123, Denpasar",
    "id_kelas": 1
}
```
- Klik tombol `Send`

### 5. Test GET Student by ID
- Method: `GET`
- end point: `/api/students/1`
- Headers
  - Tetap pakai header `Authorization` yang sama:
  - Pastikan ada header `Content-Type:` `application/json`
- Body 
 - Kosongkan karena GET
- Klik tombol `Send`

### 6. Test PUT - Update Student
- Method: `PUT`
- end point: `/api/students/1`
- Headers
  - Tetap pakai header `Authorization` yang sama:
  - Pastikan ada header `Content-Type:` `application/json`
- Body
  - `raw` JSON
  - masukan data update :
```json
{
    "nim": "230040111",
    "nama_lengkap": "I Nyoman Wisnawa Update",
    "alamat": "Jl. Testing No. 123, Denpasar",
    "id_kelas": 3
}
```
- Klik tombol `Send`

### 7. Test DELETE - Hapus Student
- Method: `DELETE`
- end point: `/api/students/4`
- Headers
  - Tetap pakai header `Authorization` yang sama:
  - Pastikan ada header `Content-Type:` `application/json`
- Body 
 - Kosongkan (karena DELETE request)
- Klik tombol `Send`
