<?php
require_once 'auth.php';

require_once '../includes/config.php';

$edit_mode = false;
$id = $nama = $deskripsi = $foto_lama = '';

// Cek jika mode edit
if (isset($_GET['id'])) {
    $edit_mode = true;
    $id = (int)$_GET['id'];
    $sql = "SELECT * FROM fasilitas WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $nama = $row['nama'];
            $deskripsi = $row['deskripsi'];
            $foto_lama = $row['foto'];
        } else {
            $_SESSION['error_message'] = "Data fasilitas tidak ditemukan.";
            header("Location: kelola-fasilitas.php");
            exit();
        }
        $stmt->close();
    }
}

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['facility_name'];
    $deskripsi = $_POST['facility_description'];
    $id_to_update = $_POST['id'] ?? null;
    $foto = $_POST['foto_lama'] ?? '';

    // Proses upload foto baru jika ada
    if (isset($_FILES['facility_photo']) && $_FILES['facility_photo']['error'] == 0) {
        if (!empty($foto) && file_exists('../assets/images/fasilitas/' . $foto)) {
            unlink('../assets/images/fasilitas/' . $foto);
        }
        $target_dir = "../assets/images/fasilitas/";
        $foto = time() . '_' . basename($_FILES["facility_photo"]["name"]);
        $target_file = $target_dir . $foto;
        if (!move_uploaded_file($_FILES["facility_photo"]["tmp_name"], $target_file)) {
            $_SESSION['error_message'] = "Gagal mengunggah foto baru.";
            header("Location: form-fasilitas.php?id=$id_to_update"); exit();
        }
    }

    if ($id_to_update) {
        // UPDATE data
        $sql = "UPDATE fasilitas SET nama = ?, deskripsi = ?, foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nama, $deskripsi, $foto, $id_to_update);
        $pesan_sukses = "Data fasilitas berhasil diperbarui!";
    } else {
        // INSERT data baru
        $sql = "INSERT INTO fasilitas (nama, deskripsi, foto) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nama, $deskripsi, $foto);
        $pesan_sukses = "Data fasilitas berhasil ditambahkan!";
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = $pesan_sukses;
    } else {
        $_SESSION['error_message'] = "Operasi gagal: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header("Location: kelola-fasilitas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_mode ? 'Edit' : 'Tambah' ?> Fasilitas - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php $currentPage = 'fasilitas'; require_once '../includes/sidebar_admin.php'; ?>
    
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php $pageTitle = $edit_mode ? 'Edit Fasilitas' : 'Tambah Fasilitas Baru'; require_once '../includes/header_admin.php'; ?>
        
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
                <form action="form-fasilitas.php" method="POST" class="space-y-6" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="foto_lama" value="<?= htmlspecialchars($foto_lama) ?>">

                    <div>
                        <label for="facility_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Fasilitas</label>
                        <input type="text" id="facility_name" name="facility_name" value="<?= htmlspecialchars($nama) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                    </div>
                    <div>
                        <label for="facility_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat</label>
                        <textarea id="facility_description" name="facility_description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md" required><?= htmlspecialchars($deskripsi) ?></textarea>
                    </div>
                    <div>
                        <label for="facility_photo" class="block text-sm font-medium text-gray-700 mb-1">Foto Fasilitas</label>
                        <?php if ($edit_mode && !empty($foto_lama)): ?>
                            <img src="../assets/images/fasilitas/<?= htmlspecialchars($foto_lama) ?>" alt="Foto saat ini" class="w-full max-w-xs h-auto rounded-md border bg-gray-100 mb-2">
                        <?php endif; ?>
                        <input type="file" id="facility_photo" name="facility_photo" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" <?= !$edit_mode ? 'required' : '' ?>>
                    </div>
                    <div class="flex justify-end space-x-4 pt-4 border-t">
                        <a href="kelola-fasilitas.php" class="bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded hover:bg-gray-300">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded hover:bg-blue-700">Simpan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>