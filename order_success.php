<?php
// order_success.php
session_start();
require_once __DIR__ . '/config/database.php';

// Sembunyikan notice/warning agar tidak tampil di layar pengguna
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Ambil kode pesanan dari URL atau Session
$kode_pesanan = $_GET['kode'] ?? $_SESSION['order_success_code'] ?? '';

if (empty($kode_pesanan)) {
    header("Location: index.php");
    exit;
}

$pdo = getDBConnection();

// Ambil data pesanan utama dari database
$stmt = $pdo->prepare("SELECT * FROM pesanan WHERE kode_pesanan = :kode");
$stmt->execute([':kode' => $kode_pesanan]);
$order = $stmt->fetch();

if (!$order) {
    header("Location: index.php");
    exit;
}

// Ambil rincian item produk yang dipesan
$stmt_detail = $pdo->prepare("SELECT * FROM pesanan_item WHERE pesanan_id = :id");
$stmt_detail->execute([':id' => $order['id']]);
$items = $stmt_detail->fetchAll();

// Ambil alamat dengan aman (mendukung kolom 'alamat_pengiriman' maupun 'alamat')
$alamat_tampil = $order['alamat_pengiriman'] ?? $order['alamat'] ?? '-';

// Konfigurasi Nomor WhatsApp Admin
$admin_wa = "6287812957112"; 
$pesan_wa = "Halo Admin HMAN Store, saya ingin mengonfirmasi pesanan baru dengan Kode: " . ($order['kode_pesanan'] ?? '') . " atas nama " . ($order['nama_pemesan'] ?? '') . ". Total tagihan: Rp " . number_format($order['total_harga'] ?? 0, 0, ',', '.') . ". Berikut saya lampirkan bukti pembayarannya.";
$url_wa = "https://wa.me/" . $admin_wa . "?text=" . urlencode($pesan_wa);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - HMAN Store</title>
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

    <!-- HEADER -->
    <header class="bg-[#3D2514] text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3">
                <div class="w-12 h-12 bg-brandYellow rounded-full flex items-center justify-center text-2xl shadow-sm">
                    🍌
                </div>
                <div>
                    <h1 class="font-black text-xl sm:text-2xl tracking-wide text-brandYellow leading-none">HMAN STORE</h1>
                    <span class="text-[10px] sm:text-xs text-amber-200 font-medium tracking-widest uppercase block mt-1">BANCHIP HMAN</span>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-6 font-bold text-sm">
                <a href="index.php" class="hover:text-brandYellow transition-colors">Beranda</a>
                <a href="catalog.php" class="hover:text-brandYellow transition-colors">Katalog Produk</a>
                <a href="about.php" class="hover:text-brandYellow transition-colors">Tentang Kami</a>
            </nav>
        </div>
    </header>

    <!-- KONTEN SUKSES -->
    <main class="max-w-3xl mx-auto px-4 py-12 space-y-6">
        <div class="bg-white border-2 border-amber-200 rounded-3xl p-8 shadow-sm space-y-6 text-center">
            
            <div class="w-16 h-16 bg-brandYellow rounded-full flex items-center justify-center text-3xl mx-auto shadow-sm">
                ✅
            </div>

            <div class="space-y-2">
                <h1 class="text-3xl font-black text-brandBrown">Pesanan Berhasil Dibuat!</h1>
                <p class="text-xs sm:text-sm text-slate-600">Terima kasih telah berbelanja di HMAN Store. Pesanan Anda telah tercatat di sistem kami.</p>
            </div>

            <div class="bg-brandCream p-4 rounded-2xl border border-amber-200 inline-block">
                <span class="text-xs text-slate-500 block">Kode Pesanan Anda:</span>
                <span class="text-base sm:text-lg font-black font-mono text-brandBrown"><?= htmlspecialchars($order['kode_pesanan'] ?? '') ?></span>
            </div>

            <!-- RINCIAN PESANAN -->
            <div class="text-left border-t border-amber-100 pt-6 space-y-4">
                <h2 class="font-black text-brandBrown text-sm">Rincian Pengiriman & Pembayaran</h2>
                <div class="text-xs space-y-1.5 text-slate-700 bg-brandCream/50 p-4 rounded-xl border border-amber-100">
                    <p><strong>Nama Pemesan:</strong> <?= htmlspecialchars($order['nama_pemesan'] ?? '') ?></p>
                    <p><strong>No. WhatsApp:</strong> <?= htmlspecialchars($order['nomor_wa'] ?? '') ?></p>
                    <p><strong>Alamat:</strong> <?= htmlspecialchars($alamat_tampil) ?></p>
                    <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($order['metode_pembayaran'] ?? '') ?></p>
                    <p><strong>Total Tagihan:</strong> <span class="font-black text-brandBrown">Rp <?= number_format($order['total_harga'] ?? 0, 0, ',', '.') ?></span></p>
                </div>
            </div>

            <!-- DAFTAR ITEM PRODUK -->
            <div class="text-left border-t border-amber-100 pt-6 space-y-3">
                <h2 class="font-black text-brandBrown text-sm">Produk yang Dipesan</h2>
                <div class="space-y-2">
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="flex justify-between items-center text-xs bg-slate-50 p-3 rounded-xl border border-amber-100">
                                <div>
                                    <span class="font-bold text-brandBrown"><?= htmlspecialchars($item['nama_produk'] ?? '') ?></span>
                                    <span class="text-slate-500 block"><?= (int)($item['jumlah'] ?? 0) ?>x @ Rp <?= number_format($item['harga'] ?? 0, 0, ',', '.') ?></span>
                                </div>
                                <span class="font-bold text-brandBrown">Rp <?= number_format($item['subtotal'] ?? 0, 0, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TOMBOL AKSI (WHATSAPP & KEMBALI) -->
            <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?= $url_wa ?>" target="_blank" class="px-6 py-3.5 bg-green-600 hover:bg-green-700 text-white font-black rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm flex items-center justify-center gap-2">
                    💬 Konfirmasi via WhatsApp
                </a>
                <a href="index.php" class="px-6 py-3.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-xl text-xs uppercase tracking-wider transition-all shadow-sm text-center flex items-center justify-center">
                    Kembali ke Beranda &rarr;
                </a>
            </div>

        </div>
    </main>

</body>
</html>