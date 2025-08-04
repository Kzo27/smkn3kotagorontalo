<?php
require_once 'includes/config.php';

// --- LOGIKA PENCARIAN & PAGINASI ---
$limit = 8; // Jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// FIX: Ubah logika pencarian untuk kolom `nama` dan `nip`
$search_query = "";
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [];
$types = "";

if (!empty($search_term)) {
    $search_query = " WHERE nama LIKE ? OR nip LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params[] = &$like_term;
    $params[] = &$like_term;
    $types .= "ss";
}

// 3. Hitung Total Data (untuk paginasi)
$sql_total = "SELECT COUNT(id) as total FROM guru" . $search_query;
$stmt_total = $conn->prepare($sql_total);
if (!empty($search_term)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_total->close();

// 4. Ambil Data Utama dengan Limit dan Offset
$sql_guru = "SELECT * FROM guru" . $search_query . " ORDER BY nama ASC LIMIT ? OFFSET ?";
$params[] = &$limit;
$params[] = &$offset;
$types .= "ii";

$stmt_guru = $conn->prepare($sql_guru);
$stmt_guru->bind_param($types, ...$params);
$stmt_guru->execute();
$result_guru = $stmt_guru->get_result();

// Set judul halaman
$pageTitle = "Guru & Staf - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-4xl font-bold">Guru & Tenaga Kependidikan</h1>
            <p class="text-blue-200 mt-2">Para Pendidik Profesional di Balik Kesuksesan Siswa Kami</p>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="mb-12">
                <form action="guru.php" method="GET" class="max-w-lg mx-auto">
                    <div class="flex">
                        <input type="text" name="q" class="w-full px-4 py-2 border border-r-0 border-gray-300 rounded-l-md" placeholder="Cari nama atau NIP..." value="<?= htmlspecialchars($search_term) ?>">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-md hover:bg-blue-700">Cari</button>
                    </div>
                </form>
            </div>

            <div id="teacher-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php if ($result_guru->num_rows > 0): ?>
                    <?php while($guru = $result_guru->fetch_assoc()): ?>
                        <div class="text-center bg-white rounded-lg shadow-md p-6">
                            <img src="assets/images/guru/<?= htmlspecialchars($guru['foto']) ?>" alt="Foto <?= htmlspecialchars($guru['nama']) ?>" class="w-32 h-32 mx-auto rounded-full object-cover mb-4 border-4 border-gray-100">
                            <h3 class="font-bold text-lg text-gray-900"><?= htmlspecialchars($guru['nama']) ?></h3>
                            <p class="text-blue-600 text-sm">NIP: <?= htmlspecialchars($guru['nip']) ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-4 text-center text-gray-500">
                        <?= !empty($search_term) ? 'Data tidak ditemukan untuk pencarian "' . htmlspecialchars($search_term) . '".' : 'Data guru belum tersedia.' ?>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="flex justify-center mt-12">
                <nav class="flex items-center space-x-2">
                    <?php if ($total_pages > 1): ?>
                        <?php if ($page > 1): ?><a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 text-gray-700 bg-white border rounded-md">Sebelumnya</a><?php endif; ?>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?><a href="?page=<?= $i ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 border rounded-md <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' ?>"><?= $i ?></a><?php endfor; ?>
                        <?php if ($page < $total_pages): ?><a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 text-gray-700 bg-white border rounded-md">Selanjutnya</a><?php endif; ?>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </section>
</main>
<?php require_once 'includes/footer.php'; ?>