<?php
require_once 'includes/config.php';

// --- LOGIKA PENCARIAN & PAGINASI ---
$limit = 6; // Jumlah berita per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_query = "";
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [];
$types = "";

if (!empty($search_term)) {
    // Mencari di kolom judul, isi, atau kategori
    $search_query = " WHERE judul LIKE ? OR isi LIKE ? OR kategori LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params[] = &$like_term;
    $params[] = &$like_term;
    $params[] = &$like_term;
    $types .= "sss";
}

// Hitung Total Data
$sql_total = "SELECT COUNT(id) as total FROM berita" . $search_query;
$stmt_total = $conn->prepare($sql_total);
if (!empty($search_term)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_total->close();

// Ambil Data Berita Utama dengan Limit dan Offset
$sql_berita = "SELECT * FROM berita" . $search_query . " ORDER BY tanggal_publikasi DESC LIMIT ? OFFSET ?";
$params[] = &$limit;
$params[] = &$offset;
$types .= "ii";

$stmt_berita = $conn->prepare($sql_berita);
$stmt_berita->bind_param($types, ...$params);
$stmt_berita->execute();
$result_berita = $stmt_berita->get_result();

// Ambil data pengaturan untuk sorotan informasi
$sql_pengaturan = "SELECT highlight_announcement, highlight_exam, highlight_holiday FROM pengaturan WHERE id = 1";
$result_pengaturan = $conn->query($sql_pengaturan);
$pengaturan = ($result_pengaturan && $result_pengaturan->num_rows > 0) ? $result_pengaturan->fetch_assoc() : [];

$pageTitle = "Berita & Pengumuman - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Berita & Pengumuman</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Informasi Terbaru Seputar Kegiatan dan Prestasi Sekolah</p>
        </div>
    </section>

    <section class="py-16">
        <div class="container mx-auto px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-center mb-8" data-aos="fade-up">Sorotan Informasi</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                </div>
        </div>
    </section>

    <section class="pb-20">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="mb-12">
                <form action="berita.php" method="GET" class="max-w-lg mx-auto" data-aos="fade-up">
                    <div class="flex">
                        <input type="text" name="q" class="w-full px-4 py-2 border border-r-0 border-gray-300 rounded-l-md" placeholder="Cari berita..." value="<?= htmlspecialchars($search_term) ?>">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-md hover:bg-blue-700">Cari</button>
                    </div>
                </form>
            </div>

            <h2 class="text-2xl font-bold text-center mb-8" data-aos="fade-up">Semua Berita Terbaru</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if ($result_berita->num_rows > 0): ?>
                    <?php while($berita = $result_berita->fetch_assoc()): ?>
                        <a href="detail-berita.php?id=<?= $berita['id'] ?>" class="bg-white rounded-lg shadow-md overflow-hidden group" data-aos="fade-up">
                            <img src="assets/images/berita/<?= htmlspecialchars($berita['gambar']) ?>" alt="<?= htmlspecialchars($berita['judul']) ?>" class="w-full h-56 object-cover transform group-hover:scale-105 transition-transform">
                            <div class="p-6">
                                <p class="text-sm text-blue-600 font-semibold"><?= htmlspecialchars($berita['kategori']) ?></p>
                                <h3 class="mt-2 font-bold text-lg text-gray-800 group-hover:text-blue-600"><?= htmlspecialchars($berita['judul']) ?></h3>
                                <p class="text-sm text-gray-500 mt-4"><?= date('d F Y', strtotime($berita['tanggal_publikasi'])) ?></p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-3 text-center text-gray-500">
                        <?= !empty($search_term) ? 'Berita tidak ditemukan untuk pencarian "' . htmlspecialchars($search_term) . '".' : 'Belum ada berita untuk ditampilkan.' ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="flex justify-center mt-12" data-aos="fade-up">
                <nav class="flex items-center space-x-2">
                    <?php if ($total_pages > 1): ?>
                        <?php if ($page > 1): ?> <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 bg-white border rounded-md">Sebelumnya</a> <?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?> <a href="?page=<?= $i ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 border rounded-md <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-white' ?>"><?= $i ?></a> <?php endfor; ?>
                        <?php if ($page < $total_pages): ?> <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 bg-white border rounded-md">Selanjutnya</a> <?php endif; ?>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </section>
</main>
    
<?php require_once 'includes/footer.php'; ?>