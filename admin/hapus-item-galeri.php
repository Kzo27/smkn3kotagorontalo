<?php
// PERBAIKAN: session_start() dihapus karena sudah ada di auth.php
require_once 'auth.php';
require_once '../includes/config.php';

$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$album_id = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;

if ($item_id > 0 && $album_id > 0) {
    // PERBAIKAN: Ambil tipe dan path_or_url untuk pengecekan
    $stmt_select = $conn->prepare("SELECT tipe, path_or_url FROM foto_galeri WHERE id = ?");
    $stmt_select->bind_param("i", $item_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($item = $result->fetch_assoc()) {
        // PERBAIKAN: Hapus file fisik HANYA JIKA tipenya adalah 'foto'
        if ($item['tipe'] == 'foto' && !empty($item['path_or_url'])) {
            $file_path = '../assets/images/galeri/' . $item['path_or_url'];
            if (file_exists($file_path)) {
                // Hapus file dari server
                unlink($file_path);
            }
        }
        // Jika tipenya 'video', tidak ada file fisik yang perlu dihapus.
    }
    $stmt_select->close();

    // Hapus record dari database (berlaku untuk foto dan video)
    $stmt_delete = $conn->prepare("DELETE FROM foto_galeri WHERE id = ?");
    $stmt_delete->bind_param("i", $item_id);
    if ($stmt_delete->execute()) {
        // PERBAIKAN: Pesan dibuat lebih umum
        $_SESSION['success_message'] = "Item dari galeri berhasil dihapus.";
    } else {
        $_SESSION['error_message'] = "Gagal menghapus item dari galeri.";
    }
    $stmt_delete->close();
}

$conn->close();
// Kembali ke halaman galeri dari album yang bersangkutan
header("Location: kelola-galeri.php?album_id=" . $album_id);
exit();
?>