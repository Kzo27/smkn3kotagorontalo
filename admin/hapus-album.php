<?php
session_start();
require_once 'auth.php';
require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $album_id = (int)$_GET['id'];

    // LANGKAH 1: Ambil semua nama file foto dari album yang akan dihapus
    $sql_select_fotos = "SELECT nama_file FROM foto_galeri WHERE album_id = ?";
    if ($stmt_select = $conn->prepare($sql_select_fotos)) {
        $stmt_select->bind_param("i", $album_id);
        $stmt_select->execute();
        $result_fotos = $stmt_select->get_result();

        // LANGKAH 2: Hapus setiap file foto dari server
        while ($foto = $result_fotos->fetch_assoc()) {
            $file_path = '../assets/images/galeri/' . $foto['nama_file'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        $stmt_select->close();
    }

    // LANGKAH 3: Hapus record album dari tabel `album`
    // Karena kita menggunakan ON DELETE CASCADE, semua record di `foto_galeri`
    // yang berhubungan dengan album ini akan otomatis terhapus juga.
    $sql_delete_album = "DELETE FROM album WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete_album)) {
        $stmt_delete->bind_param("i", $album_id);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Album dan semua fotonya berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus album.";
        }
        $stmt_delete->close();
    }
}

$conn->close();
header("Location: kelola-album.php");
exit();
?>