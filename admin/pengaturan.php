<?php
require_once 'auth.php';

require_once '../includes/config.php';

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $nama_sekolah = $_POST['nama_sekolah'];
    $alamat = $_POST['alamat'];
    $telepon = $_POST['telepon'];
    $email = $_POST['email'];
    $peta_lokasi = $_POST['peta_lokasi'];
    $link_facebook = $_POST['link_facebook'];
    $link_instagram = $_POST['link_instagram'];
    $link_youtube = $_POST['link_youtube'];
    $link_tiktok = $_POST['link_tiktok'];
    $highlight_announcement = $_POST['highlight_announcement'];
    $highlight_exam = $_POST['highlight_exam'];
    $highlight_holiday = $_POST['highlight_holiday'];
    
    // Query UPDATE
    $sql = "UPDATE pengaturan SET 
                nama_sekolah = ?, alamat = ?, telepon = ?, email = ?, peta_lokasi = ?,
                link_facebook = ?, link_instagram = ?, link_youtube = ?, link_tiktok = ?,
                highlight_announcement = ?, highlight_exam = ?, highlight_holiday = ?
            WHERE id = 1";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssssssss", 
            $nama_sekolah, $alamat, $telepon, $email, $peta_lokasi,
            $link_facebook, $link_instagram, $link_youtube, $link_tiktok,
            $highlight_announcement, $highlight_exam, $highlight_holiday
        );
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pengaturan berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui pengaturan: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Gagal menyiapkan statement: " . $conn->error;
    }
    
    header("Location: pengaturan.php");
    exit();
}

// Ambil data pengaturan yang ada untuk ditampilkan di form
$sql_select = "SELECT * FROM pengaturan WHERE id = 1";
$result = $conn->query($sql_select);
$pengaturan = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Pengaturan Website - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php $currentPage = 'pengaturan'; require_once '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <?php $pageTitle = 'Pengaturan Website'; require_once '../includes/header_admin.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                 <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
                </div>
            <?php endif; ?>

            <form action="pengaturan.php" method="POST" class="space-y-8">
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold border-b border-gray-200 pb-3 mb-6">Pengaturan Umum & Kontak</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama_sekolah" class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                            <input type="text" id="nama_sekolah" name="nama_sekolah" value="<?= htmlspecialchars($pengaturan['nama_sekolah'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700">No. Telepon</label>
                            <input type="text" id="telepon" name="telepon" value="<?= htmlspecialchars($pengaturan['telepon'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat</label>
                            <input type="text" id="alamat" name="alamat" value="<?= htmlspecialchars($pengaturan['alamat'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($pengaturan['email'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="md:col-span-2">
                            <label for="peta_lokasi" class="block text-sm font-medium text-gray-700">URL Google Maps Embed</label>
                            <textarea id="peta_lokasi" name="peta_lokasi" rows="3" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md"><?= htmlspecialchars($pengaturan['peta_lokasi'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-lg font-semibold border-b pb-2 mb-4">Link Media Sosial</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="link_facebook" class="block text-sm font-medium text-gray-700">Facebook URL</label>
                            <input type="url" id="link_facebook" name="link_facebook" value="<?= htmlspecialchars($pengaturan['link_facebook'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                         <div>
                            <label for="link_instagram" class="block text-sm font-medium text-gray-700">Instagram URL</label>
                            <input type="url" id="link_instagram" name="link_instagram" value="<?= htmlspecialchars($pengaturan['link_instagram'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="link_youtube" class="block text-sm font-medium text-gray-700">YouTube URL</label>
                            <input type="url" id="link_youtube" name="link_youtube" value="<?= htmlspecialchars($pengaturan['link_youtube'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="link_tiktok" class="block text-sm font-medium text-gray-700">TikTok URL</label>
                            <input type="url" id="link_tiktok" name="link_tiktok" value="<?= htmlspecialchars($pengaturan['link_tiktok'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-lg font-semibold border-b pb-2 mb-4">Kelola Sorotan Informasi</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="highlight_announcement" class="block text-sm font-medium text-gray-700"><i class="fas fa-bullhorn text-blue-500 mr-2"></i> Teks Pengumuman</label>
                            <input type="text" id="highlight_announcement" name="highlight_announcement" value="<?= htmlspecialchars($pengaturan['highlight_announcement'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="highlight_exam" class="block text-sm font-medium text-gray-700"><i class="fas fa-calendar-alt text-yellow-500 mr-2"></i> Teks Jadwal Ujian</label>
                            <input type="text" id="highlight_exam" name="highlight_exam" value="<?= htmlspecialchars($pengaturan['highlight_exam'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="highlight_holiday" class="block text-sm font-medium text-gray-700"><i class="fas fa-flag text-red-500 mr-2"></i> Teks Hari Libur</label>
                            <input type="text" id="highlight_holiday" name="highlight_holiday" value="<?= htmlspecialchars($pengaturan['highlight_holiday'] ?? '') ?>" class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-8 rounded-lg hover:bg-blue-700 transition">Simpan Pengaturan</button>
                </div>
            </form>
        </main>
    </div>
</div>
</body>
</html>