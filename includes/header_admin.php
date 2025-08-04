<header class="bg-white shadow-md p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800"><?= $pageTitle ?? 'Dashboard' ?></h1>
    <div class="flex items-center">
       <p class="mr-4">Selamat datang, <span class="font-semibold">Admin</span></p>
       <a href="logout.php" class="text-gray-600 hover:text-red-500" title="Logout">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </a>
   </div>
</header>