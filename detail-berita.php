<?php
// Hubungkan ke database
require_once 'includes/config.php';

// Ambil ID berita dari URL dan pastikan itu adalah angka
$berita_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Jika ID tidak valid, alihkan pengguna ke halaman berita utama
if ($berita_id === 0) {
    header("Location: berita.php");
    exit();
}

// Ambil data berita spesifik berdasarkan ID menggunakan prepared statement
$sql = "SELECT * FROM berita WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $berita_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $berita = $result->fetch_assoc();
    } else {
        // Jika berita dengan ID tersebut tidak ditemukan, alihkan juga
        header("Location: berita.php");
        exit();
    }
    $stmt->close();
}

// Ambil 3 berita terbaru lainnya untuk ditampilkan di sidebar
$sql_lainnya = "SELECT id, judul, gambar FROM berita WHERE id != ? ORDER BY tanggal_publikasi DESC LIMIT 3";
if ($stmt_lainnya = $conn->prepare($sql_lainnya)) {
    $stmt_lainnya->bind_param("i", $berita_id);
    $stmt_lainnya->execute();
    $result_lainnya = $stmt_lainnya->get_result();
}

// Set judul halaman sesuai judul berita
$pageTitle = htmlspecialchars($berita['judul']) . " - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <div class="py-16 lg:py-24">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-8">
                    <article class="bg-white p-6 md:p-8 rounded-lg shadow-md">
                        <p class="text-sm text-blue-600 font-semibold mb-2"><?= htmlspecialchars($berita['kategori']) ?></p>
                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight mb-4">
                            <?= htmlspecialchars($berita['judul']) ?>
                        </h1>
                        <p class="text-sm text-gray-500 mb-6">
                            Dipublikasikan pada: <?= date('d F Y', strtotime($berita['tanggal_publikasi'])) ?>
                        </p>
                        
                        <img src="assets/images/berita/<?= htmlspecialchars($berita['gambar']) ?>" alt="<?= htmlspecialchars($berita['judul']) ?>" class="w-full h-auto max-h-[500px] object-cover rounded-lg mb-8">
                        
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            <?php // nl2br berfungsi untuk mengubah baris baru dari database menjadi tag <br> di HTML ?>
                            <?= nl2br(htmlspecialchars($berita['isi'])) ?>
                        </div>
                    </article>
                </div>

                <aside class="lg:col-span-4">
                    <div class="sticky top-28">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold mb-4 border-b pb-3">Berita Lainnya</h3>
                            <div class="space-y-4">
                                <?php if ($result_lainnya && $result_lainnya->num_rows > 0): ?>
                                    <?php while($lainnya = $result_lainnya->fetch_assoc()): ?>
                                        <a href="detail-berita.php?id=<?= $lainnya['id'] ?>" class="flex items-center gap-4 group">
                                            <img src="assets/images/berita/<?= htmlspecialchars($lainnya['gambar']) ?>" alt="<?= htmlspecialchars($lainnya['judul']) ?>" class="w-20 h-20 rounded-md object-cover flex-shrink-0">
                                            <div>
                                                <h4 class="font-semibold text-gray-800 group-hover:text-blue-600 transition leading-tight"><?= htmlspecialchars($lainnya['judul']) ?></h4>
                                            </div>
                                        </a>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</main>
<?php require_once 'includes/footer.php'; ?>