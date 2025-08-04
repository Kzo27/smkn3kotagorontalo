<?php
require_once 'includes/config.php';

// --- FUNGSI HELPER UNTUK YOUTUBE (DIPERBAIKI) ---
function getYoutubeVideoID($url) {
    // Regex ini lebih andal untuk berbagai format URL YouTube
    $regex = '/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    preg_match($regex, $url, $matches);
    return $matches[1] ?? null;
}

function getYoutubeThumbnailUrl($videoID) {
    if ($videoID) {
        // Menggunakan thumbnail kualitas medium untuk tampilan yang lebih baik
        return "https://img.youtube.com/vi/{$videoID}/mqdefault.jpg";
    }
    return 'assets/images/default-placeholder.png'; // Gambar cadangan
}

function getYoutubeEmbedUrl($videoID) {
    if ($videoID) {
        // Ini adalah format URL yang benar untuk embed di iframe
        return "https://www.youtube.com/embed/{$videoID}";
    }
    return '';
}

$album_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($album_id === 0) {
    header("Location: galeri.php");
    exit();
}

// Ambil detail album
$stmt_album = $conn->prepare("SELECT * FROM album WHERE id = ?");
$stmt_album->bind_param("i", $album_id);
$stmt_album->execute();
$album = $stmt_album->get_result()->fetch_assoc();
if (!$album) { header("Location: galeri.php"); exit(); }

// Ambil semua item (foto & video) dari album ini
$stmt_items = $conn->prepare("SELECT * FROM foto_galeri WHERE album_id = ? ORDER BY tanggal_upload DESC");
$stmt_items->bind_param("i", $album_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

$pageTitle = "Galeri: " . htmlspecialchars($album['judul']);
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-4xl font-bold" data-aos="fade-up"><?= htmlspecialchars($album['judul']) ?></h1>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8">
            <div id="gallery-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <?php if ($result_items && $result_items->num_rows > 0): ?>
                    <?php while($item = $result_items->fetch_assoc()): ?>
                        <div class="gallery-item" data-aos="zoom-in">
                            <?php
                            // PERBAIKAN: Cek data lama dan baru, default ke 'foto' jika tipe tidak ada
                            $type = $item['tipe'] ?? 'foto';
                            $path_or_url = $item['path_or_url'] ?? $item['nama_file'] ?? ''; // Ambil path_or_url, jika tidak ada, ambil nama_file (untuk data lama)

                            if ($type == 'foto' && !empty($path_or_url)):
                                $filePath = 'assets/images/galeri/' . htmlspecialchars($path_or_url);
                            ?>
                                <a href="<?= $filePath ?>" data-type="image" class="group relative block w-full h-64 bg-gray-200 rounded-lg overflow-hidden">
                                    <img src="<?= $filePath ?>" alt="<?= htmlspecialchars($item['keterangan_foto'] ?? 'Foto Galeri') ?>" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform">
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center p-4 text-center opacity-0 group-hover:opacity-100 transition">
                                        <p class="text-white text-sm font-semibold"><?= htmlspecialchars($item['keterangan_foto'] ?? '') ?></p>
                                    </div>
                                </a>
                            <?php elseif ($type == 'video' && !empty($path_or_url)):
                                $videoID = getYoutubeVideoID($path_or_url);
                                $thumbnailUrl = getYoutubeThumbnailUrl($videoID);
                                $embedUrl = getYoutubeEmbedUrl($videoID);
                            ?>
                                <a href="<?= $embedUrl ?>" data-type="video" class="group relative block w-full h-64 bg-gray-900 rounded-lg overflow-hidden">
                                    <img src="<?= $thumbnailUrl ?>" alt="<?= htmlspecialchars($item['keterangan_foto'] ?? 'Video Galeri') ?>" class="w-full h-full object-cover opacity-70 group-hover:opacity-100 group-hover:scale-110 transition-all">
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-16 h-16 bg-red-600/80 rounded-full flex items-center justify-center group-hover:bg-red-500 transition-colors">
                                            <i class="fas fa-play text-white text-2xl ml-1"></i>
                                        </div>
                                    </div>
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center p-4 text-center opacity-0 group-hover:opacity-100 transition">
                                        <p class="text-white text-sm font-semibold"><?= htmlspecialchars($item['keterangan_foto'] ?? '') ?></p>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-full text-center text-gray-500">Tidak ada foto atau video di dalam album ini.</p>
                <?php endif; ?>
            </div>
            <div class="text-center mt-12">
                <a href="galeri.php" class="text-blue-600 hover:underline">← Kembali ke Daftar Album</a>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if(typeof basicLightbox !== 'undefined') {
        const galleryContainer = document.getElementById('gallery-grid');

        galleryContainer.addEventListener('click', function(event) {
            const link = event.target.closest('a.group');
            if (!link) return;

            // Cek jika link memiliki href, jika tidak, abaikan
            const url = link.getAttribute('href');
            if (!url) return;
            
            event.preventDefault();

            const type = link.dataset.type;

            if (type === 'image') {
                basicLightbox.create(`<img src="${url}">`).show();
            } else if (type === 'video') {
                basicLightbox.create(
                    `<iframe src="${url}?autoplay=1" width="1280" height="720" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>`
                ).show();
            }
        });
    }
});
</script>