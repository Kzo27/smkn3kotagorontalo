<?php
require_once 'auth.php';

require_once '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kepsek_nama = $_POST['kepsek_nama'];
    $kepsek_sambutan = $_POST['kepsek_sambutan'];
    $kepsek_foto_lama = $_POST['kepsek_foto_lama'];

    function handle_upload($file_input_name, $old_filename) {
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
            if (!empty($old_filename) && file_exists('../assets/images/profil/' . $old_filename)) {
                unlink('../assets/images/profil/' . $old_filename);
            }
            $target_dir = "../assets/images/profil/"; // Folder upload tetap sama
            $new_filename = time() . '_' . basename($_FILES[$file_input_name]["name"]);
            move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_dir . $new_filename);
            return $new_filename;
        }
        return $old_filename;
    }

    $kepsek_foto_baru = handle_upload('kepsek_foto', $kepsek_foto_lama);

    // FIX: Query sekarang meng-UPDATE tabel `sambutan_kepsek`
    $sql = "UPDATE sambutan_kepsek SET nama = ?, sambutan = ?, foto = ? WHERE id = 1";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $kepsek_nama, $kepsek_sambutan, $kepsek_foto_baru);
        if($stmt->execute()) {
            $_SESSION['success_message'] = "Sambutan Kepala Sekolah berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data.";
        }
        $stmt->close();
    }
    header("Location: kelola-kepsek.php");
    exit();
}
?>