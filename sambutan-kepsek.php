<?php
// Hubungkan ke database
require_once 'includes/config.php';

// Ambil data sambutan kepala sekolah dari database
$sql_sambutan = "SELECT * FROM sambutan_kepsek WHERE id = 1";
$result_sambutan = $conn->query($sql_sambutan);
$sambutan = ($result_sambutan && $result_sambutan->num_rows > 0) ? $result_sambutan->fetch_assoc() : [];

// Set judul halaman untuk header
$pageTitle = "Sambutan Kepala Sekolah - " . ($pengaturan['nama_sekolah'] ?? 'SMKN Makassar');
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center px-4">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Sambutan Kepala Sekolah</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Pesan Hangat dari Pimpinan Sekolah</p>
        </div>
    </section>

    <section class="py-16">
        <div class="container mx-auto max-w-4xl px-6 lg:px-8">
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                <div class="flex flex-col sm:flex-row gap-8 items-center">
                    <div class="flex-shrink-0" data-aos="fade-up">
                        <img src="assets/images/profil/<?= htmlspecialchars($sambutan['foto'] ?? 'default-kepsek.png') ?>" alt="Foto Kepala Sekolah" class="w-48 h-48 rounded-full object-cover border-4 border-gray-100 shadow-md">
                    </div>
                    <div class="text-center sm:text-left" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($sambutan['nama'] ?? 'Nama Kepala Sekolah') ?></h2>
                        <p class="text-lg text-gray-500 mt-1">Kepala Sekolah</p>
                    </div>
                </div>

                <hr class="my-8">

                <div class="prose max-w-none text-gray-700 leading-relaxed" data-aos="fade-up" data-aos-delay="200">
                    <?= nl2br(htmlspecialchars($sambutan['sambutan'] ?? 'Teks sambutan lengkap belum diisi.')) ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once 'includes/footer.php'; ?>