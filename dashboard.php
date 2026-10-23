<?php
// admin/dashboard.php
session_start();
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();

// PROSES HAPUS PESANAN LANGSUNG DI SINI AGAR TIDAK ERROR 404
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    try {
        $stmtItem = $pdo->prepare("DELETE FROM pesanan_item WHERE pesanan_id = :id");
        $stmtItem->execute([':id' => $id]);

        $stmtOrder = $pdo->prepare("DELETE FROM pesanan WHERE id = :id");
        $stmtOrder->execute([':id' => $id]);
    } catch (PDOException $e) {
        // Abaikan galat
    }
    header("Location: dashboard.php");
    exit;
}

// Hitung total produk
$stmtProd = $pdo->query("SELECT COUNT(*) FROM produk");
$total_produk = $stmtProd->fetchColumn();

// Hitung total pesanan masuk
$stmtPesanan = $pdo->query("SELECT COUNT(*) FROM pesanan");
$total_pesanan = $stmtPesanan->fetchColumn();

// Hitung pesanan menunggu konfirmasi
$stmtPending = $pdo->query("SELECT COUNT(*) FROM pesanan WHERE status_pesanan = 'Menunggu Konfirmasi'");
$pesanan_pending = $stmtPending->fetchColumn();

// Ambil 5 pesanan terbaru
$stmtLatest = $pdo->query("SELECT * FROM pesanan ORDER BY id DESC LIMIT 5");
$latest_orders = $stmtLatest->fetchAll();

// Hitung jumlah produk untuk Grafik Donat (Kategori Banchips vs Baju Workshirt)
$stmtBanchip = $pdo->query("SELECT COUNT(*) FROM produk WHERE nama_produk LIKE '%Banchip%' OR nama_produk LIKE '%Balado%' OR nama_produk LIKE '%Tiramisu%'");
$jml_banchip = $stmtBanchip->fetchColumn();

