<?php
require_once 'auth.php';
require_once '../includes/config.php';

// --- AWAL LOGIKA PAGINASI ---

// 1. Atur jumlah item yang ditampilkan per halaman
$limit = 6;

// 2. Ambil nomor halaman saat ini dari URL. Jika tidak ada, default ke halaman 1.
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// 3. Hitung total data untuk mengetahui jumlah halaman
$total_result = $conn->query("SELECT COUNT(id) AS total FROM fasilitas");
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// 4. Hitung offset (data yang dilewati) untuk query SQL
$offset = ($page - 1) * $limit;

// --- AKHIR LOGIKA PAGINASI ---

// 5. Ubah query utama untuk mengambil data sesuai halaman saat ini
$sql = "SELECT * FROM fasilitas ORDER BY nama ASC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
if (!$result) { die("Error: " . $conn->error); }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Fasilitas - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php $currentPage = 'fasilitas'; require_once '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <?php $pageTitle = 'Kelola Fasilitas'; require_once '../includes/header_admin.php'; ?>
        
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Kelola Fasilitas</h1>
                <a href="form-fasilitas.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-600 transition duration-300">Tambah Fasilitas Baru</a>
            </div>

            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded mb-4" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
                        <img src="../assets/images/fasilitas/<?= htmlspecialchars($row['foto']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" class="w-full h-48 object-cover">
                        <div class="p-4 flex flex-col flex-grow">
                            <h3 class="font-bold text-lg mb-2 text-gray-800"><?= htmlspecialchars($row['nama']) ?></h3>
                            <p class="text-gray-600 text-sm mb-4 flex-grow"><?= htmlspecialchars($row['deskripsi']) ?></p>
                            <div class="flex justify-end space-x-3 border-t pt-3 mt-auto">
                                <a href="form-fasilitas.php?id=<?= $row['id'] ?>" class="text-sm text-yellow-600 hover:text-yellow-800 font-semibold">Edit</a>
                                <a href="hapus-fasilitas.php?id=<?= $row['id'] ?>" class="text-sm text-red-600 hover:text-red-800 font-semibold" onclick="return confirm('Yakin ingin menghapus fasilitas ini?')">Hapus</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 bg-white rounded-lg shadow-md">
                        <p class="text-gray-500">Belum ada data fasilitas yang ditambahkan.</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1): ?>
            <div class="mt-8 flex justify-center items-center space-x-2">
                <a href="?page=<?= $page > 1 ? $page - 1 : 1 ?>" 
                   class="px-4 py-2 text-gray-700 bg-white rounded-md shadow-sm hover:bg-blue-500 hover:text-white transition duration-200 <?= $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" 
                       class="px-4 py-2 rounded-md shadow-sm transition duration-200 <?= $i == $page ? 'bg-blue-500 text-white font-bold' : 'bg-white text-gray-700 hover:bg-blue-500 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <a href="?page=<?= $page < $total_pages ? $page + 1 : $total_pages ?>" 
                   class="px-4 py-2 text-gray-700 bg-white rounded-md shadow-sm hover:bg-blue-500 hover:text-white transition duration-200 <?= $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            <?php endif; ?>
            </main>
    </div>
</div>
</body>
</html>