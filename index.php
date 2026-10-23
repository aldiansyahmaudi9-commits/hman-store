<?php
// index.php
session_start();
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();

// Hitung jumlah item di keranjang
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['jumlah'];
    }
}

// Ambil produk keripik pisang (Banchip) - misalnya yang mengandung kata 'Banchip' atau 'Balado' / 'Tiramisu' / 'Coklat' / 'Jagung'
$stmtBanchip = $pdo->query("SELECT * FROM produk WHERE nama_produk LIKE '%Banchip%' OR nama_produk LIKE '%Balado%' OR nama_produk LIKE '%Tiramisu%' LIMIT 4");
$banchip_products = $stmtBanchip->fetchAll();

// Ambil khusus produk pakaian / Workshirt
$stmtWorkshirt = $pdo->query("SELECT * FROM produk WHERE nama_produk LIKE '%Workshirt%' OR nama_produk LIKE '%Baju%' OR nama_produk LIKE '%Kemeja%' LIMIT 4");
$workshirt_products = $stmtWorkshirt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - HMAN Store</title>
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

    <!-- HEADER / NAVBAR -->
    <header class="bg-[#3D2514] text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3">
                <div class="w-12 h-12 bg-brandYellow rounded-full flex items-center justify-center text-2xl shadow-sm">🍌</div>
                <div>
                    <h1 class="font-black text-xl sm:text-2xl tracking-wide text-brandYellow leading-none">HMAN STORE</h1>
                    <span class="text-[10px] sm:text-xs text-amber-200 font-medium tracking-widest uppercase block mt-1">BANCHIP HMAN</span>
                </div>
            </a>
            <nav class="hidden md:flex items-center gap-6 font-bold text-sm">
                <a href="index.php" class="text-brandYellow underline">Beranda</a>
                <a href="catalog.php" class="hover:text-brandYellow transition-colors">Katalog Produk</a>
                <a href="cart.php" class="hover:text-brandYellow transition-colors">Keranjang</a>
                <a href="about.php" class="hover:text-brandYellow transition-colors">Tentang Kami</a>
            </nav>
            <div class="flex items-center gap-3">
                <a href="cart.php" class="relative p-2 text-white hover:text-brandYellow transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <?php if ($cart_count > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-brandYellow text-brandBrown font-black text-xs w-5 h-5 rounded-full flex items-center justify-center border-2 border-[#3D2514]">
                            <?= $cart_count ?>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-16">
        <div class="bg-brandBrown rounded-3xl p-8 sm:p-12 text-white flex flex-col md:flex-row items-center justify-between gap-8 shadow-xl border-2 border-amber-900">
            <div class="space-y-4 max-w-xl text-center md:text-left">
                <span class="bg-brandYellow text-brandBrown font-black text-xs px-4 py-1.5 rounded-full uppercase tracking-wider">Pusat Belanja Hman Store</span>
                <h1 class="text-3xl sm:text-5xl font-black text-brandYellow leading-tight">Keripik Pisang Renyah & Pakaian Kekinian</h1>
                <p class="text-xs sm:text-sm text-amber-100/90 leading-relaxed">Nikmati kelezatan Banchip berbagai rasa dan tampil keren dengan koleksi Baju Workshirt eksklusif kami.</p>
                <div class="pt-2 flex flex-wrap justify-center md:justify-start gap-4">
                    <a href="catalog.php" class="px-6 py-3.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-2xl text-xs uppercase tracking-wider shadow-md transition-all">
                        Lihat Semua Katalog &rarr;
                    </a>
                </div>
            </div>
            <div class="w-full md:w-80 h-72 bg-brandCream/10 rounded-2xl border border-amber-800 overflow-hidden flex items-center justify-center relative shadow-inner">
                <img src="assets/img/banner.jpg" alt="Banner HMAN Store" class="w-full h-full object-cover">
            </div>
        </div>

        <!-- SECTION 1: PRODUK BANCHIP (KERIPIK PISANG) -->
        <div class="space-y-6">
            <div class="flex items-center justify-between border-b border-amber-200 pb-3">
                <div>
                    <span class="text-xs font-black text-amber-800 uppercase tracking-widest block">Cemilan Favorit</span>
                    <h2 class="text-2xl font-black text-brandBrown">Produk Banchip (Keripik Pisang)</h2>
                </div>
                <a href="catalog.php" class="text-xs font-bold text-amber-800 hover:underline">Lihat Semua &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if (!empty($banchip_products)): ?>
                    <?php foreach ($banchip_products as $p): ?>
                        <div class="bg-white border-2 border-amber-200 rounded-3xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                            <div>
                                <div class="w-full h-48 bg-brandCream rounded-2xl flex items-center justify-center border border-amber-100 overflow-hidden mb-4 relative">
                                    <?php 
                                        $gambar_nama = trim($p['gambar'] ?? '');
                                        $foto_path = __DIR__ . '/assets/img/' . $gambar_nama;
                                        if (!empty($gambar_nama) && file_exists($foto_path)): 
                                    ?>
                                        <img src="assets/img/<?= htmlspecialchars($gambar_nama) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <img src="assets/img/banner.jpg" alt="HMAN Store" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <h3 class="font-black text-brandBrown text-base mb-1"><?= htmlspecialchars($p['nama_produk']) ?></h3>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4"><?= htmlspecialchars($p['deskripsi'] ?? '') ?></p>
                            </div>
                            <div class="pt-3 border-t border-amber-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Harga</span>
                                    <span class="font-black text-brandBrown text-sm">Rp <?= number_format($p['harga'], 0, ',', '.') ?></span>
                                </div>
                                <a href="cart.php?action=add&id=<?= $p['id'] ?>" class="px-3 py-2.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-xl text-[11px] uppercase tracking-wider shadow-sm transition-all">
                                    + Keranjang
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-4 bg-white border-2 border-amber-200 rounded-3xl p-8 text-center text-slate-500 text-sm">
                        Belum ada produk Banchip yang ditampilkan.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SECTION 2: KOLEKSI PAKAIAN / BAJU WORKSHIRT (DI BAWAH BANCHIP) -->
        <div class="space-y-6 pt-6">
            <div class="flex items-center justify-between border-b border-amber-200 pb-3">
                <div>
                    <span class="text-xs font-black text-amber-800 uppercase tracking-widest block">Fashion & Apparel</span>
                    <h2 class="text-2xl font-black text-brandBrown">Koleksi Baju Workshirt</h2>
                </div>
                <a href="catalog.php" class="text-xs font-bold text-amber-800 hover:underline">Lihat Semua &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if (!empty($workshirt_products)): ?>
                    <?php foreach ($workshirt_products as $w): ?>
                        <div class="bg-white border-2 border-amber-200 rounded-3xl p-5 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                            <div>
                                <div class="w-full h-48 bg-brandCream rounded-2xl flex items-center justify-center border border-amber-100 overflow-hidden mb-4 relative">
                                    <?php 
                                        $gambar_workshirt = trim($w['gambar'] ?? '');
                                        $path_workshirt = __DIR__ . '/assets/img/' . $gambar_workshirt;
                                        if (!empty($gambar_workshirt) && file_exists($path_workshirt)): 
                                    ?>
                                        <img src="assets/img/<?= htmlspecialchars($gambar_workshirt) ?>" alt="<?= htmlspecialchars($w['nama_produk']) ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <img src="assets/img/banner.jpg" alt="Workshirt HMAN" class="w-full h-full object-cover">
                                    <?php endif; ?>
                                </div>
                                <h3 class="font-black text-brandBrown text-base mb-1"><?= htmlspecialchars($w['nama_produk']) ?></h3>
                                <p class="text-xs text-slate-600 line-clamp-2 mb-4"><?= htmlspecialchars($w['deskripsi'] ?? 'Kemeja workshirt eksklusif dari HMAN Store.') ?></p>
                            </div>
                            <div class="pt-3 border-t border-amber-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] text-slate-400 block uppercase font-bold">Harga</span>
                                    <span class="font-black text-brandBrown text-sm">Rp <?= number_format($w['harga'], 0, ',', '.') ?></span>
                                </div>
                                <a href="cart.php?action=add&id=<?= $w['id'] ?>" class="px-3 py-2.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-xl text-[11px] uppercase tracking-wider shadow-sm transition-all">
                                    + Keranjang
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- TAMPILAN JIKA PRODUK WORKSHIRT BELUM DIMASUKKAN KE DATABASE -->
                    <div class="col-span-4 bg-white border-2 border-amber-200 rounded-3xl p-8 text-center space-y-3">
                        <p class="text-sm font-bold text-brandBrown">Belum ada produk Workshirt di database.</p>
                        <p class="text-xs text-slate-500">Silakan masukkan data produk Workshirt melalui phpMyAdmin Anda.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="bg-[#3D2514] text-amber-200 text-center py-8 text-xs border-t border-amber-900 mt-16 space-y-2">
        <p class="font-bold text-brandYellow text-sm">HMAN STORE - BANCHIP HMAN</p>
        <p>&copy; <?= date('Y') ?> Hak Cipta Dilindungi. Dikembangkan dengan PHP Native & Tailwind CSS.</p>
    </footer>

</body>
</html>