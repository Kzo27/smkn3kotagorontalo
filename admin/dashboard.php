<?php
require_once 'auth.php';
require_once '../includes/config.php';

// Definisikan variabel untuk halaman ini
$pageTitle = "Dashboard";
$currentPage = "dashboard";

// --- PENGAMBILAN DATA UNTUK DASHBOARD ---

// 1. Statistik Utama
$total_berita = $conn->query("SELECT COUNT(id) as total FROM berita")->fetch_assoc()['total'] ?? 0;
$total_galeri = $conn->query("SELECT COUNT(id) as total FROM foto_galeri")->fetch_assoc()['total'] ?? 0;
$total_guru = $conn->query("SELECT COUNT(id) as total FROM guru")->fetch_assoc()['total'] ?? 0;

// 2. Data untuk Grafik Berita per Kategori
$sql_kategori = "SELECT kategori, COUNT(id) as jumlah FROM berita GROUP BY kategori";
$result_kategori = $conn->query($sql_kategori);
$labels_kategori = [];
$data_kategori = [];
if ($result_kategori) {
    while ($row = $result_kategori->fetch_assoc()) {
        $labels_kategori[] = $row['kategori'];
        $data_kategori[] = $row['jumlah'];
    }
}

// 3. Feed Aktivitas Terbaru (lebih akurat)
$aktivitas_terbaru = [];
// Ambil 3 berita terakhir
$result_berita_terbaru = $conn->query("SELECT 'berita' as tipe, judul as info, tanggal_publikasi as waktu FROM berita ORDER BY tanggal_publikasi DESC LIMIT 3");
if($result_berita_terbaru) while($row = $result_berita_terbaru->fetch_assoc()) $aktivitas_terbaru[] = $row;
// Ambil 3 foto terakhir
$result_foto_terbaru = $conn->query("SELECT 'galeri' as tipe, keterangan_foto as info, tanggal_upload as waktu FROM foto_galeri ORDER BY tanggal_upload DESC LIMIT 3");
if($result_foto_terbaru) while($row = $result_foto_terbaru->fetch_assoc()) $aktivitas_terbaru[] = $row;
// Ambil 3 guru terakhir
$result_guru_terbaru = $conn->query("SELECT 'guru' as tipe, nama as info, created_at as waktu FROM guru ORDER BY created_at DESC LIMIT 3");
if($result_guru_terbaru) while($row = $result_guru_terbaru->fetch_assoc()) $aktivitas_terbaru[] = $row;

// Urutkan semua aktivitas berdasarkan waktu dan ambil 5 teratas
usort($aktivitas_terbaru, function($a, $b) {
    return strtotime($b['waktu']) - strtotime($a['waktu']);
});
$aktivitas_terbaru = array_slice($aktivitas_terbaru, 0, 5);


// 4. Sapaan Dinamis & Tanggal
date_default_timezone_set('Asia/Makassar');
$jam = date('H');
if ($jam >= 4 && $jam < 11) {
    $salam = "Selamat Pagi";
} elseif ($jam >= 11 && $jam < 15) {
    $salam = "Selamat Siang";
} elseif ($jam >= 15 && $jam < 19) {
    $salam = "Selamat Sore";
} else {
    $salam = "Selamat Malam";
}
// Mengambil nama admin dari session, ganti 'nama_admin' sesuai key session Anda
$nama_admin = isset($_SESSION['nama_admin']) ? htmlspecialchars($_SESSION['nama_admin']) : 'Admin';

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }
    </style>
</head>
<body class="bg-gray-200 font-sans">

