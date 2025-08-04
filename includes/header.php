<?php
// Di masa depan, file config bisa dipanggil di sini jika header butuh data dari DB
// require_once 'config.php'; 
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'SMK Negeri Makassar' ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/basiclightbox@5.0.4/dist/basicLightbox.min.css">

    <link rel="stylesheet" href="assets/css/style.css"> 
    <style>
        .header-scrolled { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1); }
    </style>

    
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">

    <header id="main-header" class="bg-white/80 backdrop-blur-lg sticky top-0 z-50 transition-shadow duration-300">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="flex items-center space-x-3">
                <img src="assets/images/logo.jpg" alt="SMP NEGERI 2 TELAGA" class="h-10">
                <span class="text-xl font-bold text-gray-800">SMPN 2 TELAGA</span>
            </a>
            <nav class="hidden md:flex items-center space-x-8 text-gray-600 font-medium">
                <a href="index.php" class="hover:text-blue-600 transition-colors">Beranda</a>
                <div class="relative group">
                    <button class="hover:text-blue-600 transition-colors flex items-center">
                        Profil Sekolah <i class="fas fa-chevron-down text-xs ml-2"></i>
                    </button>
                    
                    <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md w-56 py-2 z-10 pt-4">
                        <a href="sejarah.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Sejarah Sekolah</a>
                        <a href="visi-misi.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Visi & Misi</a>
                        <a href="struktur-organisasi.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Struktur Organisasi</a>
                        <a href="guru.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Guru & Tenaga Kependidikan</a>
                        <a href="denah-sekolah.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Denah Sekolah</a>
                        <a href="fasilitas.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">Fasilitas Sekolah</a>
                    </div>
                </div>
                <a href="berita.php" class="hover:text-blue-600 transition-colors">Berita</a>
                <a href="galeri.php" class="hover:text-blue-600 transition-colors">Galeri</a>
            </nav>
            <div class="flex items-center space-x-4">
                <a href="kontak.php" class="bg-blue-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition-all transform hover:scale-105">Kontak Kami</a>
                <div class="md:hidden">
                    <button id="menu-btn" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                        <span class="sr-only">Buka Menu</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div id="mobile-menu" class="hidden md:hidden bg-white shadow-md border-t">
        <nav class="flex flex-col p-4 space-y-1">
            <a href="index.php" class="text-gray-700 font-medium block px-3 py-2 rounded-md hover:bg-gray-100">Beranda</a>
            
            <div>
                <button id="profil-toggle-btn" class="w-full text-left text-gray-700 font-medium flex justify-between items-center px-3 py-2 rounded-md hover:bg-gray-100">
                    <span>Profil Sekolah</span>
                    <i id="profil-arrow" class="fas fa-chevron-down transition-transform duration-300"></i>
                </button>
                <div id="profil-submenu" class="hidden pl-4 pt-2 space-y-1">
                    <a href="sejarah.php" class="block text-gray-600 px-3 py-2 rounded-md hover:bg-gray-100">Sejarah Sekolah</a>
                    <a href="visi-misi.php" class="block text-gray-600 px-3 py-2 rounded-md hover:bg-gray-100">Visi & Misi</a>
                    <a href="struktur-organisasi.php" class="block text-gray-600 px-3 py-2 rounded-md hover:bg-gray-100">Struktur Organisasi</a>
                    <a href="guru.php" class="block text-gray-600 px-3 py-2 rounded-md hover:bg-gray-100">Guru & Staf</a>
                    <a href="denah-sekolah.php" class="block text-gray-600 px-3 py-2 rounded-md hover:bg-gray-100">Denah Sekolah</a>
                    <a href="fasilitas.php" class="block text-gray-600 px-3 py-2 rounded-md hover:bg-gray-100">Fasilitas Sekolah</a>
                </div>
            </div>

            <a href="berita.php" class="text-gray-700 font-medium block px-3 py-2 rounded-md hover:bg-gray-100">Berita</a>
            <a href="galeri.php" class="text-gray-700 font-medium block px-3 py-2 rounded-md hover:bg-gray-100">Galeri</a>
            <a href="kontak.php" class="text-gray-700 font-medium block px-3 py-2 rounded-md hover:bg-gray-100">Kontak Kami</a>
        </nav>
    </div>

    