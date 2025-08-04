<?php
require_once 'auth.php';

require_once '../includes/config.php';

// Cek apakah ada ID yang dikirim melalui URL
if (isset($_GET['id'])) {
    $id = (int)$_GET['id']; // Ambil ID dan pastikan itu adalah integer

    // LANGKAH 1: Ambil nama file gambar dari database SEBELUM menghapus recordnya
    $sql_select_image = "SELECT gambar FROM berita WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select_image)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($row = $result->fetch_assoc()) {
            $gambar_lama = $row['gambar'];
            // Hapus file gambar dari folder jika ada
            if (!empty($gambar_lama) && file_exists('../assets/images/berita/' . $gambar_lama)) {
                unlink('../assets/images/berita/' . $gambar_lama);
            }
        }
        $stmt_select->close();
    }

    // LANGKAH 2: Hapus record berita dari database
    $sql_delete = "DELETE FROM berita WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);

        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Berita berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus berita.";
        }
        $stmt_delete->close();
    }
} else {
    $_SESSION['error_message'] = "ID berita tidak valid.";
}

$conn->close();
// Arahkan kembali ke halaman kelola berita
header("Location: kelola-berita.php");
exit();
?>