<div class="flex h-screen bg-gray-200">
    <?php require_once '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once '../includes/header_admin.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200 p-6 md:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800"><?= $salam ?>, <?= $nama_admin ?>! 👋</h1>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-lg font-medium opacity-80">Total Berita</span>
                            <span class="text-4xl font-bold"><?= $total_berita ?></span>
                        </div>
                        <div class="bg-white/30 p-3 rounded-xl">
                            <i class="fas fa-newspaper text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300">
                     <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-lg font-medium opacity-80">Foto di Galeri</span>
                            <span class="text-4xl font-bold"><?= $total_galeri ?></span>
                        </div>
                        <div class="bg-white/30 p-3 rounded-xl">
                            <i class="fas fa-images text-2xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition-transform duration-300">
                    <div class="flex justify-between items-start">
                        <div class="flex flex-col">
                            <span class="text-lg font-medium opacity-80">Guru & Staf</span>
                            <span class="text-4xl font-bold"><?= $total_guru ?></span>
                        </div>
                        <div class="bg-white/30 p-3 rounded-xl">
                            <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white p-6 rounded-2xl shadow-md">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Komposisi Berita</h2>
                        <div class="h-80 w-full flex items-center justify-center">
                            <canvas id="chartBerita"></canvas>
                        </div>
                    </div>
                     <div class="bg-white p-6 rounded-2xl shadow-md">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Pintasan</h2>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <a href="form-berita.php" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-blue-100 hover:text-blue-700 transition">
                                <i class="fas fa-plus-circle text-3xl mb-2 text-blue-500"></i>
                                <p class="font-semibold">Tambah Berita</p>
                            </a>
                             <a href="kelola-album.php" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-purple-100 hover:text-purple-700 transition">
                                <i class="fas fa-images text-3xl mb-2 text-purple-500"></i>
                                <p class="font-semibold">Kelola Galeri</p>
                            </a>
                            <a href="form-guru.php" class="text-center p-4 bg-gray-50 rounded-lg hover:bg-green-100 hover:text-green-700 transition">
                                <i class="fas fa-user-plus text-3xl mb-2 text-green-500"></i>
                                <p class="font-semibold">Tambah Guru</p>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-md">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Terbaru</h2>
                    <ul class="space-y-5">
                        <?php if (!empty($aktivitas_terbaru)): ?>
                            <?php foreach($aktivitas_terbaru as $aktivitas): ?>
                                <li class="flex items-start space-x-4">
                                    <?php
                                        $icon = 'fa-question-circle text-gray-500';
                                        $prefix = 'Aktivitas baru:';
                                        if ($aktivitas['tipe'] == 'berita') {
                                            $icon = 'fa-newspaper text-blue-500';
                                            $prefix = 'Berita ditambahkan';
                                        } elseif ($aktivitas['tipe'] == 'guru') {
                                            $icon = 'fa-user-plus text-green-500';
                                            $prefix = 'Guru ditambahkan';
                                        } elseif ($aktivitas['tipe'] == 'galeri') {
                                            $icon = 'fa-image text-purple-500';
                                            $prefix = 'Foto diunggah';
                                        }
                                    ?>
                                    <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gray-100 rounded-full">
                                         <i class="fas <?= $icon ?>"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-700"><?= $prefix ?></p>
                                        <p class="text-sm text-gray-500 truncate" title="<?= htmlspecialchars($aktivitas['info']) ?>"><?= htmlspecialchars($aktivitas['info']) ?></p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-center text-gray-500 py-4">Belum ada aktivitas terbaru.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari PHP untuk chart
    const labels = <?= json_encode($labels_kategori) ?>;
    const data = <?= json_encode($data_kategori) ?>;

    if (labels.length > 0) {
        const ctx = document.getElementById('chartBerita').getContext('2d');
        const chartBerita = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Berita',
                    data: data,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(168, 85, 247, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(20, 184, 166, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 255, 255, 1)'
                    ],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    }
                },
                cutout: '70%'
            }
        });
    } else {
        // Tampilkan pesan jika tidak ada data berita
        const chartContainer = document.getElementById('chartBerita').parentElement;
        chartContainer.innerHTML = '<div class="text-center text-gray-500">Tidak ada data berita untuk ditampilkan pada grafik.</div>';
    }
});
</script>

</body>
</html>