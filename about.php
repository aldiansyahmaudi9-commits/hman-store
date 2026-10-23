<?php
// about.php
session_start();
$cart_count = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += ($item['jumlah'] ?? 1);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - HMAN Store</title>
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
                <a href="cart.php" class="hover:text-brandYellow transition-colors">Keranjang</a>
                <a href="about.php" class="text-brandYellow hover:underline">Tentang Kami</a>
            </nav>

            <div class="flex items-center">
                <a href="cart.php" class="relative p-2 text-white hover:text-brandYellow transition-colors" title="Keranjang Belanja">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="root" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
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

    <!-- KONTEN TENTANG KAMI -->
    <main class="max-w-4xl mx-auto px-4 py-12 space-y-6">
        <div class="bg-white border-2 border-amber-200 rounded-3xl p-8 shadow-sm space-y-6">
            <div class="text-center space-y-2">
                <div class="w-16 h-16 bg-brandYellow rounded-full flex items-center justify-center text-3xl mx-auto shadow-sm">
                    🍌
                </div>
                <h1 class="text-3xl font-black text-brandBrown">Tentang HMAN Store</h1>
                <p class="text-xs sm:text-sm text-slate-600">Banchip HMAN - Keripik Pisang Olahan Pilihan Berkualitas Tinggi</p>
            </div>

            <div class="space-y-4 text-xs sm:text-sm text-slate-700 leading-relaxed border-t border-amber-100 pt-6">
                <p>
                    <strong>HMAN Store</strong> adalah usaha rintisan yang berfokus pada penyediaan camilan keripik pisang olahan berkualitas tinggi (Banchip HMAN). Kami berkomitmen untuk menyajikan cita rasa yang renyah, gurih, dan lezat dengan balutan bumbu pilihan yang melimpah di setiap gigitannya.
                </p>
                <p>
                    Produk kami dibuat dari buah pisang pilihan berkualitas segar yang diolah secara higienis untuk menjaga kelezatan serta kepuasan para pelanggan setia kami.
                </p>
            </div>
        </div>
    </main>

</body>
</html>