<aside class="w-64 bg-gray-800 text-white flex-shrink-0">
    <div class="p-6 text-2xl font-bold">
        <a href="dashboard.php">Admin Panel</a>
    </div>
    <nav class="mt-4 text-sm font-medium">
        <a href="dashboard.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'dashboard') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-tachometer-alt w-6 mr-3"></i> Dashboard
        </a>
        <a href="kelola-kepsek.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'kepsek') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-user-tie w-6 mr-3"></i> Kelola Kepsek
        </a>
        <a href="kelola-profil.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'profil') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-id-card w-6 mr-3"></i> Kelola Profil
        </a>
        <a href="kelola-fasilitas.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'fasilitas') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-building w-6 mr-3"></i> Kelola Fasilitas
        </a>
        <a href="kelola-berita.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'berita') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-newspaper w-6 mr-3"></i> Kelola Berita
        </a>
        <a href="kelola-album.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'galeri') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-images w-6 mr-3"></i> Kelola Galeri
        </a>
        <a href="kelola-guru.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'guru') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-chalkboard-teacher w-6 mr-3"></i> Kelola Guru
        </a>
        <!-- <a href="kelola-pesan.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'pesan') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-envelope-open-text w-6 mr-3"></i> Pesan Masuk
        </a> -->
        <a href="kelola-hero.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'hero') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-desktop w-6 mr-3"></i> Kelola Hero
        </a>
        <a href="pengaturan.php" class="flex items-center px-6 py-3 hover:bg-gray-700 <?= ($currentPage == 'pengaturan') ? 'bg-gray-700' : '' ?>">
            <i class="fas fa-cogs w-6 mr-3"></i> Pengaturan
        </a>
    </nav>
</aside>