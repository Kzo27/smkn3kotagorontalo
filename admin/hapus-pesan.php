<?php
require_once 'auth.php';

require_once '../includes/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $sql = "DELETE FROM pesan_kontak WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pesan berhasil dihapus!";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus pesan.";
        }
        $stmt->close();
    }
}

$conn->close();
header("Location: kelola-pesan.php");
exit();
?>