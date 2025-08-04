<?php
require_once 'auth.php';

$pageTitle = "Kelola Berita";
$currentPage = "berita";

require_once '../includes/config.php';

$edit_mode = false;
$id = $judul = $kategori = $isi = $gambar_lama = '';

// Cek apakah ini mode edit (ada ID di URL)
if (isset($_GET['id'])) {
    $edit_mode = true;
    $id = (int)$_GET['id'];

    // Ambil data yang ada dari database
    $sql = "SELECT * FROM berita WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $judul = $row['judul'];
            $kategori = $row['kategori'];
            $isi = $row['isi'];
            $gambar_lama = $row['gambar'];
        } else {
            $_SESSION['error_message'] = "Berita tidak ditemukan.";
            header("Location: kelola-berita.php");
            exit();
        }
        $stmt->close();
    }
}

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['title'];
    $kategori = $_POST['category'];
    $isi = $_POST['content'];
    $id_to_update = $_POST['id'] ?? null;
    $gambar = $_POST['gambar_lama'] ?? '';

    // Proses upload gambar baru jika ada
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] == 0) {
        // Hapus gambar lama jika ada gambar baru yang diupload
        if (!empty($gambar) && file_exists('../assets/images/berita/' . $gambar)) {
            unlink('../assets/images/berita/' . $gambar);
        }
        $target_dir = "../assets/images/berita/";
        $gambar = time() . '_' . basename($_FILES["image_upload"]["name"]);
        $target_file = $target_dir . $gambar;
        if (!move_uploaded_file($_FILES["image_upload"]["tmp_name"], $target_file)) {
            $_SESSION['error_message'] = "Gagal mengunggah gambar baru.";
            header("Location: form-berita.php?id=$id_to_update"); exit();
        }
    }
    
    // Cek apakah ini proses UPDATE atau INSERT
    if ($id_to_update) {
        // UPDATE data yang ada
        $sql = "UPDATE berita SET judul = ?, kategori = ?, isi = ?, gambar = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $judul, $kategori, $isi, $gambar, $id_to_update);
        $pesan_sukses = "Berita berhasil diperbarui!";
    } else {
        // INSERT data baru
        $sql = "INSERT INTO berita (judul, kategori, isi, gambar) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $judul, $kategori, $isi, $gambar);
        $pesan_sukses = "Berita berhasil ditambahkan!";
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = $pesan_sukses;
    } else {
        $_SESSION['error_message'] = "Operasi gagal: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header("Location: kelola-berita.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $edit_mode ? 'Edit' : 'Tambah' ?> Berita - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen bg-gray-100">
        <?php include '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include '../includes/header_admin.php'; ?>
    
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
                <div class="bg-white p-8 rounded-lg shadow-lg max-w-3xl mx-auto">
                    <form action="form-berita.php" method="POST" class="space-y-6" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                        <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($gambar_lama) ?>">
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Berita</label>
                            <input type="text" id="title" name="title" value="<?= htmlspecialchars($judul) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                            <select id="category" name="category" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="Prestasi" <?= $kategori == 'Prestasi' ? 'selected' : '' ?>>Prestasi</option>
                                <option value="Kegiatan" <?= $kategori == 'Kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                                <option value="Event" <?= $kategori == 'Event' ? 'selected' : '' ?>>Event</option>
                                <option value="Pengumuman" <?= $kategori == 'Pengumuman' ? 'selected' : '' ?>>Pengumuman</option>
                            </select>
                        </div>

                        <div>
                            <label for="image_upload" class="block text-sm font-medium text-gray-700 mb-1">Gambar Utama <?= !$edit_mode ? '<span class="text-red-500">*</span>' : '' ?></label>
                            <?php if ($edit_mode && !empty($gambar_lama)): ?>
                                <div class="my-2">
                                    <p class="text-xs text-gray-500">Gambar saat ini:</p>
                                    <img src="../assets/images/berita/<?= htmlspecialchars($gambar_lama) ?>" alt="Gambar lama" class="w-40 h-auto rounded-md border bg-gray-100">
                                    <p class="text-xs text-gray-500 mt-2">Pilih file baru jika ingin mengganti gambar.</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" id="image_upload" name="image_upload" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" <?= !$edit_mode ? 'required' : '' ?>>
                        </div>

                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Isi Berita</label>
                            <textarea id="content" name="content" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500" required><?= htmlspecialchars($isi) ?></textarea>
                        </div>

                        <div class="flex justify-end space-x-4 pt-4 border-t">
                            <a href="kelola-berita.php" class="bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-md hover:bg-gray-300 transition">Batal</a>
                            <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-md hover:bg-blue-700 transition">Simpan</button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>