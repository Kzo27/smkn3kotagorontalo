<?php
require_once 'auth.php';

require_once '../includes/config.php';

// Ambil data sambutan kepsek saat ini dari tabel `sambutan_kepsek`
$sql_sambutan = "SELECT id, nama, sambutan, foto FROM sambutan_kepsek WHERE id = 1";
$result_sambutan = $conn->query($sql_sambutan);
$sambutan = ($result_sambutan->num_rows > 0) ? $result_sambutan->fetch_assoc() : null;

// Ambil data sejarah kepsek dari tabel `sejarah_kepsek`
$sql_sejarah = "SELECT * FROM sejarah_kepsek ORDER BY id ASC";
$result_sejarah = $conn->query($sql_sejarah);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Kepala Sekolah - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
<div class="flex h-screen bg-gray-100">
    <?php $currentPage = 'kepsek'; require_once '../includes/sidebar_admin.php'; ?>

    <div class="flex-1 flex flex-col overflow-hidden">
        <?php $pageTitle = 'Kelola Kepala Sekolah'; require_once '../includes/header_admin.php'; ?>
        
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-8">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                </div>
            <?php endif; ?>

            <div class="bg-white p-8 rounded-2xl shadow-lg mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-4">Sambutan Kepala Sekolah Saat Ini</h2>
                <form action="proses_update_kepsek_saat_ini.php" method="POST" enctype="multipart/form-data" class="mt-4">
                    <input type="hidden" name="kepsek_foto_lama" value="<?= htmlspecialchars($sambutan['foto'] ?? '') ?>">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-6">
                            <div>
                                <label for="kepsek_nama" class="block text-sm font-medium text-gray-700">Nama Kepala Sekolah</label>
                                <input type="text" name="kepsek_nama" value="<?= htmlspecialchars($sambutan['nama'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="kepsek_sambutan" class="block text-sm font-medium text-gray-700">Teks Sambutan</label>
                                <textarea name="kepsek_sambutan" rows="8" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($sambutan['sambutan'] ?? '') ?></textarea>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Foto Kepala Sekolah</label>
                            <div class="mt-1">
                                <img id="kepsek_preview" src="<?= !empty($sambutan['foto']) ? '../assets/images/profil/' . htmlspecialchars($sambutan['foto']) : 'https://via.placeholder.com/150' ?>" class="w-40 h-40 rounded-full object-cover border-4 border-gray-200 bg-gray-100">
                                <input type="file" name="kepsek_foto" id="kepsek_foto_input" class="hidden">
                                <button type="button" onclick="document.getElementById('kepsek_foto_input').click()" class="mt-4 ms-8 text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg">
                                    Ganti Foto
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-6 border-t border-gray-200 pt-6">
                        <button type="submit" class="bg-blue-600 text-white font-bold py-2 px-6 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Simpan Sambutan</button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">Daftar Sejarah Kepala Sekolah</h2>
                    <a href="form-kepsek.php" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tambah Data
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100 text-sm font-semibold text-gray-600">
                                <th class="py-3 px-4">Foto</th>
                                <th class="py-3 px-4">Nama</th>
                                <th class="py-3 px-4">Periode Jabatan</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php while($row = $result_sejarah->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4"><img src="../assets/images/kepsek/<?= htmlspecialchars($row['foto']) ?>" class="w-12 h-12 rounded-full object-cover"></td>
                                <td class="py-3 px-4 font-medium text-gray-900"><?= htmlspecialchars($row['nama']) ?></td>
                                <td class="py-3 px-4 text-gray-600"><?= htmlspecialchars($row['periode_jabatan']) ?></td>
                                <td class="py-3 px-4 text-center space-x-4">
                                    <a href="form-kepsek.php?id=<?= $row['id'] ?>" class="text-yellow-500 hover:text-yellow-700" title="Edit"><i class="fas fa-edit fa-lg"></i></a>
                                    <a href="hapus-kepsek.php?id=<?= $row['id'] ?>" class="text-red-500 hover:text-red-700" title="Hapus" onclick="return confirm('Yakin?')"><i class="fas fa-trash-alt fa-lg"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Script untuk pratinjau foto kepala sekolah
    const kepsekFotoInput = document.getElementById('kepsek_foto_input');
    const kepsekPreview = document.getElementById('kepsek_preview');
    if(kepsekFotoInput) {
        kepsekFotoInput.addEventListener('change', function(event) {
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    kepsekPreview.src = e.target.result;
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        });
    }
</script>

</body>
</html>