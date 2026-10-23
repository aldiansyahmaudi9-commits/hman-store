<?php
// checkout.php
session_start();
require_once __DIR__ . '/config/database.php';

if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_count += $item['jumlah'];
}

$error_msg = '';
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pemesan      = trim($_POST['nama_pemesan'] ?? '');
    $nomor_wa          = trim($_POST['nomor_wa'] ?? '');
    $wilayah           = trim($_POST['wilayah'] ?? '');
    $alamat_detail     = trim($_POST['alamat'] ?? '');
    $metode_pembayaran = trim($_POST['metode_pembayaran'] ?? 'QRIS');

    $alamat_lengkap = (!empty($wilayah) ? "Wilayah: " . $wilayah . " | " : "") . "Alamat: " . $alamat_detail;

    if (empty($nama_pemesan) || empty($nomor_wa) || empty($alamat_detail)) {
        $error_msg = "Semua kolom bertanda bintang (*) wajib diisi!";
    } else {
        try {
            $pdo->beginTransaction();

            $total_belanja = 0;
            $items_summary = [];
            foreach ($_SESSION['cart'] as $product_id => $item) {
                $subtotal = $item['harga'] * $item['jumlah'];
                $total_belanja += $subtotal;
                
                $items_summary[] = [
                    'produk_id'   => $product_id,
                    'nama_produk' => $item['nama_produk'],
                    'harga'       => $item['harga'],
                    'jumlah'      => $item['jumlah'],
                    'subtotal'    => $subtotal
                ];
            }
            $ongkir = 5000;
            $total_tagihan = $total_belanja + $ongkir;

            $kode_pesanan = 'HMAN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // Menggunakan kolom 'alamat_pengiriman' sesuai struktur database Anda
            $stmt = $pdo->prepare("INSERT INTO pesanan (kode_pesanan, nama_pemesan, nomor_wa, alamat_pengiriman, total_harga, metode_pembayaran, status_pembayaran, status_pesanan, created_at) VALUES (:kode, :nama, :wa, :alamat, :total, :metode, 'belum_bayar', 'menunggu_konfirmasi', NOW())");
            $stmt->execute([
                ':kode'   => $kode_pesanan,
                ':nama'   => $nama_pemesan,
                ':wa'     => $nomor_wa,
                ':alamat' => $alamat_lengkap,
                ':total'  => $total_tagihan,
                ':metode' => $metode_pembayaran
            ]);

            $order_id = $pdo->lastInsertId();

            foreach ($items_summary as $it) {
                $stmt_item = $pdo->prepare("INSERT INTO pesanan_item (pesanan_id, produk_id, nama_produk, harga, jumlah, subtotal) VALUES (:pid, :prod_id, :nama, :harga, :jumlah, :subtotal)");
                $stmt_item->execute([
                    ':pid'      => $order_id,
                    ':prod_id'  => $it['produk_id'],
                    ':nama'     => $it['nama_produk'],
                    ':harga'    => $it['harga'],
                    ':jumlah'   => $it['jumlah'],
                    ':subtotal' => $it['subtotal']
                ]);

                $stmt_stok = $pdo->prepare("UPDATE produk SET stok = stok - :jml WHERE id = :id AND stok >= :jml");
                $stmt_stok->execute([':jml' => $it['jumlah'], ':id' => $it['produk_id']]);
            }

            $pdo->commit();

            unset($_SESSION['cart']);
            $_SESSION['order_success_code'] = $kode_pesanan;

            header("Location: order_success.php?kode=" . $kode_pesanan);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Gagal memproses pesanan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Pesanan - HMAN Store</title>
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
        function toggleQris() {
            const select = document.getElementById('metode_pembayaran');
            const qrisContainer = document.getElementById('qris-container');
            if (select.value === 'QRIS') {
                qrisContainer.classList.remove('hidden');
            } else {
                qrisContainer.classList.add('hidden');
            }
        }
    </script>
</head>
<body class="bg-brandCream min-h-screen text-slate-800 font-sans" onload="toggleQris()">

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
                <a href="cart.php" class="hover:text-brandYellow transition-colors">Keranjang</a>
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

    <!-- FORMULIR CHECKOUT -->
    <main class="max-w-4xl mx-auto px-4 py-8 space-y-6">
        <div class="space-y-1">
            <h1 class="text-3xl font-black text-brandBrown">Checkout Pesanan</h1>
            <p class="text-xs sm:text-sm text-slate-600">Lengkapi data pengiriman untuk pesanan Banchip HMAN Anda.</p>
        </div>

        <?php if (!empty($error_msg)): ?>
            <div class="bg-red-100 border border-red-400 text-red-800 p-3 rounded-xl text-xs">⚠️ <?= htmlspecialchars($error_msg) ?></div>
        <?php endif; ?>

        <form action="checkout.php" method="POST" class="space-y-6">
            
            <!-- INFORMASI PEMESAN -->
            <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-black text-brandBrown text-base flex items-center gap-2 border-b border-amber-100 pb-3">👤 Informasi Pemesan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-brandBrown mb-1">Nama Lengkap *</label>
                        <input type="text" name="nama_pemesan" required class="w-full p-3 text-xs sm:text-sm border border-amber-200 rounded-xl focus:ring-2 focus:ring-brandYellow focus:outline-none" placeholder="Contoh: Siti Aminah">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brandBrown mb-1">Nomor WhatsApp *</label>
                        <input type="text" name="nomor_wa" required class="w-full p-3 text-xs sm:text-sm border border-amber-200 rounded-xl focus:ring-2 focus:ring-brandYellow focus:outline-none" placeholder="Contoh: 081234567890">
                    </div>
                </div>
            </div>

            <!-- ALAMAT & PENGIRIMAN -->
            <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-black text-brandBrown text-base flex items-center gap-2 border-b border-amber-100 pb-3">📍 Alamat & Pengiriman</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-brandBrown mb-1">Wilayah Pengiriman *</label>
                        <select name="wilayah" class="w-full p-3 text-xs sm:text-sm border border-amber-200 rounded-xl focus:ring-2 focus:ring-brandYellow focus:outline-none bg-white">
                            <option value="Kampus PNUP Tamalanrea (Kurir Kampus)">Kampus PNUP Tamalanrea (Kurir Kampus)</option>
                            <option value="Kecamatan Tamalanrea">Kecamatan Tamalanrea</option>
                            <option value="Luar Tamalanrea / Kota Makassar">Luar Tamalanrea / Kota Makassar</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-brandBrown mb-1">Alamat Lengkap *</label>
                        <textarea name="alamat" rows="3" required class="w-full p-3 text-xs sm:text-sm border border-amber-200 rounded-xl focus:ring-2 focus:ring-brandYellow focus:outline-none" placeholder="Nama jalan, nomor rumah, patokan..."></textarea>
                    </div>
                </div>
            </div>

            <!-- METODE PEMBAYARAN & QRIS -->
            <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-4">
                <h2 class="font-black text-brandBrown text-base flex items-center gap-2 border-b border-amber-100 pb-3">💳 Metode Pembayaran</h2>
                <div>
                    <label class="block text-xs font-bold text-brandBrown mb-1">Pilih Metode Pembayaran *</label>
                    <select name="metode_pembayaran" id="metode_pembayaran" onchange="toggleQris()" class="w-full p-3 text-xs sm:text-sm border border-amber-200 rounded-xl focus:ring-2 focus:ring-brandYellow focus:outline-none bg-white">
                        <option value="QRIS">QRIS ALL PAYMENT (Scan GoPay/OVO/Dana/ShopeePay/BCA)</option>
                        <option value="Transfer Bank">Transfer Bank (BCA / Mandiri)</option>
                        <option value="COD">Cash on Delivery (Bayar di Tempat)</option>
                    </select>
                </div>

                <!-- KOTAK GAMBAR QRIS -->
                <div id="qris-container" class="hidden pt-4 border-t border-amber-100 text-center space-y-3">
                    <p class="text-xs font-bold text-brandBrown">Scan QRIS di bawah ini menggunakan aplikasi e-Wallet / m-Banking Anda:</p>
                    <div class="inline-block p-4 bg-brandCream rounded-2xl border-2 border-amber-200 shadow-inner">
                        <img src="assets/img/qris.png" alt="QRIS HMAN Store" class="w-48 h-48 sm:w-56 sm:h-56 mx-auto object-contain rounded-xl" onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=HMANSTORE-QRIS-DUMMY';">
                    </div>
                    <p class="text-[11px] text-slate-500">Simpan bukti transfer/screenshot pembayaran Anda untuk dikirimkan melalui WhatsApp setelah pesanan dibuat.</p>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="w-full py-4 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-2xl text-sm uppercase tracking-wider shadow-sm transition-all">
                    Buat Pesanan & Lanjutkan ke WhatsApp &rarr;
                </button>
            </div>

        </form>
    </main>

</body>
</html>