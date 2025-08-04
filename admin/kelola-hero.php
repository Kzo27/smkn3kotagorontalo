<?php
require_once 'auth.php';
require_once '../includes/config.php';

// --- FUNGSI HELPER YOUTUBE ---
function getYoutubeVideoID($url) {
    if (empty($url)) return null;
    $regex = '/(?:youtube(?:-nocookie)?\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    preg_match($regex, $url, $matches);
    return $matches[1] ?? null;
}

function getYoutubeEmbedUrl($videoID) {
    return $videoID ? "https://www.youtube.com/embed/{$videoID}" : '';
}

function getYoutubeWatchUrl($videoID) {
    return $videoID ? "https://www.youtube.com/watch?v=XXXXXXXXXXX{$videoID}" : '';
}
// --- AKHIR FUNGSI HELPER ---


// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $subjudul = $_POST['subjudul'];
    $link_video_input = $_POST['link_video']; // Link 'watch' dari user
    $gambar_lama = $_POST['gambar_lama'];
    $gambar_baru = $gambar_lama;

    // PERBAIKAN: Konversi link 'watch' ke 'embed' sebelum disimpan
    $videoID = getYoutubeVideoID($link_video_input);
    $link_video_untuk_db = getYoutubeEmbedUrl($videoID);

    // Handle upload gambar baru jika ada
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        if (!empty($gambar_lama) && file_exists('../assets/images/hero/' . $gambar_lama)) {
            unlink('../assets/images/hero/' . $gambar_lama);
        }
        $target_dir = "../assets/images/hero/";
        $gambar_baru = time() . '_' . basename($_FILES["gambar"]["name"]);
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $gambar_baru);
    }

    // Query UPDATE
    $sql = "UPDATE hero_section SET judul = ?, subjudul = ?, link_video = ?, gambar = ? WHERE id = 1";
    if ($stmt = $conn->prepare($sql)) {
        // Gunakan link yang sudah diubah untuk database
        $stmt->bind_param("ssss", $judul, $subjudul, $link_video_untuk_db, $gambar_baru);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Bagian Hero berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
    header("Location: kelola-hero.php");
    exit();
}

// Ambil data hero yang ada untuk ditampilkan di form
$sql_select = "SELECT * FROM hero_section WHERE id = 1";
$result = $conn->query($sql_select);
$hero = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [];

// PERBAIKAN: Ubah link 'embed' dari DB kembali ke 'watch' untuk ditampilkan di form
$link_video_untuk_form = '';
if (!empty($hero['link_video'])) {
    $videoID_from_db = getYoutubeVideoID($hero['link_video']);
    $link_video_untuk_form = getYoutubeWatchUrl($videoID_from_db);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Hero Section - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php $currentPage = 'hero'; require_once '../includes/sidebar_admin.php'; ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php $pageTitle = 'Kelola Hero Section'; require_once '../includes/header_admin.php'; ?>
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-white p-8 rounded-2xl shadow-lg">
                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-4">Pengaturan Hero Section</h2>
                <form action="kelola-hero.php" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($hero['gambar'] ?? '') ?>">

                    <div>
                        <label for="judul" class="block text-sm font-medium text-gray-700">Judul Utama</label>
                        <input type="text" id="judul" name="judul" value="<?= htmlspecialchars($hero['judul'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="subjudul" class="block text-sm font-medium text-gray-700">Sub Judul / Paragraf</label>
                        <textarea id="subjudul" name="subjudul" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm"><?= htmlspecialchars($hero['subjudul'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <?php // PERBAIKAN: Label dan placeholder diubah ?>
                        <label for="link_video" class="block text-sm font-medium text-gray-700">Link Video YouTube (Biasa / Watch)</label>
                        <input type="url" id="link_video" name="link_video" value="<?= htmlspecialchars($link_video_untuk_form) ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Contoh: https://www.youtube.com/watch?v=...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Gambar Hero</label>
                        <div class="mt-2 flex items-center gap-6">
                            <img id="gambar_preview" src="<?= !empty($hero['gambar']) ? '../assets/images/hero/' . htmlspecialchars($hero['gambar']) : 'https://via.placeholder.com/200x150' ?>" class="w-48 h-auto rounded-md bg-gray-100 border object-cover">
                            <input type="file" id="gambar_upload" name="gambar" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>

                    <div class="text-right pt-6 border-t border-gray-200">
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-8 rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    // Pratinjau untuk gambar
    const gambarUpload = document.getElementById('gambar_upload');
    const gambarPreview = document.getElementById('gambar_preview');
    if(gambarUpload) {
        gambarUpload.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) { gambarPreview.src = e.target.result; }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }
</script>

</body>
</html>