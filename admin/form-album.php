<?php
require_once 'auth.php';
require_once '../includes/config.php';

$edit_mode = false;
$id = $judul = '';

// Cek jika ini mode edit (ada ID di URL)
if (isset($_GET['id'])) {
    $edit_mode = true;
    $id = (int)$_GET['id'];

    $stmt_select = $conn->prepare("SELECT * FROM album WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    if ($row = $result->fetch_assoc()) {
        $judul = $row['judul'];
        
    } else {
        header("Location: kelola-album.php"); // Jika ID tidak ditemukan
        exit();
    }
    $stmt_select->close();
}

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $id_to_update = $_POST['id'] ?? null;

    if ($id_to_update) {
        // UPDATE data album yang ada
        $sql = "UPDATE album SET judul = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $judul,  $id_to_update);
        $pesan_sukses = "Album berhasil diperbarui!";
    } else {
        // INSERT album baru
        $sql = "INSERT INTO album (judul) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $judul, );
        $pesan_sukses = "Album baru berhasil dibuat!";
    }

    if ($stmt->execute()) {
        $_SESSION['success_message'] = $pesan_sukses;
    } else {
        $_SESSION['error_message'] = "Operasi gagal: " . $stmt->error;
    }
    $stmt->close();
    header("Location: kelola-album.php");
    exit();
}

// Set variabel untuk template
$pageTitle = $edit_mode ? 'Edit Album' : 'Buat Album Baru';
$currentPage = "galeri";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    
    <?php require_once '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        
        <?php require_once '../includes/header_admin.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-2xl mx-auto">
                <h2 class="text-2xl font-bold text-gray-800 mb-6"><?= $pageTitle ?></h2>
                <form action="form-album.php" method="POST" class="space-y-6">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    
                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700 mb-1">Judul Album</label>
                        <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($judul) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <div class="flex justify-end space-x-4 pt-4 border-t">
                        <a href="kelola-album.php" class="bg-gray-200 text-gray-700 font-bold py-2 px-6 rounded-lg hover:bg-gray-300 transition-colors">Batal</a>
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors">Simpan Album</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
</body>
</html>