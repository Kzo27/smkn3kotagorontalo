<?php
// Pengaturan zona waktu
date_default_timezone_set('Asia/Makassar');

// --- PENGATURAN KONEKSI DATABASE ---

// Nama host atau server database
// Untuk lingkungan lokal (XAMPP), biasanya 'localhost'
define('DB_HOST', 'localhost');

// Username untuk koneksi ke database
// Default untuk XAMPP adalah 'root'
define('DB_USER', 'root');

// Password untuk koneksi ke database
// Default untuk XAMPP biasanya kosong
define('DB_PASS', '');

// Nama database yang sudah Anda buat
define('DB_NAME', 'db_sekolah_telaga');


// --- PROSES KONEKSI ---

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Memeriksa apakah koneksi berhasil atau gagal
if ($conn->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi script dan tampilkan pesan error
    die("Koneksi ke database gagal: " . $conn->connect_error);
} 

// Mengatur set karakter koneksi menjadi utf8mb4 untuk mendukung berbagai karakter
$conn->set_charset("utf8mb4");

?>
