<?php
// Koneksi ke database
include 'includes/config.php';
include 'includes/header.php';
?>

    <style>
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border-width: 0; }
        .section-container { padding-top: 5rem; padding-bottom: 5rem; padding-left: 1rem; padding-right: 1rem; }
        .swiper-pagination-bullet-active { background-color: #2563eb !important; }
        .header-scrolled { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); }
        @media (min-width: 1024px) { .section-container { padding-left: 6rem; padding-right: 6rem; } }
    </style>

    <main>

            <?php
            // Ambil data untuk hero section
            $sql_hero = "SELECT * FROM hero_section WHERE id = 1";
            $result_hero = $conn->query($sql_hero);
            $hero = ($result_hero && $result_hero->num_rows > 0) ? $result_hero->fetch_assoc() : [];
            ?>

        <section class="relative bg-gradient-to-r from-blue-700 to-blue-500 text-white mt-12 overflow-hidden">
            <div class="container mx-auto px-8 md:px-12 py-24 grid grid-cols-1 md:grid-cols-2 gap-8 items-center relative z-10">
                <div class="space-y-5" data-aos="fade-right">
                    <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight">
                        <?= htmlspecialchars($hero['judul'] ?? 'Judul Utama Belum Diatur') ?>
                    </h1>
                    <p class="text-lg text-blue-100">
                        <?= htmlspecialchars($hero['subjudul'] ?? 'Subjudul belum diatur.') ?>
                    </p>
                    <div class="flex flex-wrap gap-4 mt-8">
                        <button id="play-video-btn" 
                                data-video-url="<?= htmlspecialchars($hero['link_video'] ?? '') ?>" 
                                class="border-2 border-white text-white px-8 py-3 rounded-full font-semibold flex items-center space-x-2 hover:bg-white hover:text-blue-600 transition-all transform hover:scale-105">
                            <i class="fas fa-play"></i>
                            <span>Profil Video</span>
                        </button>
                    </div>
                </div>
                <div class="flex justify-center" data-aos="fade-left" data-aos-delay="200">
                    <img src="assets/images/hero/<?= htmlspecialchars($hero['gambar'] ?? 'default-hero.png') ?>" alt="Hero Section Image" class="max-w-md w-full rounded-lg">
                </div>
            </div>
        </section>

        <?php
            
            // Ambil data sambutan kepala sekolah
            $sql_sambutan = "SELECT nama, sambutan, foto FROM sambutan_kepsek WHERE id = 1";
            $result_sambutan = $conn->query($sql_sambutan);
            $sambutan = ($result_sambutan && $result_sambutan->num_rows > 0) ? $result_sambutan->fetch_assoc() : [];
        ?>

        <section class="section-container">
            <div class="container mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-center bg-white p-8 rounded-2xl shadow-lg border border-gray-100">
                    
                    <div class="flex justify-center lg:col-span-1" data-aos="fade-right">
                        <img src="assets/images/profil/<?= htmlspecialchars($sambutan['foto'] ?? 'default-kepsek.png') ?>" alt="Foto Kepala Sekolah" class="rounded-lg shadow-md w-full max-w-[280px] object-cover">
                    </div>

                    <div class="lg:col-span-2" data-aos="fade-left" data-aos-delay="150">
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">Sambutan Hangat dari Kepala Sekolah</h2>
                        
                        <div class="relative text-gray-600 italic">
                            <i class="fas fa-quote-left text-blue-100 text-6xl absolute -top-4 -left-6"></i>
                            <p class="relative z-10 leading-relaxed text-lg">
                                <?php
                                    // FIX: Menggunakan variabel $sambutan
                                    $kutipan = substr(strip_tags($sambutan['sambutan'] ?? 'Sambutan belum diisi.'), 0, 200);
                                    echo htmlspecialchars($kutipan) . '...';
                                ?>
                            </p>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200 flex flex-col sm:flex-row items-start sm:items-center justify-between">
                            <div>
                                <p class="font-bold text-gray-800 text-lg"><?= htmlspecialchars($sambutan['nama'] ?? 'Nama Kepala Sekolah') ?></p>
                                <p class="text-sm text-gray-500">Kepala Sekolah</p>
                            </div>
                            <a href="sambutan-kepsek.php" class="mt-4 sm:mt-0 bg-blue-100 text-blue-700 px-6 py-2 rounded-full font-semibold hover:bg-blue-200 transition-colors">
                                Baca Selengkapnya
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <?php
            
            // Ambil data profil kepala sekolah
            $sql_profil = "SELECT * FROM profil_sekolah WHERE id = 1";
            $result_profil = $conn->query($sql_profil);
            $profil = ($result_profil && $result_profil->num_rows > 0) ? $result_profil->fetch_assoc() : [];
        ?>

        <section class="section-container -mt-12">
            <div class="container mx-auto">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="text-center lg:text-left" data-aos="fade-right">
                        <p class="text-blue-600 font-semibold uppercase tracking-wider mb-2">Jejak Langkah Kami</p>
                        
                        <h2 class="text-3xl font-bold text-gray-800 mb-4">
                            <?= htmlspecialchars($profil['sejarah_judul'] ?? 'Sejarah Singkat Sekolah') ?>
                        </h2>
                        
                        <p class="text-gray-600 leading-relaxed mb-6">
                            <?= nl2br(htmlspecialchars(substr($profil['sejarah_teks'] ?? 'Teks sejarah belum diisi.', 0, 250))) ?>...
                        </p>
                        
                        <a href="sejarah.php" class="bg-blue-600 text-white px-8 py-3 rounded-full font-semibold hover:bg-blue-700 transition-transform transform hover:scale-105 inline-block">
                            Baca Selengkapnya
                        </a>
                    </div>
                    <div class="flex justify-center" data-aos="fade-left">
                        <img src="assets/images/profil/<?= htmlspecialchars($profil['sejarah_gambar'] ?? 'default_sejarah.jpg') ?>" alt="Foto Sejarah Sekolah" class="rounded-lg shadow-lg w-full max-w-md object-cover">
                    </div>
                </div>
            </div>
        </section>
        
        <section class="bg-blue-50 section-container overflow-hidden">
            <div class="container mx-auto">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-3xl font-bold mb-2 text-gray-800">Sejarah Kepemimpinan Sekolah</h2>
                    <p class="text-gray-600">Para kepala sekolah yang pernah memimpin.</p>
                </div>

                <div class="swiper kepsek-swiper" data-aos="fade-up">
                    <div class="swiper-wrapper pb-10">

                        <?php 
                            $result_sejarah_kepsek = $conn->query("SELECT * FROM sejarah_kepsek "); 
                        ?>
                        <?php if ($result_sejarah_kepsek && $result_sejarah_kepsek->num_rows > 0): ?>
                            <?php while($kepsek = $result_sejarah_kepsek->fetch_assoc()): ?>
                            <div class="swiper-slide h-auto">
                                <div class="bg-white rounded-xl shadow-md p-6 flex flex-col items-center text-center h-full">
                                    <img src="assets/images/kepsek/<?= htmlspecialchars($kepsek['foto']) ?>" alt="Foto <?= htmlspecialchars($kepsek['nama']) ?>" class="w-28 h-28 rounded-full mb-4 object-cover border-4 border-gray-200">
                                    <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($kepsek['nama']) ?></h3>
                                    <p class="text-blue-600 font-medium mt-1"><?= htmlspecialchars($kepsek['periode_jabatan']) ?></p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="col-span-3 text-center text-gray-500">Data kepemimpinan belum tersedia.</p>
                        <?php endif; ?>

                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section>


        <?php
        // Ambil 3 berita terbaru (pastikan ada)
        $sql_berita = "SELECT id, judul, kategori, gambar, isi, tanggal_publikasi FROM berita ORDER BY tanggal_publikasi DESC LIMIT 5";
        $result_berita = $conn->query($sql_berita);

        // Simpan hasilnya ke dalam sebuah array agar mudah diakses
        $berita_list = [];
        if ($result_berita && $result_berita->num_rows > 0) {
            while ($row = $result_berita->fetch_assoc()) {
                $berita_list[] = $row;
            }
        }
        ?>
        
        <section class="section-container">
            <div class="container mx-auto">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-3xl font-bold mb-2 text-gray-800">Sorotan Berita Sekolah</h2>
                    <p class="text-gray-600">Ikuti perkembangan dan prestasi terbaru dari sekolah kami.</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <?php if (!empty($berita_list)): ?>
                        
                        <?php $berita_utama = $berita_list[0]; ?>
                        <a href="detail-berita.php?id=<?= $berita_utama['id'] ?>" class="lg:col-span-2 bg-white rounded-xl shadow-lg overflow-hidden flex flex-col group" data-aos="fade-right">
                            <img src="assets/images/berita/<?= htmlspecialchars($berita_utama['gambar']) ?>" alt="<?= htmlspecialchars($berita_utama['judul']) ?>" class="w-full h-64 sm:h-80 object-cover">
                            <div class="p-6 flex-1 flex flex-col">
                                <p class="text-blue-600 font-semibold text-sm"><?= htmlspecialchars($berita_utama['kategori']) ?></p>
                                <h3 class="text-2xl font-bold text-gray-800 mt-2 group-hover:text-blue-700 transition"><?= htmlspecialchars($berita_utama['judul']) ?></h3>
                                <p class="text-gray-600 mt-4 flex-grow"><?= htmlspecialchars(substr(strip_tags($berita_utama['isi']), 0, 100)) ?>...</p>
                                <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-500"><?= date('d F Y', strtotime($berita_utama['tanggal_publikasi'])) ?></div>
                            </div>
                        </a>

                        <div class="space-y-6" data-aos="fade-left" data-aos-delay="150">
                            <?php for ($i = 1; $i < count($berita_list); $i++): ?>
                                <?php $berita_samping = $berita_list[$i]; ?>
                                <a href="detail-berita.php?id=<?= $berita_samping['id'] ?>" class="flex items-center gap-4 group">
                                    <img src="assets/images/berita/<?= htmlspecialchars($berita_samping['gambar']) ?>" alt="<?= htmlspecialchars($berita_samping['judul']) ?>" class="w-24 h-24 rounded-lg object-cover flex-shrink-0">
                                    <div>
                                        <h4 class="font-bold text-gray-800 group-hover:text-blue-600 transition"><?= htmlspecialchars($berita_samping['judul']) ?></h4>
                                        <p class="text-sm text-gray-500 mt-1"><?= date('d F Y', strtotime($berita_samping['tanggal_publikasi'])) ?></p>
                                    </div>
                                </a>
                            <?php endfor; ?>
                        </div>

                    <?php else: ?>
                        <p class="col-span-3 text-center text-gray-500">Belum ada berita untuk ditampilkan.</p>
                    <?php endif; ?>
                    
                </div>
            </div>
        </section>

        <section class="bg-blue-50 section-container overflow-hidden">
            <div class="container mx-auto">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-3xl font-bold mb-2 text-gray-800">Galeri Kegiatan Sekolah</h2>
                    <p class="text-gray-600">Momen-momen terbaik dari berbagai kegiatan kami.</p>
                </div>
                <div class="swiper mySwiper" data-aos="fade-up">
                    <div class="swiper-wrapper">
                        
                        <?php 
                            $result_galeri = $conn->query("SELECT * FROM foto_galeri ORDER BY tanggal_upload DESC LIMIT 8"); 
                            if ($result_galeri && $result_galeri->num_rows > 0): 
                        ?>
                            <?php while($foto = $result_galeri->fetch_assoc()): ?>
                            <div class="swiper-slide">
                                <div class="relative group h-96 rounded-lg overflow-hidden shadow-lg">
                                    <img src="assets/images/galeri/<?= htmlspecialchars($foto['nama_file']) ?>" alt="Foto Galeri Sekolah" class="w-full h-full object-cover">
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="swiper-slide">
                                <div class="h-96 flex items-center justify-center text-gray-500">
                                    Belum ada foto di galeri.
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                    <div class="swiper-pagination mt-8 relative"></div>
                </div>
            </div>
        </section>

        <?php 
            $sql_pengaturan = "SELECT highlight_announcement, highlight_exam, highlight_holiday FROM pengaturan WHERE id = 1";
            $result_pengaturan = $conn->query($sql_pengaturan);
            $pengaturan = ($result_pengaturan->num_rows > 0) ? $result_pengaturan->fetch_assoc() : [];
        ?>

        <section class="py-16 px-16">
            <div class="container mx-auto px-6 lg:px-8">
                <h2 class="text-3xl text-center font-bold text-gray-800">Informasi</h2>
                <p class="text-gray-600 text-center">Informasi Seputar Kegiatan Sekolah.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6  my-12">
                    <div class="bg-blue-100 border-l-4 border-blue-500 p-6 rounded-r-lg" data-aos="fade-up">
                        <div class="flex items-center">
                            <i class="fas fa-bullhorn text-blue-500 text-2xl mr-4"></i>
                            <div>
                                <h3 class="font-bold text-blue-800">PENGUMUMAN</h3>
                                <p class="text-sm text-blue-700"><?= htmlspecialchars($pengaturan['highlight_announcement'] ?? 'Tidak ada pengumuman.') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 p-6 rounded-r-lg" data-aos="fade-up" data-aos-delay="150">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-yellow-500 text-2xl mr-4"></i>
                            <div>
                                <h3 class="font-bold text-yellow-800">JADWAL UJIAN</h3>
                                <p class="text-sm text-yellow-700"><?= htmlspecialchars($pengaturan['highlight_exam'] ?? 'Tidak ada jadwal ujian.') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-red-100 border-l-4 border-red-500 p-6 rounded-r-lg" data-aos="fade-up" data-aos-delay="300">
                        <div class="flex items-center">
                            <i class="fas fa-flag text-red-500 text-2xl mr-4"></i>
                            <div>
                                <h3 class="font-bold text-red-800">HARI LIBUR</h3>
                                <p class="text-sm text-red-700"><?= htmlspecialchars($pengaturan['highlight_holiday'] ?? 'Tidak ada info hari libur.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
                            
    <?php require_once 'includes/footer.php'; ?>
    
    <button id="back-to-top" class="hidden fixed bottom-5 right-5 bg-blue-600 text-white w-12 h-12 rounded-full shadow-lg hover:bg-blue-700 transition-opacity z-50 flex justify-center items-center">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/basiclightbox@5.0.4/dist/basicLightbox.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Inisialisasi Animasi AOS
            AOS.init({
                duration: 800,
                once: true, // Animasi hanya berjalan sekali
            });

            // 2. Inisialisasi Swiper.js
            new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: { delay: 3000, disableOnInteraction: false },
                breakpoints: { 640: { slidesPerView: 2 }, 1024: { slidesPerView: 3 } },
            });

            // 3. Inisialisasi Carousel untuk Kepala Sekolah
            if (typeof Swiper !== 'undefined') {
                new Swiper(".kepsek-swiper", {
                    // Jumlah slide yang terlihat
                    slidesPerView: 1,
                    // Jarak antar slide
                    spaceBetween: 30,
                    // Pagination (titik-titik di bawah)
                    pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                    },
                    // Pengaturan responsif
                    breakpoints: {
                        // Untuk layar 768px atau lebih besar
                        768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                        },
                        // Untuk layar 1024px atau lebih besar
                        1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                        },
                    },
                });
            }


            // 4. Popup Video
            const videoBtn = document.getElementById('play-video-btn');
            if (videoBtn && typeof basicLightbox !== 'undefined') {
                videoBtn.addEventListener('click', () => {
                    // Ambil URL dari atribut data-video-url pada tombol
                    const videoUrl = videoBtn.getAttribute('data-video-url');

                    // Hanya tampilkan popup jika URL-nya ada
                    if (videoUrl) {
                        basicLightbox.create(`
                            <iframe width="560" height="315" src="${videoUrl}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        `).show();
                    } else {
                        // Beri tahu pengguna jika link belum diatur
                        alert('Link video belum diatur di dasbor admin.');
                    }
                });
            }

            // 5. Tombol Back to Top
            const backToTopBtn = document.getElementById('back-to-top');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 300) {
                    backToTopBtn.classList.remove('hidden');
                } else {
                    backToTopBtn.classList.add('hidden');
                }
            });
            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // 6. Header dengan efek shadow saat scroll
            const header = document.getElementById('main-header');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 10) {
                    header.classList.add('header-scrolled');
                } else {
                    header.classList.remove('header-scrolled');
                }
            });
            
            // 7. Update Tahun Copyright di Footer
            document.getElementById('current-year').textContent = new Date().getFullYear();
        });
    </script>
</body>
</html>