<?php
require_once 'auth.php';
require_once '../includes/config.php';

// Proses form saat disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil semua data dari form
    $visi = $_POST['vision'];
    $misi = $_POST['mission'];
    $sejarah_judul = $_POST['history_title'];
    $sejarah_teks = $_POST['history_text'];
    
    // Ambil data timeline
    $tl1_thn = $_POST['timeline1_tahun']; $tl1_teks = $_POST['timeline1_teks'];
    $tl2_thn = $_POST['timeline2_tahun']; $tl2_teks = $_POST['timeline2_teks'];
    $tl3_thn = $_POST['timeline3_tahun']; $tl3_teks = $_POST['timeline3_teks'];
    $tl4_thn = $_POST['timeline4_tahun']; $tl4_teks = $_POST['timeline4_teks'];

    // Ambil nama gambar lama dari hidden input
    $sejarah_gambar_lama = $_POST['sejarah_gambar_lama'];
    $org_chart_gambar_lama = $_POST['org_chart_gambar_lama'];
    $denah_gambar_lama = $_POST['denah_gambar_lama'];

    // Fungsi untuk handle upload
    function handle_upload($file_input_name, $old_filename) {
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
            if (!empty($old_filename) && file_exists('../assets/images/profil/' . $old_filename)) {
                unlink('../assets/images/profil/' . $old_filename);
            }
            $target_dir = "../assets/images/profil/";
            $new_filename = time() . '_' . basename($_FILES[$file_input_name]["name"]);
            move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_dir . $new_filename);
            return $new_filename;
        }
        return $old_filename;
    }

    $sejarah_gambar_baru = handle_upload('image_upload_sejarah', $sejarah_gambar_lama);
    $org_chart_gambar_baru = handle_upload('org_chart_upload', $org_chart_gambar_lama);
    $denah_gambar_baru = handle_upload('denah_gambar', $denah_gambar_lama);

    // Query UPDATE
    $sql = "UPDATE profil_sekolah SET 
                visi = ?, misi = ?, sejarah_judul = ?, sejarah_teks = ?, 
                sejarah_gambar = ?, org_chart_gambar = ?, denah_gambar = ?,
                timeline1_tahun = ?, timeline1_teks = ?, timeline2_tahun = ?, timeline2_teks = ?,
                timeline3_tahun = ?, timeline3_teks = ?, timeline4_tahun = ?, timeline4_teks = ?
            WHERE id = 1";
            
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssssssssssss", 
            $visi, $misi, $sejarah_judul, $sejarah_teks, 
            $sejarah_gambar_baru, $org_chart_gambar_baru, $denah_gambar_baru,
            $tl1_thn, $tl1_teks, $tl2_thn, $tl2_teks, 
            $tl3_thn, $tl3_teks, $tl4_thn, $tl4_teks
        );
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data profil sekolah berhasil diperbarui!";
        } else {
            $_SESSION['error_message'] = "Gagal memperbarui data: " . $stmt->error;
        }
        $stmt->close();
    }
    
    header("Location: kelola-profil.php");
    exit();
}

// Ambil data profil yang ada untuk ditampilkan di form
$sql_select = "SELECT * FROM profil_sekolah WHERE id = 1";
$result = $conn->query($sql_select);
$data = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : [];

