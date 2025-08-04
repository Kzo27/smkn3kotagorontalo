<?php
// Hubungkan ke database
require_once 'includes/config.php';

// Ambil nama file gambar struktur organisasi dari database
$sql_profil = "SELECT org_chart_gambar FROM profil_sekolah WHERE id = 1";
$result_profil = $conn->query($sql_profil);
$profil = ($result_profil->num_rows > 0) ? $result_profil->fetch_assoc() : [];

// Set judul halaman untuk header
$pageTitle = "Struktur Organisasi - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Struktur Organisasi</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Hierarki Kepemimpinan dan Tata Kelola Sekolah</p>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <p class="max-w-3xl mx-auto text-gray-600 mb-12" data-aos="fade-up">Berikut adalah bagan struktur organisasi yang menggambarkan alur koordinasi dan tanggung jawab di lingkungan sekolah kami.</p>
            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100" data-aos="zoom-in">
                
                <img src="assets/images/profil/<?= htmlspecialchars($profil['org_chart_gambar'] ?? 'default-chart.png') ?>" alt="Diagram Struktur Organisasi SMKN Negeri Makassar" class="w-full h-auto">
            
            </div>
        </div>
    </section>
</main>
<?php require_once 'includes/footer.php'; ?>