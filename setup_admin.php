<?php
// admin/setup_admin.php
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

$username = 'admin';
$password_plain = 'password123';
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);
$nama_lengkap = 'Administrator HMAN';

// Cek apakah akun sudah ada
$stmt = $pdo->prepare("SELECT id FROM admin WHERE username = :username");
$stmt->execute([':username' => $username]);
$exists = $stmt->fetch();

if ($exists) {
    // Update password dengan hash yang valid
    $update = $pdo->prepare("UPDATE admin SET password = :pass, nama_lengkap = :nama WHERE username = :username");
    $update->execute([
        ':pass' => $hashed_password,
        ':nama' => $nama_lengkap,
        ':username' => $username
    ]);
    $pesan = "Akun admin berhasil di-reset ulang!";
} else {
    // Buat akun baru
    $insert = $pdo->prepare("INSERT INTO admin (username, password, nama_lengkap) VALUES (:username, :pass, :nama)");
    $insert->execute([
        ':username' => $username,
        ':pass' => $hashed_password,
        ':nama' => $nama_lengkap
    ]);
    $pesan = "Akun admin berhasil dibuat!";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Admin HMAN Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-brandCream min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white border-2 border-amber-300 rounded-3xl p-8 text-center space-y-4 shadow-md">
        <div class="text-4xl">✨</div>
        <h1 class="text-xl font-black text-amber-900"><?= $pesan ?></h1>
        <div class="bg-amber-50 p-4 rounded-xl text-xs text-left space-y-1 border border-amber-200">
            <p><b>Username:</b> admin</p>
            <p><b>Password:</b> password123</p>
        </div>
        <a href="login.php" class="inline-block w-full bg-yellow-400 hover:bg-yellow-500 text-amber-950 font-extrabold py-3 rounded-xl text-sm uppercase tracking-wider shadow-sm">
            Lanjut ke Halaman Login →
        </a>
    </div>
</body>
</html>