<?php
// admin/login.php
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi.";
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            // Regenerasi session ID untuk keamanan
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Kombinasi username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - HMAN Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brandYellow: '#FACC15', brandBrown: '#78350F', brandCream: '#FEF3C7' }
                }
            }
        }
    </script>
</head>
<body class="bg-brandCream min-h-screen flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white border-2 border-amber-300 rounded-3xl p-8 shadow-md space-y-6">
        <div class="text-center space-y-2">
            <span class="text-4xl">🔐</span>
            <h1 class="text-2xl font-black text-brandBrown">Admin HMAN Store</h1>
            <p class="text-xs text-slate-600">Masuk untuk mengelola produk dan pesanan toko.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded-xl text-xs">
                ⚠️ <?= sanitize_input($error) ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Username</label>
                <input type="text" name="username" required class="w-full px-4 py-2.5 text-xs sm:text-sm border border-amber-300 rounded-xl focus:ring-2 focus:ring-brandYellow focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="passwordInput" name="password" required class="w-full px-4 py-2.5 text-xs sm:text-sm border border-amber-300 rounded-xl focus:ring-2 focus:ring-brandYellow focus:outline-none pr-10">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-brandBrown text-sm focus:outline-none">
                        <span id="eyeIcon">👁️</span>
                    </button>
                </div>
            </div>
            <button type="submit" class="w-full bg-brandYellow hover:bg-yellow-400 text-brandBrown font-extrabold py-3 rounded-xl text-sm transition-all shadow-sm uppercase tracking-wider">
                Masuk Dashboard →
            </button>
        </form>

        <div class="text-center pt-2 space-y-2 border-t border-amber-100">
            <p class="text-[11px] text-slate-500">Belum bisa masuk? Klik <a href="setup_admin.php" class="text-amber-700 font-bold underline">Setup Admin</a> untuk mereset akun.</p>
            <a href="../index.php" class="block text-xs font-bold text-brandBrown hover:underline">← Kembali ke Beranda Toko</a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.textContent = '🙈';
            } else {
                passInput.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>