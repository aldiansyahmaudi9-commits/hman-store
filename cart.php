<?php
// cart.php
session_start();
require_once __DIR__ . '/config/database.php';

$pdo = getDBConnection();

// Tangkap aksi dari URL (tambah, kurang, hapus)
$action = $_GET['action'] ?? '';
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);

if ($action === 'add' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $product = $stmt->fetch();

    if ($product) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['jumlah'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                'id'          => $product['id'],
                'nama_produk' => $product['nama_produk'],
                'harga'       => $product['harga'],
                'jumlah'      => 1
            ];
        }
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'update' && $id) {
    $qty = filter_var($_POST['jumlah'] ?? 1, FILTER_VALIDATE_INT);
    if ($qty > 0 && isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['jumlah'] = $qty;
    } elseif ($qty <= 0 && isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;
}

if ($action === 'remove' && $id) {
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;
}

$cart_items = $_SESSION['cart'] ?? [];
$total_belanja = 0;
$cart_count = 0;

foreach ($cart_items as $item) {
    $total_belanja += ($item['harga'] * $item['jumlah']);
    $cart_count += $item['jumlah'];
}
$ongkir = !empty($cart_items) ? 5000 : 0;
$total_tagihan = $total_belanja + ($cart_count > 0 ? $ongkir : 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - HMAN Store</title>
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
        function ubahJumlah(id, nilai) {
            const input = document.getElementById('input-jumlah-' + id);
            let val = parseInt(input.value) + nilai;
            if (val < 1) val = 1;
            input.value = val;
            input.form.submit();
        }
    </script>
</head>
<body class="bg-brandCream min-h-screen text-slate-800 font-sans">

    <!-- HEADER -->
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
                <a href="catalog.php" class="hover:text-brandYellow transition-colors">Katalog Produk</a>
                <a href="cart.php" class="text-brandYellow underline">Keranjang</a>
                <a href="about.php" class="hover:text-brandYellow transition-colors">Tentang Kami</a>
            </nav>
            <div class="flex items-center">
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

    <!-- KONTEN KERANJANG -->
    <main class="max-w-5xl mx-auto px-4 py-8 space-y-6">
        <h1 class="text-3xl font-black text-brandBrown">Keranjang Belanja Anda</h1>

        <?php if (!empty($cart_items)): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- DAFTAR ITEM -->
                <div class="lg:col-span-2 space-y-4">
                    <?php foreach ($cart_items as $id => $item): ?>
                        <div class="bg-white border-2 border-amber-200 rounded-3xl p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="flex items-center gap-4 w-full sm:w-auto">
                                <div class="w-16 h-16 bg-brandCream rounded-2xl flex items-center justify-center text-3xl border border-amber-100 flex-shrink-0">
                                    🍌
                                </div>
                                <div>
                                    <h3 class="font-black text-brandBrown text-base"><?= htmlspecialchars($item['nama_produk']) ?></h3>
                                    <p class="text-xs text-slate-500">Harga: Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                                </div>
                            </div>

                            <div class="flex items-center justify-between sm:justify-end gap-3 w-full sm:w-auto">
                                <!-- FORM DENGAN TOMBOL MINUS (-) DAN PLUS (+) -->
                                <form action="cart.php?action=update&id=<?= $id ?>" method="POST" class="flex items-center gap-1 bg-amber-50 p-1.5 rounded-2xl border border-amber-200">
                                    <button type="button" onclick="ubahJumlah(<?= $id ?>, -1)" class="w-8 h-8 bg-white hover:bg-amber-100 text-brandBrown font-black rounded-xl text-sm flex items-center justify-center shadow-xs transition-all">-</button>
                                    
                                    <input type="number" id="input-jumlah-<?= $id ?>" name="jumlah" value="<?= $item['jumlah'] ?>" min="1" class="w-12 p-1 text-xs border-0 bg-transparent text-center font-black text-brandBrown focus:outline-none">
                                    
                                    <button type="button" onclick="ubahJumlah(<?= $id ?>, 1)" class="w-8 h-8 bg-white hover:bg-amber-100 text-brandBrown font-black rounded-xl text-sm flex items-center justify-center shadow-xs transition-all">+</button>
                                </form>

                                <a href="cart.php?action=remove&id=<?= $id ?>" class="text-red-600 hover:text-red-800 text-xs font-bold px-2 py-1">Hapus</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- RINGKASAN BELANJA -->
                <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-4 h-fit">
                    <h2 class="font-black text-brandBrown text-base border-b border-amber-100 pb-3">Ringkasan Belanja</h2>
                    <div class="space-y-2 text-xs text-slate-600">
                        <div class="flex justify-between">
                            <span>Total Harga Produk:</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($total_belanja, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estimasi Ongkir:</span>
                            <span class="font-bold text-slate-800">Rp <?= number_format($ongkir, 0, ',', '.') ?></span>
                        </div>
                        <div class="flex justify-between text-sm font-black text-brandBrown pt-3 border-t border-amber-200">
                            <span>Total Tagihan:</span>
                            <span>Rp <?= number_format($total_tagihan, 0, ',', '.') ?></span>
                        </div>
                    </div>
                    <div class="pt-4">
                        <a href="checkout.php" class="block w-full py-3.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-2xl text-xs uppercase tracking-wider text-center shadow-md transition-all">
                            Lanjut ke Checkout &rarr;
                        </a>
                    </div>
                </div>

            </div>
        <?php else: ?>
            <div class="bg-white border-2 border-amber-200 rounded-3xl p-12 text-center space-y-4 shadow-sm">
                <div class="text-5xl">🛒</div>
                <h2 class="text-xl font-black text-brandBrown">Keranjang Anda Masih Kosong</h2>
                <p class="text-xs text-slate-500">Yuk, pilih keripik pisang favorit Anda di katalog produk!</p>
                <div class="pt-2">
                    <a href="catalog.php" class="inline-block px-6 py-3 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-2xl text-xs uppercase tracking-wider shadow-sm">
                        Lihat Katalog Produk &rarr;
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>