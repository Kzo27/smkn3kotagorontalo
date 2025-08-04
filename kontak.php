<?php
// FIX: Tambahkan baris ini di paling atas
session_start();

// Hubungkan ke database
require_once 'includes/config.php';

// --- BAGIAN PEMROSESAN FORM ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form & bersihkan
    $nama = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subjek = trim($_POST['subject']);
    $pesan = trim($_POST['message']);

    // Validasi sederhana
    if (!empty($nama) && !empty($email) && !empty($pesan) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Simpan pesan ke database
        $sql_insert = "INSERT INTO pesan_kontak (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param("ssss", $nama, $email, $subjek, $pesan);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Pesan Anda berhasil terkirim. Terima kasih!";
            } else {
                $_SESSION['error_message'] = "Maaf, terjadi kesalahan. Silakan coba lagi.";
            }
            $stmt->close();
        }
    } else {
        $_SESSION['error_message'] = "Harap isi semua kolom yang wajib dengan benar.";
    }
    
    // Arahkan kembali ke halaman kontak untuk menampilkan notifikasi
    header("Location: kontak.php");
    exit();
}

// --- BAGIAN PENGAMBILAN DATA UNTUK DITAMPILKAN ---
$sql_pengaturan = "SELECT * FROM pengaturan WHERE id = 1";
$result_pengaturan = $conn->query($sql_pengaturan);
$pengaturan = ($result_pengaturan && $result_pengaturan->num_rows > 0) ? $result_pengaturan->fetch_assoc() : [];

$pageTitle = "Kontak Kami - " . ($pengaturan['nama_sekolah'] ?? 'SMKN Makassar');
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Hubungi Kami</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Kami siap membantu dan menjawab pertanyaan Anda.</p>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    
                    <div data-aos="fade-right">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Informasi Kontak</h2>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4"><div class="bg-blue-100 p-3 rounded-full"><i class="fas fa-map-marker-alt text-blue-600"></i></div><div><h3 class="font-semibold">Alamat</h3><p class="text-gray-600"><?= htmlspecialchars($pengaturan['alamat'] ?? 'Alamat belum diatur.') ?></p></div></div>
                            <div class="flex items-start gap-4"><div class="bg-blue-100 p-3 rounded-full"><i class="fas fa-phone-alt text-blue-600"></i></div><div><h3 class="font-semibold">Telepon</h3><p class="text-gray-600"><?= htmlspecialchars($pengaturan['telepon'] ?? '-') ?></p></div></div>
                            <div class="flex items-start gap-4"><div class="bg-blue-100 p-3 rounded-full"><i class="fas fa-envelope text-blue-600"></i></div><div><h3 class="font-semibold">Email</h3><p class="text-gray-600"><?= htmlspecialchars($pengaturan['email'] ?? '-') ?></p></div></div>
                        </div>
                        <hr class="my-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Media Sosial</h2>
                        <div class="flex space-x-4">
                            <a href="<?= htmlspecialchars($pengaturan['link_facebook'] ?? '#') ?>" target="_blank" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                            <a href="<?= htmlspecialchars($pengaturan['link_instagram'] ?? '#') ?>" target="_blank" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white"><i class="fab fa-instagram"></i></a>
                            <a href="<?= htmlspecialchars($pengaturan['link_youtube'] ?? '#') ?>" target="_blank" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white"><i class="fab fa-youtube"></i></a>
                            <a href="<?= htmlspecialchars($pengaturan['link_tiktok'] ?? '#') ?>" target="_blank" class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 hover:bg-blue-600 hover:text-white"><i class="fab fa-tiktok"></i></a>
                        </div>
                    </div>

                    <div data-aos="fade-left" data-aos-delay="150">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Titik Maps</h2>
                        
                        <div class="rounded-2xl shadow-lg border border-gray-100 p-2 overflow-hidden">
                            <iframe src="<?= htmlspecialchars($pengaturan['peta_lokasi'] ?? '') ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>