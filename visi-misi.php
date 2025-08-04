<?php
// Hubungkan ke database
require_once 'includes/config.php';

// Ambil data profil sekolah dari database
$sql_profil = "SELECT visi, misi FROM profil_sekolah WHERE id = 1";
$result_profil = $conn->query($sql_profil);
$profil = ($result_profil->num_rows > 0) ? $result_profil->fetch_assoc() : [];

// Set judul halaman untuk header
$pageTitle = "Visi & Misi - SMKN Negeri Makassar";
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="bg-blue-600 text-white py-20">
        <div class="container mx-auto text-center">
            <h1 class="text-4xl font-bold" data-aos="fade-up">Visi & Misi</h1>
            <p class="text-blue-200 mt-2" data-aos="fade-up" data-aos-delay="100">Landasan dan Arah Tujuan Pendidikan Kami</p>
        </div>
    </section>

    <section class="py-20">
        <div class="container mx-auto px-6 lg:px-8 space-y-16">
            <div class="bg-white p-8 lg:p-12 rounded-2xl shadow-lg border border-gray-100" data-aos="fade-right">
                <div class="flex items-start gap-6">
                    <div class="bg-blue-100 text-blue-600 p-4 rounded-full flex-shrink-0">
                        <i class="fas fa-eye text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">Visi Sekolah</h2>
                        <p class="text-gray-600 text-lg leading-relaxed">
                            "<?= htmlspecialchars($profil['visi'] ?? 'Visi sekolah belum diisi.') ?>"
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 lg:p-12 rounded-2xl shadow-lg border border-gray-100" data-aos="fade-left">
                <div class="flex items-start gap-6">
                    <div class="bg-green-100 text-green-600 p-4 rounded-full flex-shrink-0">
                        <i class="fas fa-bullseye text-3xl"></i>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">Misi Sekolah</h2>
                        <ol class="list-decimal list-inside text-gray-600 text-lg space-y-3">
                            <?php
                            // Ambil teks misi, lalu pecah menjadi array berdasarkan baris baru
                            $misi_points = explode("\n", trim($profil['misi'] ?? ''));

                            // Looping untuk setiap poin misi dan tampilkan sebagai <li>
                            foreach ($misi_points as $point) {
                                $trimmed_point = trim($point);
                                if (!empty($trimmed_point)) {
                                    echo "<li>" . htmlspecialchars($trimmed_point) . "</li>";
                                }
                            }
                            ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require_once 'includes/footer.php'; ?>