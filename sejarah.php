<?php
// Hubungkan ke database
require_once 'includes/config.php';

// Ambil data profil sekolah dari database
$sql_profil = "SELECT * FROM profil_sekolah WHERE id = 1";
$result_profil = $conn->query($sql_profil);
// Jika tidak ada data, beri nilai default untuk menghindari error
$profil = ($result_profil->num_rows > 0) ? $result_profil->fetch_assoc() : [];

// Set judul halaman untuk header
$pageTitle = "Sejarah Sekolah - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Sejarah Sekolah</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Menelusuri Jejak Langkah dan Perkembangan Kami</p>
        </div>
    </section>

    <section class="py-16">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div data-aos="fade-right">
                    <img src="assets/images/profil/<?= htmlspecialchars($profil['sejarah_gambar'] ?? 'default_sejarah.jpg') ?>" alt="Foto gedung sekolah SMKN Makassar" class="rounded-lg shadow-xl w-full">
                </div>
                <div data-aos="fade-left">
                    <h2 class="text-3xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($profil['sejarah_judul'] ?? 'Judul Sejarah Sekolah') ?></h2>
                    <div class="text-gray-600 leading-relaxed space-y-4">
                        <?= nl2br(htmlspecialchars($profil['sejarah_teks'] ?? 'Teks sejarah belum diisi.')) ?>
                    </div>
                </div>
            </div>

            <div class="mt-20" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Linimasa Perkembangan</h2>
                <div class="relative max-w-2xl mx-auto">
                    <div class="absolute left-1/2 w-0.5 h-full bg-blue-200"></div>
                    
                    <div class="relative mb-8 flex items-center w-full">
                        <div class="w-1/2 pr-8 text-right">
                            <p class="font-bold text-lg text-blue-600"><?= htmlspecialchars($profil['timeline1_tahun'] ?? '') ?></p>
                            <p><?= htmlspecialchars($profil['timeline1_teks'] ?? '') ?></p>
                        </div>
                        <div class="absolute left-1/2 -ml-3 z-10 w-6 h-6 rounded-full bg-blue-600 border-4 border-white"></div>
                    </div>
                    
                    <div class="relative mb-8 flex items-center justify-end w-full">
                        <div class="w-1/2 pl-8 text-left">
                            <p class="font-bold text-lg text-blue-600"><?= htmlspecialchars($profil['timeline2_tahun'] ?? '') ?></p>
                            <p><?= htmlspecialchars($profil['timeline2_teks'] ?? '') ?></p>
                        </div>
                        <div class="absolute left-1/2 -ml-3 z-10 w-6 h-6 rounded-full bg-blue-600 border-4 border-white"></div>
                    </div>

                    <div class="relative mb-8 flex items-center w-full">
                        <div class="w-1/2 pr-8 text-right">
                            <p class="font-bold text-lg text-blue-600"><?= htmlspecialchars($profil['timeline3_tahun'] ?? '') ?></p>
                            <p><?= htmlspecialchars($profil['timeline3_teks'] ?? '') ?></p>
                        </div>
                        <div class="absolute left-1/2 -ml-3 z-10 w-6 h-6 rounded-full bg-blue-600 border-4 border-white"></div>
                    </div>

                    <div class="relative flex items-center justify-end w-full">
                        <div class="w-1/2 pl-8 text-left">
                            <p class="font-bold text-lg text-blue-600"><?= htmlspecialchars($profil['timeline4_tahun'] ?? '') ?></p>
                            <p><?= htmlspecialchars($profil['timeline4_teks'] ?? '') ?></p>
                        </div>
                        <div class="absolute left-1/2 -ml-3 z-10 w-6 h-6 rounded-full bg-blue-600 border-4 border-white"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php 
require_once 'includes/footer.php'; 
?>