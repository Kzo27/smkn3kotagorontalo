<?php
// 1. Mulai sesi
session_start();

// 2. Hapus semua variabel sesi
$_SESSION = array();

// 3. Hancurkan sesi
session_destroy();

// 4. Arahkan kembali ke halaman login
header("location: login.php");
exit;
?>