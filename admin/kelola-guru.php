<?php
require_once 'auth.php';
require_once '../includes/config.php';

// --- AWAL LOGIKA PAGINASI ---

// 1. Tentukan jumlah item per halaman
$limit = 10; 

// 2. Ambil nomor halaman saat ini dari URL, default ke halaman 1 jika tidak ada
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 3. Hitung total data di database
$total_result = $conn->query("SELECT COUNT(id) as total FROM guru");
if (!$total_result) { die("Error counting rows: " . $conn->error); }
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// 4. Hitung offset untuk query SQL
$offset = ($page - 1) * $limit;

// --- AKHIR LOGIKA PAGINASI ---


// Query utama diubah untuk mengambil data sesuai limit dan offset halaman
$sql = "SELECT * FROM guru ORDER BY nama ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
if (!$result) { die("Error fetching data: " . $conn->error); }

$pageTitle = "Kelola Guru";
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
        <main class="flex-1 p-8 overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">Kelola Guru & Staf</h1>
                <a href="form-guru.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600">Tambah Data Baru</a>
            </div>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-3 px-4">Foto</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">NIP</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="border-b">
                                <td class="py-2 px-4">
                                    <img src="../assets/images/guru/<?= htmlspecialchars($row['foto']) ?>" class="w-12 h-12 rounded-full object-cover" alt="Foto <?= htmlspecialchars($row['nama']) ?>">
                                </td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['nip']) ?></td>
                                <td class="py-2 px-4 text-center">
                                    <a href="form-guru.php?id=<?= $row['id'] ?>" class="text-yellow-500 hover:text-yellow-700 mr-4" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="hapus-guru.php?id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" title="Hapus"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">Tidak ada data untuk ditampilkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if ($total_pages > 1): ?>
                <div class="mt-6 flex justify-center items-center">
                    <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>" class="px-4 py-2 mx-1 text-gray-700 bg-white rounded-md hover:bg-blue-500 hover:text-white <?= $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="px-4 py-2 mx-1 rounded-md <?= $i == $page ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-blue-500 hover:text-white' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <a href="?page=<?= $page < $total_pages ? $page + 1 : $total_pages ?>" class="px-4 py-2 mx-1 text-gray-700 bg-white rounded-md hover:bg-blue-500 hover:text-white <?= $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
                <?php endif; ?>
                </div>
        </main>
    </div>
</div>
</body>
</html>