<?php
require_once 'auth.php';
require_once '../includes/config.php';

// --- AWAL LOGIKA PAGINASI ---
$limit = 10; // Jumlah album per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total album untuk paginasi
$count_result = $conn->query("SELECT COUNT(id) as total FROM album");
if (!$count_result) { die("Error counting data: " . $conn->error); }
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);
// --- AKHIR LOGIKA PAGINASI ---

// Ambil data album sesuai limit dan halaman saat ini
$sql = "SELECT * FROM album ORDER BY tanggal_dibuat DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
if (!$result) { die("Error fetching data: " . $conn->error); }


// Set variabel untuk template
$pageTitle = "Kelola Album Galeri";
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
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h1>
                <a href="form-album.php" class="ms-auto bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Buat Album Baru
                </a>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100 text-sm font-semibold text-gray-600">
                                <th class="p-3">Judul Album</th>
                                <th class="p-3">Kelola Foto</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($album = $result->fetch_assoc()): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 font-semibold text-gray-800"><?= htmlspecialchars($album['judul']) ?></td>
                                        <td class="p-3 text-sm text-gray-600">
                                            <a href="kelola-galeri.php?album_id=<?= $album['id'] ?>" class="bg-blue-500 text-white text-xs py-1 px-3 rounded-full hover:bg-blue-600">Lihat/Tambah Foto</a>
                                        </td>
                                        <td class="p-3 text-center">
                                            <a href="form-album.php?id=<?= $album['id'] ?>" class="text-yellow-500 hover:text-yellow-700 mx-4" title="Edit Album"><i class="fas fa-edit"></i></a>
                                            <a href="hapus-album.php?id=<?= $album['id'] ?>" class="text-red-500 hover:text-red-700" title="Hapus Album" onclick="return confirm('Yakin ingin hapus album ini? Semua foto di dalamnya akan ikut terhapus.')"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center p-4 text-gray-500">Belum ada album yang dibuat.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="mt-6 flex justify-end items-center space-x-2">
                    <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>" 
                       class="px-4 py-2 text-gray-700 bg-white rounded-md border hover:bg-gray-100 transition <?= $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        Sebelumnya
                    </a>

                    <a href="?page=<?= $page < $total_pages ? $page + 1 : $total_pages ?>" 
                       class="px-4 py-2 text-gray-700 bg-white rounded-md border hover:bg-gray-100 transition <?= $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        Berikutnya
                    </a>
                </div>
                <?php endif; ?>
                </div>
        </main>
    </div>
</div>
</body>
</html>