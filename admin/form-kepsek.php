<?php
require_once 'auth.php';

require_once '../includes/config.php';

$edit_mode = false;
$id = $nama = $periode_jabatan = $foto_lama = '';

// Cek jika mode edit
if (isset($_GET['id'])) {
    $edit_mode = true;
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM sejarah_kepsek WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $nama = $row['nama'];
            $periode_jabatan = $row['periode_jabatan'];
            $foto_lama = $row['foto'];
        } else {
            header("Location: kelola-kepsek.php"); exit();
        }
        $stmt->close();
    }
}

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $periode_jabatan = $_POST['periode_jabatan'];
    $id_to_update = $_POST['id'] ?? null;
    $foto = $_POST['foto_lama'] ?? '';

    // Proses upload foto baru jika ada
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        if (!empty($foto) && file_exists('../assets/images/kepsek/' . $foto)) {
            unlink('../assets/images/kepsek/' . $foto);
        }
        $target_dir = "../assets/images/kepsek/";
        $foto = time() . '_' . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $foto;
        if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $_SESSION['error_message'] = "Gagal mengunggah foto baru.";
            header("Location: form-kepsek.php?id=$id_to_update"); exit();
        }
    }

    if ($id_to_update) {
        // UPDATE data
        $sql = "UPDATE sejarah_kepsek SET nama = ?, periode_jabatan = ?, foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama, $periode_jabatan, $foto, $id_to_update);
        $pesan_sukses = "Data berhasil diperbarui!";
    } else {
        // INSERT data baru
        $sql = "INSERT INTO sejarah_kepsek (nama, periode_jabatan, foto) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nama, $periode_jabatan, $foto);
        $pesan_sukses = "Data berhasil ditambahkan!";
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = $pesan_sukses;
    } else {
        $_SESSION['error_message'] = "Operasi gagal: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header("Location: kelola-kepsek.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $edit_mode ? 'Edit' : 'Tambah' ?> Sejarah Kepsek - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php $currentPage = 'kepsek'; require_once '../includes/sidebar_admin.php'; ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php $pageTitle = $edit_mode ? 'Edit Sejarah Kepala Sekolah' : 'Tambah Sejarah Kepala Sekolah'; require_once '../includes/header_admin.php'; ?>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
                <form action="form-kepsek.php" method="POST" class="space-y-6" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($foto_lama) ?>">

                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="periode_jabatan" class="block text-sm font-medium text-gray-700 mb-1">Periode Jabatan</label>
                        <input type="text" id="periode_jabatan" name="periode_jabatan" value="<?= htmlspecialchars($periode_jabatan) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" placeholder="Contoh: 2010 - 2015" required>
                    </div>
                    <div>
                        <label for="foto" class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
                        <?php if ($edit_mode && !empty($foto_lama)): ?>
                            <img src="../assets/images/kepsek/<?= htmlspecialchars($foto_lama) ?>" alt="Foto saat ini" class="w-24 h-24 rounded-full object-cover mb-2">
                        <?php endif; ?>
                        <input type="file" id="foto" name="foto" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    <div class="flex justify-end space-x-4 pt-4 border-t">
                        <a href="kelola-kepsek.php" class="bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded hover:bg-gray-300">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>