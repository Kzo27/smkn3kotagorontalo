<?php
require_once 'auth.php';
// Definisikan variabel untuk halaman ini
$pageTitle = "Kelola Berita";
$currentPage = "berita";

require_once '../includes/config.php';

// --- AWAL LOGIKA PAGINASI ---

// 1. Atur jumlah item per halaman
$limit = 10;

// 2. Ambil nomor halaman dari URL, default ke halaman 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 3. Hitung total data untuk paginasi
$count_result = $conn->query("SELECT COUNT(id) as total FROM berita");
if (!$count_result) { die("Error counting data: " . $conn->error); }
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// 4. Hitung offset untuk query
$offset = ($page - 1) * $limit;

// --- AKHIR LOGIKA PAGINASI ---


// 5. Ambil data berita sesuai limit dan halaman saat ini
$sql = "SELECT id, judul, kategori, tanggal_publikasi FROM berita ORDER BY tanggal_publikasi DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Cek jika query gagal
if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen bg-gray-100">
    <?php include '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <?php include '../includes/header_admin.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <div class="flex justify-between items-center mb-4">
                 <h1 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?></h1>
                <a href="form-berita.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition">
                    <i class="fas fa-plus mr-2"></i> Tambah Berita Baru
                </a>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-200 text-gray-600">
                                <th class="py-3 px-4">Judul Berita</th>
                                <th class="py-3 px-4">Kategori</th>
                                <th class="py-3 px-4">Tanggal Publikasi</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 px-4 font-medium text-gray-800"><?= htmlspecialchars($row['judul']) ?></td>
                                        <td class="py-3 px-4">
                                            <span class="bg-blue-200 text-blue-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded"><?= htmlspecialchars($row['kategori']) ?></span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600"><?= date('d M Y, H:i', strtotime($row['tanggal_publikasi'])) ?></td>
                                        <td class="py-3 px-4 text-center">
                                            <a href="form-berita.php?id=<?= $row['id'] ?>" class="text-yellow-500 hover:text-yellow-700 mr-4" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="hapus-berita.php?id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">Belum ada berita.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="mt-6 flex justify-end items-center space-x-2">
                    <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>" 
                       class="px-4 py-2 text-gray-700 bg-white rounded-md border hover:bg-gray-100 <?= $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        Sebelumnya
                    </a>

                    <a href="?page=<?= $page < $total_pages ? $page + 1 : $total_pages ?>" 
                       class="px-4 py-2 text-gray-700 bg-white rounded-md border hover:bg-gray-100 <?= $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' ?>">
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