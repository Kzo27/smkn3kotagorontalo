<?php
require_once 'auth.php';
require_once '../includes/config.php';

// Ambil ID Album dari URL
$album_id = isset($_GET['album_id']) ? (int)$_GET['album_id'] : 0;
if ($album_id === 0) {
    header("Location: kelola-album.php");
    exit();
}

// --- AWAL PROSES UPLOAD FOTO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['photos'])) {
    $keterangan_foto = $_POST['keterangan_foto'];
    $target_dir = "../assets/images/galeri/";
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    foreach ($_FILES['photos']['name'] as $key => $name) {
        if ($_FILES['photos']['error'][$key] === 0) {
            $file_name = time() . '_' . uniqid() . '_' . basename($name);
            $target_file = $target_dir . $file_name;
            $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            if (in_array($file_ext, $allowed_ext) && move_uploaded_file($_FILES['photos']['tmp_name'][$key], $target_file)) {
                // Perhatikan perubahan 'nama_file' menjadi 'path_or_url' dan penambahan 'tipe'
                $sql = "INSERT INTO foto_galeri (album_id, tipe, path_or_url, keterangan_foto) VALUES (?, 'foto', ?, ?)";
                if ($stmt = $conn->prepare($sql)) {
                    $stmt->bind_param("iss", $album_id, $file_name, $keterangan_foto);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        }
    }
    $_SESSION['success_message'] = "Foto berhasil diunggah ke album!";
    header("Location: kelola-galeri.php?album_id=" . $album_id);
    exit();
}
// --- AKHIR PROSES UPLOAD FOTO ---


// --- AWAL PROSES TAMBAH VIDEO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['video_links'])) {
    $keterangan_video = $_POST['keterangan_video'];
    $video_links_raw = $_POST['video_links'];
    $video_links = explode("\n", $video_links_raw); // Pisahkan URL berdasarkan baris baru

    $sql = "INSERT INTO foto_galeri (album_id, tipe, path_or_url, keterangan_foto) VALUES (?, 'video', ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        foreach ($video_links as $link) {
            $trimmed_link = trim($link); // Hapus spasi ekstra
            if (!empty($trimmed_link) && filter_var($trimmed_link, FILTER_VALIDATE_URL)) {
                $stmt->bind_param("iss", $album_id, $trimmed_link, $keterangan_video);
                $stmt->execute();
            }
        }
        $stmt->close();
    }
    $_SESSION['success_message'] = "Video berhasil ditambahkan ke album!";
    header("Location: kelola-galeri.php?album_id=" . $album_id);
    exit();
}
// --- AKHIR PROSES TAMBAH VIDEO ---


// Ambil info album untuk judul halaman
$stmt_album = $conn->prepare("SELECT judul FROM album WHERE id = ?");
$stmt_album->bind_param("i", $album_id);
$stmt_album->execute();
$album = $stmt_album->get_result()->fetch_assoc();
$stmt_album->close();
if (!$album) { header("Location: kelola-album.php"); exit(); }


// --- AWAL LOGIKA PAGINASI ---
$limit = 10; // Jumlah item per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Hitung total item (foto & video) di album ini untuk paginasi
$stmt_count = $conn->prepare("SELECT COUNT(id) as total FROM foto_galeri WHERE album_id = ?");
$stmt_count->bind_param("i", $album_id);
$stmt_count->execute();
$total_rows = $stmt_count->get_result()->fetch_assoc()['total'];
$stmt_count->close();
$total_pages = ceil($total_rows / $limit);
// --- AKHIR LOGIKA PAGINASI ---


// Ambil foto dan video untuk album ini sesuai dengan limit dan offset halaman
$stmt_items = $conn->prepare("SELECT * FROM foto_galeri WHERE album_id = ? ORDER BY tanggal_upload DESC LIMIT ? OFFSET ?");
$stmt_items->bind_param("iii", $album_id, $limit, $offset);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

// --- FUNGSI HELPER UNTUK THUMBNAIL YOUTUBE ---
function getYoutubeThumbnail($url) {
    $video_id = '';
    parse_str(parse_url($url, PHP_URL_QUERY), $vars);
    if (isset($vars['v'])) {
        $video_id = $vars['v'];
    } elseif (strpos($url, 'youtu.be/') !== false) {
        $video_id = substr(parse_url($url, PHP_URL_PATH), 1);
    }
    
    if ($video_id) {
        return "https://img.youtube.com/vi/{$video_id}/mqdefault.jpg";
    }
    return 'https://via.placeholder.com/320x180.png?text=Invalid+Link'; // Gambar placeholder jika URL tidak valid
}