$stmtBaju = $pdo->query("SELECT COUNT(*) FROM produk WHERE nama_produk LIKE '%Workshirt%' OR nama_produk LIKE '%Baju%' OR nama_produk LIKE '%Kemeja%'");
$jml_baju = $stmtBaju->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - HMAN Store</title>
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

    <!-- HEADER / NAVBAR ADMIN -->
    <header class="bg-[#3D2514] text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-brandYellow rounded-full flex items-center justify-center text-2xl shadow-sm">👑</div>
                <div>
                    <h1 class="font-black text-xl sm:text-2xl tracking-wide text-brandYellow leading-none">ADMIN HMAN</h1>
                    <span class="text-[10px] sm:text-xs text-amber-200 font-medium tracking-widest uppercase block mt-1">PANEL KELOLA TOKO</span>
                </div>
            </div>
            <nav class="hidden md:flex items-center gap-6 font-bold text-sm">
                <a href="dashboard.php" class="text-brandYellow underline">Dashboard</a>
                <a href="products.php" class="hover:text-brandYellow transition-colors">Kelola Produk</a>
                <a href="orders.php" class="hover:text-brandYellow transition-colors">Kelola Pesanan</a>
                <a href="../index.php" target="_blank" class="bg-amber-800/60 px-3 py-1.5 rounded-xl hover:bg-amber-800 transition-all text-xs flex items-center gap-1.5">Lihat Toko ↗</a>
            </nav>
            <div>
                <a href="logout.php" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl uppercase tracking-wider transition-all shadow-sm">Logout</a>
            </div>
        </div>
    </header>

    <!-- KONTEN UTAMA DASHBOARD -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
        
        <!-- BANNER SELAMAT DATANG -->
        <div class="bg-brandBrown rounded-3xl p-8 sm:p-10 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-xl border-2 border-amber-900">
            <div class="space-y-2 text-center md:text-left">
                <span class="bg-brandYellow text-brandBrown font-black text-xs px-3.5 py-1.5 rounded-full uppercase tracking-wider">Area Pengelola</span>
                <h1 class="text-2xl sm:text-4xl font-black text-brandYellow">Selamat Datang, Admin HMAN!</h1>
                <p class="text-xs sm:text-sm text-amber-100/90">Kelola produk, pantau pesanan pelanggan, dan atur operasional toko dengan mudah.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="products.php" class="px-5 py-3 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-2xl text-xs uppercase tracking-wider shadow-md transition-all">Kelola Produk</a>
                <a href="orders.php" class="px-5 py-3 bg-white hover:bg-slate-100 text-brandBrown font-black rounded-2xl text-xs uppercase tracking-wider shadow-md transition-all">Kelola Pesanan</a>
            </div>
        </div>

        <!-- STATISTIK KOTAK (CARDS) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Produk</span>
                <h3 class="text-3xl font-black text-brandBrown"><?= $total_produk ?></h3>
                <a href="products.php" class="text-xs font-bold text-amber-800 hover:underline block pt-2">Lihat daftar produk &rarr;</a>
            </div>

            <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Total Pesanan Masuk</span>
                <h3 class="text-3xl font-black text-brandBrown"><?= $total_pesanan ?></h3>
                <a href="orders.php" class="text-xs font-bold text-amber-800 hover:underline block pt-2">Lihat semua pesanan &rarr;</a>
            </div>

            <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pesanan Menunggu Konfirmasi</span>
                <h3 class="text-3xl font-black text-amber-600"><?= $pesanan_pending ?></h3>
                <a href="orders.php" class="text-xs font-bold text-amber-800 hover:underline block pt-2">Proses pesanan baru &rarr;</a>
            </div>
        </div>

        <!-- SECTION GRAFIK DONAT STATISTIK KATEGORI -->
        <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 shadow-sm space-y-6 flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-black text-brandBrown">Statistik Kategori Produk</h2>
                <p class="text-xs text-slate-500">Perbandingan produk Banchips & Baju</p>
            </div>
            <div class="max-w-[220px] mx-auto py-2">
                <canvas id="categoryDoughnutChart"></canvas>
            </div>
            <div class="text-center text-xs text-slate-400 font-medium pt-2 border-t border-amber-100">
                Data diambil secara real-time dari database.
            </div>
        </div>

        <!-- TABEL 5 PESANAN TERBARU (DENGAN TOMBOL DETAIL DAN HAPUS AMAN) -->
        <div class="bg-white border-2 border-amber-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-amber-100 pb-4">
                <div>
                    <h2 class="text-lg sm:text-xl font-black text-brandBrown">5 Pesanan Terbaru</h2>
                    <p class="text-xs text-slate-500">Daftar transaksi terakhir dari pelanggan store.</p>
                </div>
                <a href="orders.php" class="text-xs font-bold text-amber-800 hover:underline">Lihat Semua Pesanan &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-brandCream/50 text-[11px] font-black text-brandBrown uppercase tracking-wider border-b border-amber-100">
                            <th class="p-3.5 rounded-l-xl">ID / Kode</th>
                            <th class="p-3.5">Pemesan</th>
                            <th class="p-3.5">Total</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 rounded-r-xl text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100 text-xs">
                        <?php if (!empty($latest_orders)): ?>
                            <?php foreach ($latest_orders as $ord): ?>
                                <tr class="hover:bg-brandCream/20 transition-all">
                                    <td class="p-3.5 font-bold text-brandBrown">HMAN-<?= str_pad($ord['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                    <td class="p-3.5 font-medium text-slate-700"><?= htmlspecialchars($ord['nama_pemesan'] ?? 'Pelanggan') ?></td>
                                    <td class="p-3.5 font-bold text-brandBrown">Rp <?= number_format($ord['total_harga'], 0, ',', '.') ?></td>
                                    <td class="p-3.5">
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 font-bold rounded-full text-[10px]">
                                            <?= htmlspecialchars($ord['status_pesanan']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-center flex items-center justify-center gap-2">
                                        <!-- TOMBOL DETAIL -->
                                        <a href="order_detail.php?id=<?= $ord['id'] ?>" class="px-3 py-1.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-lg text-[10px] uppercase shadow-sm transition-all">Detail</a>
                                        <!-- TOMBOL HAPUS AMAN LANGSUNG KE DASHBOARD -->
                                        <a href="dashboard.php?action=delete&id=<?= $ord['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?');" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-[10px] uppercase shadow-sm transition-all">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="p-6 text-center text-slate-400">Belum ada data pesanan masuk.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- SCRIPT CHART.JS UNTUK GRAFIK DONAT -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('categoryDoughnutChart').getContext('2d');
        const categoryDoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Produk Banchips', 'Baju Workshirt'],
                datasets: [{
                    data: [<?= $jml_banchip ?>, <?= $jml_baju ?>],
                    backgroundColor: [
                        '#FFDE59', // Warna Kuning Khas Hman Store
                        '#4A2E18'  // Warna Cokelat Tua Hman Store
                    ],
                    borderColor: '#FFF8E8',
                    borderWidth: 4,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                family: 'sans-serif',
                                weight: 'bold',
                                size: 11
                            },
                            color: '#4A2E18',
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#3D2514',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 11 },
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                cutout: '68%'
            }
        });
    </script>

    <!-- FOOTER ADMIN -->
    <footer class="bg-[#3D2514] text-amber-200 text-center py-8 text-xs border-t border-amber-900 mt-16 space-y-2">
        <p class="font-bold text-brandYellow text-sm">HMAN STORE - PANEL ADMIN</p>
        <p>&copy; <?= date('Y') ?> Hak Cipta Dilindungi. Sistem Manajemen Toko Online Hman Store.</p>
    </footer>

</body>
</html>