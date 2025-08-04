<?php
require_once 'auth.php';

require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Ambil nama file foto sebelum dihapus dari DB
    $sql_select = "SELECT nama_file FROM galeri WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($row = $result->fetch_assoc()) {
            $nama_file = $row['nama_file'];
            if (!empty($nama_file) && file_exists('../assets/images/galeri/' . $nama_file)) {
                unlink('../assets/images/galeri/' . $nama_file);
            }
        }
        $stmt_select->close();
    }

    // Hapus record dari database
    $sql_delete = "DELETE FROM galeri WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Foto berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus foto.";
        }
        $stmt_delete->close();
    }
}

$conn->close();
header("Location: kelola-galeri.php");
exit();
?>