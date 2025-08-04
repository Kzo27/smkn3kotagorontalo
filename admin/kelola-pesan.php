<?php
require_once 'auth.php';

require_once '../includes/config.php';

// Ambil semua pesan dari database, diurutkan dari yang terbaru
$sql = "SELECT * FROM pesan_kontak ORDER BY tanggal_kirim DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Error mengambil data: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pesan Masuk - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    
    <?php $currentPage = 'pesan'; require_once '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        
        <?php $pageTitle = 'Pesan Masuk'; require_once '../includes/header_admin.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4">Daftar Pesan dari Pengunjung</h2>
                <div class="space-y-6">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-gray-900">Nama Pengirim : <?= htmlspecialchars($row['nama']) ?></p>
                                        <p class="text-sm text-blue-600"><?= htmlspecialchars($row['email']) ?></p>
                                        <p class="text-sm text-gray-500 mt-1"><?= date('d M Y, H:i', strtotime($row['tanggal_kirim'])) ?></p>
                                    </div>
                                    <a href="hapus-pesan.php?id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" title="Hapus" onclick="return confirm('Yakin ingin menghapus pesan ini?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                                <hr class="my-3">
                                <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($row['subjek']) ?></h4>
                                <p class="mt-2 text-gray-700 leading-relaxed">
                                    <?= nl2br(htmlspecialchars($row['pesan'])) ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500">Tidak ada pesan masuk.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>