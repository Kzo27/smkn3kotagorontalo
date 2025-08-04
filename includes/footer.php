<?php
// Ambil data pengaturan untuk ditampilkan di footer
if (isset($conn)) { // Cek jika koneksi masih ada
    $sql_pengaturan = "SELECT * FROM pengaturan WHERE id = 1";
    $result_pengaturan = $conn->query($sql_pengaturan);
    $pengaturan = ($result_pengaturan && $result_pengaturan->num_rows > 0) ? $result_pengaturan->fetch_assoc() : [];
} else {
    $pengaturan = []; // Beri nilai default jika koneksi tidak ada
}
?>
    <footer class="bg-gray-800 text-white">
        <div class="container mx-auto px-8 py-12 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <div>
                <h4 class="text-lg font-semibold mb-4"><?= htmlspecialchars($pengaturan['nama_sekolah'] ?? 'SMK Negeri Makassar') ?></h4>
                <p class="text-sm text-gray-400 leading-relaxed text-justify">
                    Membentuk generasi kreatif, dan berakhlak mulia yang mampu bersaing.
                </p>
                <div class="flex space-x-4 mt-6">
                    <a href="<?= htmlspecialchars($pengaturan['link_facebook'] ?? '#') ?>" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= htmlspecialchars($pengaturan['link_instagram'] ?? '#') ?>" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                    <a href="<?= htmlspecialchars($pengaturan['link_youtube'] ?? '#') ?>" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                    <a href="<?= htmlspecialchars($pengaturan['link_tiktok'] ?? '#') ?>" target="_blank" class="text-gray-400 hover:text-white"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4">Tautan Cepat</h5>
                <ul class="space-y-2 text-sm">
                    <li><a href="index.php" class="text-gray-400 hover:text-white">Beranda</a></li>
                    <li><a href="sejarah.php" class="text-gray-400 hover:text-white">Profil</a></li>
                    <li><a href="berita.php" class="text-gray-400 hover:text-white">Berita</a></li>
                    <li><a href="kontak.php" class="text-gray-400 hover:text-white">Kontak</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4">Hubungi Kami</h5>
                <address class="text-sm text-gray-400 not-italic space-y-2">
                    <p><i class="fas fa-map-marker-alt w-4 mr-2"></i><?= htmlspecialchars($pengaturan['alamat'] ?? '-') ?></p>
                    <p><i class="fas fa-envelope w-4 mr-2"></i><?= htmlspecialchars($pengaturan['email'] ?? '-') ?></p>
                    <p><i class="fas fa-phone-alt w-4 mr-2"></i><?= htmlspecialchars($pengaturan['telepon'] ?? '-') ?></p>
                </address>
            </div>
            <div>
                <h5 class="text-lg font-semibold mb-4">Jam Pelayanan</h5>
                <div class="text-sm text-gray-400 space-y-3">
                    <div class="flex">
                        <i class="fas fa-clock w-4 mr-3 mt-1 text-blue-400"></i>
                        <div>
                            <p class="font-semibold text-gray-200">Senin - Jumat</p>
                            <p>08:00 - 14:00 WITA</p>
                        </div>
                    </div>
                    <div class="flex">
                        <i class="fas fa-times-circle w-4 mr-3 mt-1 text-red-400"></i>
                        <div>
                            <p class="font-semibold text-gray-200">Sabtu & Minggu</p>
                            <p>Tutup</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-gray-900 py-4 text-center text-sm text-gray-500">
            © <span id="current-year"></span> <?= htmlspecialchars($pengaturan['nama_sekolah'] ?? 'SMK Negeri Makassar') ?>. All Rights Reserved.
        </div>
    </footer>
    
    <button id="back-to-top" class="hidden fixed bottom-5 right-5 bg-blue-600 text-white w-12 h-12 rounded-full shadow-lg hover:bg-blue-700 transition-opacity z-50 flex justify-center items-center">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/basiclightbox@5.0.4/dist/basicLightbox.min.js"></script>

    <script src="assets/js/script.js"></script>
    
    <?php if(isset($conn)) { $conn->close(); } // Tutup koneksi di sini ?>


    <script src="assets/js/script.js"></script> <script>
        // Script spesifik untuk halaman ini bisa ditaruh di sini atau di file script.js
        // Contoh: Inisialisasi AOS
        AOS.init({
            duration: 800,
            once: true,
        });
        
        // Contoh: Logic untuk menu mobile & back-to-top (bisa dipindah ke script.js)
        const menuBtn = document.getElementById('menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (menuBtn && mobileMenu) {
            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
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

        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>
</body>
</html>