$pageTitle = 'Kelola Galeri: ' . htmlspecialchars($album['judul']);
$currentPage = "galeri";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title><?= $pageTitle ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/brands.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php require_once '../includes/sidebar_admin.php'; ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once '../includes/header_admin.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <a href="kelola-album.php" class="text-blue-600 hover:underline mb-6 inline-block">&larr; Kembali ke Daftar Album</a>
            
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
                <h2 class="font-bold text-lg mb-4">Unggah Foto ke Album "<?= htmlspecialchars($album['judul']) ?>"</h2>
                <form action="kelola-galeri.php?album_id=<?= $album_id ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="keterangan_foto" class="block text-sm font-medium text-gray-700 mb-1">Keterangan/Judul Foto (berlaku untuk semua foto)</label>
                        <input type="text" name="keterangan_foto" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <label for="photos" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500">Klik untuk memilih file (bisa pilih banyak)</p>
                        </label>
                        <input type="file" id="photos" name="photos[]" class="hidden" multiple accept="image/*">
                    </div>
                    <div id="image-preview-container" class="mt-4 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4"></div>
                    <div class="text-right mt-4">
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700">Unggah Foto</button>
                    </div>
                </form>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg mb-8">
                <h2 class="font-bold text-lg mb-4">Tambah Video (dari Link YouTube) ke Album "<?= htmlspecialchars($album['judul']) ?>"</h2>
                <form action="kelola-galeri.php?album_id=<?= $album_id ?>" method="POST">
                    <div class="mb-4">
                        <label for="keterangan_video" class="block text-sm font-medium text-gray-700 mb-1">Keterangan/Judul Video (berlaku untuk semua link)</label>
                        <input type="text" name="keterangan_video" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="mb-4">
                         <label for="video_links" class="block text-sm font-medium text-gray-700 mb-1">Link Video (satu link per baris)</label>
                         <textarea name="video_links" rows="5" class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.youtube.com/watch?v=xxxxxxxxx&#10;https://www.youtube.com/watch?v=yyyyyyyyy"></textarea>
                    </div>
                    <div class="text-right mt-4">
                        <button type="submit" class="bg-green-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-green-700">Tambah Video</button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="font-bold text-lg mb-4">Daftar Galeri di Album Ini</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                <?php if($result_items->num_rows > 0): ?>
                    <?php while($item = $result_items->fetch_assoc()): ?>
                    <div class="relative group">
                        <?php if ($item['tipe'] == 'foto'): ?>
                            <img src="../assets/images/galeri/<?= htmlspecialchars($item['path_or_url']) ?>" alt="<?= htmlspecialchars($item['keterangan_foto']) ?>" class="w-full h-40 object-cover rounded-md">
                        <?php elseif ($item['tipe'] == 'video'): 
                            $thumbnail_url = getYoutubeThumbnail($item['path_or_url']);
                        ?>
                            <a href="<?= htmlspecialchars($item['path_or_url']) ?>" target="_blank" rel="noopener noreferrer">
                                <img src="<?= $thumbnail_url ?>" alt="<?= htmlspecialchars($item['keterangan_foto']) ?>" class="w-full h-40 object-cover rounded-md">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center rounded-md">
                                    <i class="fab fa-youtube text-red-500 text-5xl"></i>
                                </div>
                            </a>
                        <?php endif; ?>

                        <div class="absolute inset-0 bg-black/60 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition p-2 text-center">
                             <p class="text-white text-xs font-semibold mb-2"><?= htmlspecialchars($item['keterangan_foto']) ?></p>
                             <a href="hapus-item-galeri.php?id=<?= $item['id'] ?>&album_id=<?= $album_id ?>" class="text-white bg-red-600/80 w-8 h-8 rounded-full flex items-center justify-center" onclick="return confirm('Yakin ingin menghapus item ini?')">
                                 <i class="fas fa-trash-alt"></i>
                             </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="col-span-full text-center text-gray-500">Belum ada foto atau video di album ini.</p>
                <?php endif; ?>
                </div>

                <?php if ($total_pages > 1): ?>
                <div class="mt-8 flex justify-center items-center space-x-2">
                    <a href="?album_id=<?= $album_id ?>&page=<?= $page > 1 ? $page - 1 : 1 ?>" class="px-4 py-2 text-gray-700 bg-white rounded-md shadow-sm hover:bg-blue-500 hover:text-white transition duration-200 <?= $page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?album_id=<?= $album_id ?>&page=<?= $i ?>" class="px-4 py-2 rounded-md shadow-sm transition duration-200 <?= $i == $page ? 'bg-blue-500 text-white font-bold' : 'bg-white text-gray-700 hover:bg-blue-500 hover:text-white' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <a href="?album_id=<?= $album_id ?>&page=<?= $page < $total_pages ? $page + 1 : $total_pages ?>" class="px-4 py-2 text-gray-700 bg-white rounded-md shadow-sm hover:bg-blue-500 hover:text-white transition duration-200 <?= $page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' ?>"><i class="fas fa-chevron-right"></i></a>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const photoInput = document.getElementById('photos');
    const previewContainer = document.getElementById('image-preview-container');

    if (photoInput && previewContainer) {
        photoInput.addEventListener('change', function () {
            previewContainer.innerHTML = ''; // Kosongkan preview
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')){
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.classList.add('w-full', 'h-24', 'object-cover', 'rounded-md', 'border');
                            previewContainer.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    }
});
</script>

</body>
</html>