<?php
session_start();
require_once '../includes/config.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM admin_users WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            // Verifikasi password yang di-hash
            if (password_verify($password, $user['password'])) {
                // Password benar, buat session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                header("Location: dashboard.php");
                exit();
            }
        }
        $error_message = "Username atau password salah.";
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="w-full max-w-md">
        <form action="login.php" method="POST" class="bg-white shadow-lg rounded-xl px-8 pt-6 pb-8 mb-4">
            <div class="text-center mb-8">
                <img src="../assets/images/logo/icon.jpg" alt="Logo Sekolah" class="w-20 h-20 mx-auto mb-2">
                <h1 class="text-2xl font-bold text-gray-800">Admin Panel Login</h1>
            </div>

            <?php if(!empty($error_message)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= $error_message ?></span>
                </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                <input id="username" name="username" type="text" placeholder="Username" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input id="password" name="password" type="password" placeholder="******************" class="shadow-sm appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Login
                </button>
            </div>
        </form>
        <p class="text-center text-gray-500 text-xs">
            &copy;<?= date('Y') ?> Tim IT UNG. All rights reserved.
        </p>
    </div>
</body>
</html>