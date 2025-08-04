<?php
require_once 'auth.php';

require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Ambil nama file foto sebelum dihapus
    $sql_select = "SELECT foto FROM fasilitas WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        if ($row = $result->fetch_assoc()) {
            $foto_lama = $row['foto'];
            if (!empty($foto_lama) && file_exists('../assets/images/fasilitas/' . $foto_lama)) {
                unlink('../assets/images/fasilitas/' . $foto_lama);
            }
        }
        $stmt_select->close();
    }

    // Hapus record dari database
    $sql_delete = "DELETE FROM fasilitas WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);
        if ($stmt_delete->execute()) {
            $_SESSION['success_message'] = "Data fasilitas berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus data.";
        }
        $stmt_delete->close();
    }
}

$conn->close();
header("Location: kelola-fasilitas.php");
exit();
?>