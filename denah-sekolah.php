<?php
// Hubungkan ke database
require_once 'includes/config.php';

// FIX: Ambil nama file gambar DENAH SEKOLAH dari database
$sql_profil = "SELECT denah_gambar FROM profil_sekolah WHERE id = 1";
$result_profil = $conn->query($sql_profil);
$profil = ($result_profil->num_rows > 0) ? $result_profil->fetch_assoc() : [];

// FIX: Set judul halaman untuk Denah Sekolah
$pageTitle = "Denah Sekolah - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Denah Sekolah</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Temukan lokasi dan tata letak gedung sekolah kami.</p>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <p class="max-w-3xl mx-auto text-gray-600 mb-12" data-aos="fade-up">Klik pada gambar denah di bawah ini untuk melihat tampilan yang lebih besar dan jelas.</p>
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 inline-block" data-aos="zoom-in">
                
                <a id="denah-link" href="assets/images/profil/<?= htmlspecialchars($profil['denah_gambar'] ?? 'default-denah.png') ?>">
                    <img src="assets/images/profil/<?= htmlspecialchars($profil['denah_gambar'] ?? 'default-denah.png') ?>" alt="Denah Sekolah" class="w-full h-auto max-w-4xl mx-auto">
                </a>
            
            </div>
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const denahLink = document.getElementById('denah-link');
    if (denahLink && typeof basicLightbox !== 'undefined') {
        denahLink.addEventListener('click', function(event) {
            event.preventDefault();
            const imageUrl = this.getAttribute('href');
            basicLightbox.create(`<img src="${imageUrl}">`).show();
        });
    }
});
</script>