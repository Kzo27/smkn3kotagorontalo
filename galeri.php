<?php
require_once 'includes/config.php';

// --- FUNGSI HELPER UNTUK THUMBNAIL YOUTUBE ---
// Diletakkan di sini agar bisa digunakan untuk sampul album.
function getYoutubeThumbnail($url) {
    $video_id = '';
    if (preg_match('/(v=|vi\/|youtu.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
        $video_id = $matches[2];
    }
    if ($video_id) {
        return "https://img.youtube.com/vi/{$video_id}/mqdefault.jpg";
    }
    // Gambar placeholder jika bukan link YouTube atau tidak valid
    return 'assets/images/default-placeholder.png';
}


// --- LOGIKA PENCARIAN & PAGINASI UNTUK ALBUM ---
$limit = 6; // Jumlah album per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_query = "";
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [];
$types = "";

if (!empty($search_term)) {
    $search_query = " WHERE a.judul LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params[] = &$like_term;
    $types .= "s";
}

// --- PERBAIKAN 1: MENGGABUNGKAN QUERY (MENGHILANGKAN N+1 PROBLEM) ---
// Mengambil album beserta path/url dan tipe dari item pertama sebagai sampul.
$base_sql = "
    SELECT 
        a.*,
        (SELECT path_or_url FROM foto_galeri fg WHERE fg.album_id = a.id ORDER BY fg.id ASC LIMIT 1) AS cover_path,
        (SELECT tipe FROM foto_galeri fg WHERE fg.album_id = a.id ORDER BY fg.id ASC LIMIT 1) AS cover_tipe
    FROM album a
";

// Hitung Total Album
$sql_total = "SELECT COUNT(a.id) as total FROM album a" . $search_query;
$stmt_total = $conn->prepare($sql_total);
if (!empty($search_term)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_total->close();

// Ambil Data Album Utama dengan Limit dan Offset
$sql_album = $base_sql . $search_query . " ORDER BY a.tanggal_dibuat DESC LIMIT ? OFFSET ?";
$params[] = &$limit;
$params[] = &$offset;
$types .= "ii";

$stmt_album = $conn->prepare($sql_album);
if (!empty($params)) {
    $stmt_album->bind_param($types, ...$params);
}
$stmt_album->execute();
$result_album = $stmt_album->get_result();

$pageTitle = "Galeri Sekolah - SMP Negeri Gorontalo";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Galeri Sekolah</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Momen dan Kenangan dari Berbagai Kegiatan Kami</p>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8">
            
            <div class="mb-12">
                <form action="galeri.php" method="GET" class="max-w-lg mx-auto">
                    <div class="flex">
                        <input type="text" name="q" class="w-full px-4 py-2 border border-r-0 border-gray-300 rounded-l-md" placeholder="Cari judul album..." value="<?= htmlspecialchars($search_term) ?>">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-md hover:bg-blue-700">Cari</button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if ($result_album && $result_album->num_rows > 0): ?>
                    <?php while($album = $result_album->fetch_assoc()): ?>
                        <?php
                        // --- PERBAIKAN 2: LOGIKA SAMPUL YANG LEBIH BAIK ---
                        // Tidak ada lagi query di dalam loop. Data sampul sudah ada dari query utama.
                        $cover_image_url = 'assets/images/default-placeholder.png'; // Default
                        if (!empty($album['cover_path'])) {
                            if ($album['cover_tipe'] == 'foto') {
                                $cover_image_url = 'assets/images/galeri/' . htmlspecialchars($album['cover_path']);
                            } elseif ($album['cover_tipe'] == 'video') {
                                // Menggunakan fungsi helper untuk mendapatkan thumbnail YouTube
                                $cover_image_url = getYoutubeThumbnail($album['cover_path']);
                            }
                        }
                        ?>
                        <a href="detail-album.php?id=<?= $album['id'] ?>" class="group block bg-white rounded-lg shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform" data-aos="fade-up">
                            <div class="relative h-56">
                                <img src="<?= $cover_image_url ?>" alt="Sampul Album <?= htmlspecialchars($album['judul']) ?>" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                                <div class="absolute bottom-4 left-4"><h3 class="text-white font-bold text-2xl"><?= htmlspecialchars($album['judul']) ?></h3></div>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-full text-center text-gray-500">
                        <?= !empty($search_term) ? 'Album tidak ditemukan untuk pencarian "' . htmlspecialchars($search_term) . '".' : 'Belum ada album yang dibuat.' ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="flex justify-center mt-12" data-aos="fade-up">
                <nav class="flex items-center space-x-2">
                    <?php if ($total_pages > 1): ?>
                        <?php if ($page > 1): ?><a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 bg-white border rounded-md">Sebelumnya</a><?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?><a href="?page=<?= $i ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 border rounded-md <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-white' ?>"><?= $i ?></a><?php endfor; ?>
                        <?php if ($page < $total_pages): ?><a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 bg-white border rounded-md">Selanjutnya</a><?php endif; ?>
                    <?php endif; ?>
                </nav>
            </div>

        </div>
    </section>
</main>
    
<?php require_once 'includes/footer.php'; ?>