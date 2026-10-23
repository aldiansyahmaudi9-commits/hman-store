<?php
// admin/order_detail.php
session_start();
require_once __DIR__ . '/../config/database.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pdo = getDBConnection();

// Ambil ID pesanan dari URL
$order_id = intval($_GET['id'] ?? 0);

if ($order_id <= 0) {
    header("Location: orders.php");
    exit;
}

// Proses update status pesanan atau pembayaran jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status_pesanan = trim($_POST['status_pesanan'] ?? '');
    $status_pembayaran = trim($_POST['status_pembayaran'] ?? '');

    $updateStmt = $pdo->prepare("UPDATE pesanan SET status_pesanan = :status_pesanan, status_pembayaran = :status_pembayaran WHERE id = :id");
    $updateStmt->execute([
        ':status_pesanan' => $status_pesanan,
        ':status_pembayaran' => $status_pembayaran,
        ':id' => $order_id
    ]);

    header("Location: order_detail.php?id=" . $order_id . "&success=1");
    exit;
}

// Ambil data detail pesanan
$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE id = :id");
$stmt->execute([':id' => $order_id]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: orders.php");
    exit;
}

// Ambil item produk dalam pesanan ini
$itemStmt = $pdo->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = :pesanan_id");
$itemStmt->execute([':pesanan_id' => $order_id]);
$items = $itemStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= $order['id'] ?> - Admin HMAN Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBrown: '#4A2E18',
                        brandYellow: '#FFDE59',
                        brandCream: '#FFF8E8'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-brandCream min-h-screen text-slate-800 font-sans">

    <!-- HEADER ADMIN -->
    <header class="bg-[#3D2514] text-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brandYellow rounded-full flex items-center justify-center text-xl shadow-sm">🍌</div>
                <h1 class="font-black text-lg sm:text-xl tracking-wide text-brandYellow">ADMIN HMAN STORE</h1>
            </div>
            <nav class="flex items-center gap-4 font-bold text-xs sm:text-sm">
                <a href="dashboard.php" class="hover:text-brandYellow transition-colors">Dashboard</a>
                <a href="orders.php" class="text-brandYellow underline">Kelola Pesanan</a>
                <a href="products.php" class="hover:text-brandYellow transition-colors">Produk</a>
                <a href="logout.php" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-all">Logout</a>
            </nav>
        </div>
    </header>

    <!-- KONTEN UTAMA -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        
        <div class="flex items-center justify-between">
            <div>
                <a href="orders.php" class="text-xs font-bold text-amber-800 hover:underline">&larr; Kembali ke Daftar Pesanan</a>
                <h1 class="text-2xl sm:text-3xl font-black text-brandBrown mt-1">Detail Pesanan #<?= $order['id'] ?></h1>
            </div>
            <?php if (isset($_GET['success'])): ?>
                <span class="bg-green-100 text-green-700 font-bold text-xs px-4 py-2 rounded-xl border border-green-200">
                    Status pesanan berhasil diperbarui!
                </span>
            <?php endif; ?>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- INFORMASI PELANGGAN & UBAH STATUS -->
            <div class="space-y-6">
                <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-4">
                    <h2 class="font-black text-brandBrown text-base border-b border-amber-100 pb-2 flex items-center gap-2">
                        <span>👤</span> Info Pelanggan
                    </h2>
                    
                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Nama:</span>
                        <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($order['nama'] ?? '-') ?></p>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">WhatsApp:</span>
                        <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($order['telepon'] ?? '-') ?></p>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Alamat Lengkap:</span>
                        <p class="text-sm font-semibold text-slate-800">
                            <?= htmlspecialchars($order['alamat_pengiriman'] ?? $order['alamat'] ?? 'Alamat tidak tersedia') ?>
                        </p>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Metode Pembayaran:</span>
                        <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($order['metode_pembayaran'] ?? '-') ?></p>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-slate-400 uppercase">Tanggal Pesan:</span>
                        <p class="text-sm font-semibold text-slate-800"><?= htmlspecialchars($order['created_at'] ?? '-') ?></p>
                    </div>
                </div>

                <!-- FORM UPDATE STATUS -->
                <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm">
                    <h2 class="font-black text-brandBrown text-base border-b border-amber-100 pb-2 mb-4">Ubah Status</h2>
                    
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Pesanan</label>
                            <select name="status_pesanan" class="w-full p-2.5 text-xs rounded-xl border border-amber-200 bg-brandCream/30 font-semibold focus:outline-none">
                                <option value="Menunggu Konfirmasi" <?= ($order['status_pesanan'] == 'Menunggu Konfirmasi') ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                                <option value="Diproses" <?= ($order['status_pesanan'] == 'Diproses') ? 'selected' : '' ?>>Diproses</option>
                                <option value="Dikirim" <?= ($order['status_pesanan'] == 'Dikirim') ? 'selected' : '' ?>>Dikirim</option>
                                <option value="Selesai" <?= ($order['status_pesanan'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                <option value="Dibatalkan" <?= ($order['status_pesanan'] == 'Dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status Pembayaran</label>
                            <select name="status_pembayaran" class="w-full p-2.5 text-xs rounded-xl border border-amber-200 bg-brandCream/30 font-semibold focus:outline-none">
                                <option value="Belum Bayar" <?= ($order['status_pembayaran'] == 'Belum Bayar') ? 'selected' : '' ?>>Belum Bayar</option>
                                <option value="Sudah Bayar" <?= ($order['status_pembayaran'] == 'Sudah Bayar') ? 'selected' : '' ?>>Sudah Bayar</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full py-3 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-xl text-xs uppercase tracking-wider shadow-sm transition-all">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <!-- DAFTAR ITEM PRODUK -->
            <div class="lg:col-span-2 bg-white border-2 border-amber-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
                <h2 class="font-black text-brandBrown text-lg border-b border-amber-100 pb-2">Daftar Produk yang Dipesan</h2>

                <div class="space-y-4">
                    <?php 
                    $grand_total = 0;
                    if (!empty($items)): 
                        foreach ($items as $item):
                            $subtotal = $item['harga'] * $item['jumlah'];
                            $grand_total += $subtotal;
                    ?>
                        <div class="flex items-center justify-between p-4 bg-brandCream/30 border border-amber-100 rounded-2xl">
                            <div>
                                <h3 class="font-black text-brandBrown text-sm"><?= htmlspecialchars($item['nama_produk']) ?></h3>
                                <p class="text-xs text-slate-500">Jumlah: <?= $item['jumlah'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                            </div>
                            <span class="font-black text-brandBrown text-sm">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                        <p class="text-sm text-slate-500 text-center py-6">Tidak ada item produk dalam pesanan ini.</p>
                    <?php endif; ?>
                </div>

                <div class="pt-4 border-t border-amber-100 flex items-center justify-between">
                    <span class="font-black text-slate-700 text-base">Total Belanja</span>
                    <span class="font-black text-brandBrown text-xl">Rp <?= number_format($grand_total, 0, ',', '.') ?></span>
                </div>
            </div>

        </div>

    </main>

    <!-- FOOTER -->
    <footer class="bg-[#3D2514] text-amber-200 text-center py-8 text-xs border-t border-amber-900 mt-16 space-y-2">
        <p class="font-bold text-brandYellow text-sm">HMAN STORE - BANCHIP HMAN</p>
        <p>&copy; <?= date('Y') ?> Hak Cipta Dilindungi.</p>
    </footer>

</body>
</html>