<?php
require_once 'includes/config.php';

// --- LOGIKA PENCARIAN & PAGINASI ---

$limit = 6; // Jumlah item per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$search_query = "";
$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
$params = [];
$types = "";

if (!empty($search_term)) {
    // Mencari di kolom nama atau deskripsi
    $search_query = " WHERE nama LIKE ? OR deskripsi LIKE ?";
    $like_term = "%" . $search_term . "%";
    $params[] = &$like_term;
    $params[] = &$like_term;
    $types .= "ss";
}

// Hitung Total Data
$sql_total = "SELECT COUNT(id) as total FROM fasilitas" . $search_query;
$stmt_total = $conn->prepare($sql_total);
if (!empty($search_term)) {
    $stmt_total->bind_param($types, ...$params);
}
$stmt_total->execute();
$total_records = $stmt_total->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);
$stmt_total->close();

// Ambil Data Utama dengan Limit dan Offset
$sql_fasilitas = "SELECT * FROM fasilitas" . $search_query . " ORDER BY nama ASC LIMIT ? OFFSET ?";
$params[] = &$limit;
$params[] = &$offset;
$types .= "ii";

$stmt_fasilitas = $conn->prepare($sql_fasilitas);
$stmt_fasilitas->bind_param($types, ...$params);
$stmt_fasilitas->execute();
$result_fasilitas = $stmt_fasilitas->get_result();

$pageTitle = "Fasilitas Sekolah - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Fasilitas Sekolah</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Sarana dan Prasarana Penunjang Proses Belajar Mengajar</p>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8">
            
            <div class="mb-12">
                <form action="fasilitas.php" method="GET" class="max-w-lg mx-auto">
                    <div class="flex">
                        <input type="text" name="q" class="w-full px-4 py-2 border border-r-0 border-gray-300 rounded-l-md" placeholder="Cari fasilitas..." value="<?= htmlspecialchars($search_term) ?>">
                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-r-md hover:bg-blue-700">Cari</button>
                    </div>
                </form>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php if ($result_fasilitas->num_rows > 0): ?>
                    <?php while($fasilitas = $result_fasilitas->fetch_assoc()): ?>
                        <div class="facility-card" data-aos="fade-up">
                            <a href="assets/images/fasilitas/<?= htmlspecialchars($fasilitas['foto']) ?>" class="group block bg-white rounded-lg shadow-md overflow-hidden">
                                <img src="assets/images/fasilitas/<?= htmlspecialchars($fasilitas['foto']) ?>" alt="<?= htmlspecialchars($fasilitas['nama']) ?>" class="w-full h-56 object-cover transform group-hover:scale-105 transition-transform duration-300">
                                <div class="p-4">
                                    <h3 class="font-bold text-lg"><?= htmlspecialchars($fasilitas['nama']) ?></h3>
                                    <p class="text-sm text-gray-600"><?= htmlspecialchars($fasilitas['deskripsi']) ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-3 text-center text-gray-500">
                        <?= !empty($search_term) ? 'Fasilitas tidak ditemukan untuk pencarian "' . htmlspecialchars($search_term) . '".' : 'Data fasilitas belum tersedia.' ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="flex justify-center mt-12">
                <nav class="flex items-center space-x-2">
                    <?php if ($total_pages > 1): ?>
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 text-gray-700 bg-white border rounded-md">Sebelumnya</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?= $i ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 border rounded-md <?= ($i == $page) ? 'bg-blue-600 text-white' : 'bg-white text-gray-700' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search_term) ?>" class="px-4 py-2 text-gray-700 bg-white border rounded-md">Selanjutnya</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </section>
</main>
    
<?php require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const facilityCards = document.querySelectorAll('.facility-card a');
    if(typeof basicLightbox !== 'undefined'){
        facilityCards.forEach(card => {
            card.addEventListener('click', function(event) {
                event.preventDefault();
                const imageUrl = this.getAttribute('href');
                basicLightbox.create(`<img src="${imageUrl}" alt="Tampilan Penuh">`).show();
            });
        });
    }
});
</script>