$pageTitle = 'Kelola Profil Sekolah';
$currentPage = 'profil';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php require_once '../includes/sidebar_admin.php'; ?>
    <div class="flex-1 flex flex-col overflow-hidden">
        <?php require_once '../includes/header_admin.php'; ?>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>

            <form action="kelola-profil.php" method="POST" class="space-y-8" enctype="multipart/form-data">
                <input type="hidden" name="sejarah_gambar_lama" value="<?= htmlspecialchars($data['sejarah_gambar'] ?? '') ?>">
                <input type="hidden" name="org_chart_gambar_lama" value="<?= htmlspecialchars($data['org_chart_gambar'] ?? '') ?>">
                <input type="hidden" name="denah_gambar_lama" value="<?= htmlspecialchars($data['denah_gambar'] ?? '') ?>">

                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">Edit Visi & Misi</h2>
                    <div class="space-y-6 mt-4">
                        <div><label for="vision" class="block text-sm font-medium text-gray-700 mb-1">Visi</label><textarea id="vision" name="vision" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md"><?= htmlspecialchars($data['visi'] ?? '') ?></textarea></div>
                        <div><label for="mission" class="block text-sm font-medium text-gray-700 mb-1">Misi</label><textarea id="mission" name="mission" rows="6" class="w-full px-4 py-2 border border-gray-300 rounded-md"><?= htmlspecialchars($data['misi'] ?? '') ?></textarea></div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">Edit Halaman Sejarah</h2>
                    <div class="space-y-6 mt-4">
                        <div><label for="image_upload_sejarah" class="block text-sm font-medium text-gray-700 mb-2">Gambar Sejarah</label><div class="flex items-center gap-4"><img id="image_preview_sejarah" src="<?= !empty($data['sejarah_gambar']) ? '../assets/images/profil/' . htmlspecialchars($data['sejarah_gambar']) : 'https://via.placeholder.com/150x100' ?>" alt="Pratinjau Gambar Sejarah" class="w-40 h-auto rounded-md bg-gray-200 object-cover"><input type="file" id="image_upload_sejarah" name="image_upload_sejarah" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"></div></div>
                        <div><label for="history_title" class="block text-sm font-medium text-gray-700 mb-1">Judul Sejarah</label><input type="text" id="history_title" name="history_title" value="<?= htmlspecialchars($data['sejarah_judul'] ?? '') ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md"></div>
                        <div><label for="history_text" class="block text-sm font-medium text-gray-700 mb-1">Teks Sejarah</label><textarea id="history_text" name="history_text" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-md"><?= htmlspecialchars($data['sejarah_teks'] ?? '') ?></textarea></div>
                        <div><h3 class="text-md font-medium text-gray-700 mb-2">Item Linimasa</h3><div class="space-y-4"><div class="grid md:grid-cols-3 gap-4"><input type="text" name="timeline1_tahun" value="<?= htmlspecialchars($data['timeline1_tahun'] ?? '') ?>" placeholder="Tahun" class="px-4 py-2 border rounded-md"><input type="text" name="timeline1_teks" value="<?= htmlspecialchars($data['timeline1_teks'] ?? '') ?>" placeholder="Deskripsi" class="md:col-span-2 px-4 py-2 border rounded-md"></div><div class="grid md:grid-cols-3 gap-4"><input type="text" name="timeline2_tahun" value="<?= htmlspecialchars($data['timeline2_tahun'] ?? '') ?>" placeholder="Tahun" class="px-4 py-2 border rounded-md"><input type="text" name="timeline2_teks" value="<?= htmlspecialchars($data['timeline2_teks'] ?? '') ?>" placeholder="Deskripsi" class="md:col-span-2 px-4 py-2 border rounded-md"></div><div class="grid md:grid-cols-3 gap-4"><input type="text" name="timeline3_tahun" value="<?= htmlspecialchars($data['timeline3_tahun'] ?? '') ?>" placeholder="Tahun" class="px-4 py-2 border rounded-md"><input type="text" name="timeline3_teks" value="<?= htmlspecialchars($data['timeline3_teks'] ?? '') ?>" placeholder="Deskripsi" class="md:col-span-2 px-4 py-2 border rounded-md"></div><div class="grid md:grid-cols-3 gap-4"><input type="text" name="timeline4_tahun" value="<?= htmlspecialchars($data['timeline4_tahun'] ?? '') ?>" placeholder="Tahun" class="px-4 py-2 border rounded-md"><input type="text" name="timeline4_teks" value="<?= htmlspecialchars($data['timeline4_teks'] ?? '') ?>" placeholder="Deskripsi" class="md:col-span-2 px-4 py-2 border rounded-md"></div></div></div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">Edit Gambar Struktur Organisasi</h2>
                    <div class="mt-4">
                        <label for="org_chart_upload" class="block text-sm font-medium text-gray-700 mb-2">Unggah Gambar Baru</label>
                        <div class="mb-4"><img id="org_chart_preview" src="<?= !empty($data['org_chart_gambar']) ? '../assets/images/profil/' . htmlspecialchars($data['org_chart_gambar']) : 'https://via.placeholder.com/400x300' ?>" alt="Pratinjau Struktur Organisasi" class="max-w-md w-full h-auto rounded-md bg-gray-200 border"></div>
                        <input type="file" id="org_chart_upload" name="org_chart_upload" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-3">Edit Gambar Denah Sekolah</h2>
                    <div class="mt-4">
                        <label for="denah_upload" class="block text-sm font-medium text-gray-700 mb-2">Unggah Gambar Denah</label>
                        <div class="mb-4"><img id="denah_preview" src="<?= !empty($data['denah_gambar']) ? '../assets/images/profil/' . htmlspecialchars($data['denah_gambar']) : 'https://via.placeholder.com/400x300' ?>" alt="Pratinjau Denah" class="max-w-md w-full h-auto rounded-md bg-gray-200 border"></div>
                        <input type="file" id="denah_upload" name="denah_gambar" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-lg hover:bg-blue-700">Simpan Semua Perubahan</button>
                </div>
            </form>
        </main>
    </div>
</div>

<script>
    // Script untuk pratinjau gambar di form Sejarah
    const imageUploadSejarah = document.getElementById('image_upload_sejarah');
    const imagePreviewSejarah = document.getElementById('image_preview_sejarah');
    if(imageUploadSejarah) {
        imageUploadSejarah.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) { imagePreviewSejarah.src = e.target.result; }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Script untuk pratinjau gambar Struktur Organisasi
    const orgChartUpload = document.getElementById('org_chart_upload');
    const orgChartPreview = document.getElementById('org_chart_preview');
    if(orgChartUpload) {
        orgChartUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) { orgChartPreview.src = e.target.result; }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Script untuk pratinjau gambar Denah Sekolah
    const denahUpload = document.getElementById('denah_upload');
    const denahPreview = document.getElementById('denah_preview');
    if(denahUpload) {
        denahUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) { denahPreview.src = e.target.result; }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
</script>
</body>
</html>