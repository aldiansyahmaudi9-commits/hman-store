<?php
// catalog.php
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

// Tangkap pencarian varian rasa
$search = trim($_GET['search'] ?? '');
$query = "SELECT * FROM produk WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND nama_produk LIKE :search";
    $params[':search'] = "%$search%";
}

// Menggunakan GROUP BY berdasarkan nama produk agar tidak tampil double
$query .= " GROUP BY nama_produk ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - HMAN Store</title>
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
                <a href="index.php" class="hover:text-brandYellow transition-colors">Beranda</a>
                <a href="catalog.php" class="text-brandYellow underline">Katalog Produk</a>
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

    <!-- KONTEN KATALOG UTAMA -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- BANNER ATAS KATALOG -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-brandBrown p-8 rounded-3xl text-white shadow-lg border-2 border-amber-900">
            <div>
                <span class="bg-brandYellow text-brandBrown font-black text-xs px-3 py-1 rounded-full uppercase tracking-wider">Katalog Resmi</span>
                <h1 class="text-3xl font-black text-brandYellow mt-2">Semua Varian Banchip HMAN</h1>
                <p class="text-xs sm:text-sm text-amber-100/90 mt-1">Pilih varian rasa keripik pisang favoritmu dan nikmati kerenyahannya!</p>
            </div>
            
            <!-- FORM PENCARIAN -->
            <form method="GET" class="w-full md:w-auto flex gap-2">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari varian rasa..." class="p-3 text-xs sm:text-sm rounded-xl text-slate-800 bg-white border border-amber-200 focus:outline-none w-full md:w-64">
                <button type="submit" class="px-5 py-3 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-xl text-xs uppercase shadow-sm transition-all">Cari</button>
            </form>
        </div>

        <!-- GRID DAFTAR PRODUK -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                    <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm flex flex-col justify-between hover:shadow-md transition-all">
                        <div>
                            <!-- AREA FOTO PRODUK (TERHUBUNG KE FOLDER assets/img/) -->
                            <div class="w-full h-56 bg-brandCream rounded-2xl flex items-center justify-center border border-amber-100 overflow-hidden mb-4 relative">
                                <?php 
                                    $gambar_nama = trim($p['gambar'] ?? '');
                                    $foto_path = __DIR__ . '/assets/img/' . $gambar_nama;
                                    
                                    if (!empty($gambar_nama) && file_exists($foto_path)): 
                                ?>
                                    <img src="assets/img/<?= htmlspecialchars($gambar_nama) ?>" alt="<?= htmlspecialchars($p['nama_produk']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <img src="assets/img/banner.jpg" alt="Banchip HMAN" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                            
                            <h3 class="font-black text-brandBrown text-lg mb-1"><?= htmlspecialchars($p['nama_produk']) ?></h3>
                            <p class="text-xs text-slate-600 line-clamp-2 mb-4"><?= htmlspecialchars($p['deskripsi'] ?? 'Keripik pisang renyah berkualitas tinggi dengan bumbu melimpah.') ?></p>
                        </div>
                        
                        <!-- TOMBOL DAN HARGA -->
                        <div class="pt-4 border-t border-amber-100 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] text-slate-400 block uppercase font-bold">Harga</span>
                                <span class="font-black text-brandBrown text-base">Rp <?= number_format($p['harga'], 0, ',', '.') ?></span>
                            </div>
                            <a href="cart.php?action=add&id=<?= $p['id'] ?>" class="px-4 py-3 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-xl text-xs uppercase tracking-wider shadow-sm transition-all">
                                + Keranjang
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-3 bg-white border-2 border-amber-200 rounded-3xl p-12 text-center text-slate-500">
                    Produk yang Anda cari tidak ditemukan.
                </div>
            <?php endif; ?>
        </div>

    </main>

    <!-- FOOTER -->
    <footer class="bg-[#3D2514] text-amber-200 text-center py-8 text-xs border-t border-amber-900 mt-16 space-y-2">
        <p class="font-bold text-brandYellow text-sm">HMAN STORE - BANCHIP HMAN</p>
        <p>&copy; <?= date('Y') ?> Hak Cipta Dilindungi. Dikembangkan dengan PHP Native & Tailwind CSS.</p>
    </footer>

</body>
</html>