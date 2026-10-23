<?php
// includes/header.php
require_once __DIR__ . '/../config/database.php';

$cart_count = get_cart_count();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HMAN Store – Banchip HMAN | Keripik Pisang Premium</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandYellow: '#FACC15',
                        brandYellowDark: '#EAB308',
                        brandBrown: '#2D1810',
                        brandBrownLight: '#4A2E19',
                        brandCream: '#FFFBEB',
                        brandCreamDark: '#FEF3C7',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-brandCream text-slate-800 flex flex-col min-h-screen">

    <!-- Header / Navbar -->
    <header class="bg-brandBrown text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- Logo & Brand Identity -->
                <a href="index.php" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 bg-brandYellow rounded-full flex items-center justify-center text-brandBrown font-bold text-xl shadow-inner group-hover:scale-105 transition-transform">
                        🍌
                    </div>
                    <div class="flex flex-col">
                        <span class="font-extrabold text-xl tracking-wide text-brandYellow group-hover:text-amber-300 transition-colors">
                            HMAN STORE
                        </span>
                        <span class="text-xs text-amber-200 tracking-wider font-medium">
                            Banchip HMAN
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8 font-medium text-sm">
                    <a href="index.php" class="text-brandYellow font-semibold transition-colors">Beranda</a>
                    <a href="index.php#katalog" class="text-amber-100 hover:text-brandYellow transition-colors">Katalog Produk</a>
                    <a href="cart.php" class="text-amber-100 hover:text-brandYellow transition-colors">Keranjang</a>
                    <a href="index.php#tentang" class="text-amber-100 hover:text-brandYellow transition-colors">Tentang Kami</a>
                </nav>

                <!-- Action Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Cart Button Dinamis -->
                    <a href="cart.php" 
                       class="relative p-2.5 text-amber-100 hover:text-brandYellow hover:bg-brandBrownLight rounded-full transition-all focus:outline-none" 
                       title="Keranjang Belanja">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                        </svg>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-brandYellow text-brandBrown text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-brandBrown">
                            <?= $cart_count ?>
                        </span>
                    </a>

                    <button onclick="showNoticeModal('Login Admin', 'Panel Admin HMAN Store akan diintegrasikan pada tahap selanjutnya.')" 
                            class="bg-brandYellow hover:bg-yellow-400 text-brandBrown font-semibold text-xs px-4 py-2.5 rounded-lg shadow transition-all hover:scale-105">
                        Area Admin
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <a href="cart.php" class="relative p-2 text-amber-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"></path>
                        </svg>
                        <span class="absolute -top-1 -right-1 bg-brandYellow text-brandBrown text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                            <?= $cart_count ?>
                        </span>
                    </a>

                    <button id="mobile-menu-btn" class="text-amber-100 hover:text-white focus:outline-none p-2">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

            </div>

            <!-- Mobile Navigation Drawer -->
            <div id="mobile-menu" class="hidden md:hidden pb-5 pt-2 border-t border-amber-900/50">
                <nav class="flex flex-col space-y-3 font-medium text-sm">
                    <a href="index.php" class="text-brandYellow font-semibold px-2 py-1">Beranda</a>
                    <a href="index.php#katalog" class="text-amber-100 hover:text-brandYellow px-2 py-1">Katalog Produk</a>
                    <a href="cart.php" class="text-amber-100 hover:text-brandYellow px-2 py-1">Keranjang Belanja (<?= $cart_count ?>)</a>
                    <a href="index.php#tentang" class="text-amber-100 hover:text-brandYellow px-2 py-1">Tentang Kami</a>
                    <button onclick="showNoticeModal('Login Admin', 'Panel Admin HMAN Store akan diintegrasikan pada tahap selanjutnya.')" 
                            class="bg-brandYellow text-brandBrown font-semibold text-xs px-4 py-2.5 rounded-lg text-center mt-2">
                        Area Admin
                    </button>
                </nav>
            </div>
        </div>
    </header>