<?php
require_once 'auth.php';
require_once '../includes/config.php';

$edit_mode = false;
$id = $nama = $nip = $foto_lama = '';

// Cek jika mode edit
if (isset($_GET['id'])) {
    $edit_mode = true;
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM guru WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $nama = $row['nama'];
            $nip = $row['nip'];
            $foto_lama = $row['foto'];
        } else {
            header("Location: kelola-guru.php"); exit();
        }
        $stmt->close();
    }
}

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['full_name'];
    $nip = $_POST['nip'];
    $id_to_update = $_POST['id'] ?? null;
    $foto = $_POST['foto_lama'] ?? '';

    if (isset($_FILES['photo_upload']) && $_FILES['photo_upload']['error'] == 0) {
        if (!empty($foto) && file_exists('../assets/images/guru/' . $foto)) {
            unlink('../assets/images/guru/' . $foto);
        }
        $target_dir = "../assets/images/guru/";
        $foto = time() . '_' . basename($_FILES["photo_upload"]["name"]);
        $target_file = $target_dir . $foto;
        move_uploaded_file($_FILES["photo_upload"]["tmp_name"], $target_file);
    }

    if ($id_to_update) {
        // UPDATE data
        $sql = "UPDATE guru SET nama = ?, nip = ?, foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama, $nip, $foto, $id_to_update);
        $pesan_sukses = "Data berhasil diperbarui!";
    } else {
        // INSERT data baru
        $sql = "INSERT INTO guru (nama, nip, foto) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nama, $nip, $foto);
        $pesan_sukses = "Data berhasil ditambahkan!";
    }

    if ($stmt->execute()) { $_SESSION['success_message'] = $pesan_sukses; } 
    else { $_SESSION['error_message'] = "Operasi gagal: " . $stmt->error; }
    $stmt->close();
    $conn->close();
    header("Location: kelola-guru.php");
    exit();
}

$pageTitle = $edit_mode ? 'Edit Data Guru' : 'Tambah Data Guru';
$currentPage = "guru";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $pageTitle ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php require_once '../includes/sidebar_admin.php'; ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once '../includes/header_admin.php'; ?>
        <main class="flex-1 p-8">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
                <form action="form-guru.php" method="POST" class="space-y-6" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($foto_lama) ?>">
                    <div>
                        <label for="full_name">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($nama) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="nip">NIP (Nomor Induk Pegawai)</label>
                        <input type="text" id="nip" name="nip" value="<?= htmlspecialchars($nip) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="photo_upload">Foto</label>
                        <?php if ($edit_mode && !empty($foto_lama)): ?>
                            <img src="../assets/images/guru/<?= htmlspecialchars($foto_lama) ?>" class="w-24 h-24 rounded-full object-cover my-2">
                        <?php endif; ?>
                        <input type="file" id="photo_upload" name="photo_upload" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div class="flex justify-end space-x-4 pt-4 border-t">
                        <a href="kelola-guru.php" class="bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>