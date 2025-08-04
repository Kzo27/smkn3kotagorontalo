<?php
// Memulai atau melanjutkan sesi yang sudah ada
session_start();

// Cek apakah variabel sesi 'admin_logged_in' tidak ada atau tidak bernilai true
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Jika tidak, arahkan pengguna ke halaman login
    header("location: login.php");
    exit; // Pastikan untuk menghentikan eksekusi script setelah redirect
}
?>
