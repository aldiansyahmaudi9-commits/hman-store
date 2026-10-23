<?php
// admin/orders.php
session_start();
require_once __DIR__ . '/../config/database.php';

// Pemeriksaan Autentikasi Admin
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$pdo = getDBConnection();

// Tangkap parameter pencarian & filter
$search = trim($_GET['search'] ?? '');
$status_pesanan = trim($_GET['status_pesanan'] ?? '');
$status_pembayaran = trim($_GET['status_pembayaran'] ?? '');

$query = "SELECT * FROM pesanan WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (kode_pesanan LIKE :search OR nama_pemesan LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($status_pesanan)) {
    $query .= " AND status_pesanan = :status_pesanan";
    $params[':status_pesanan'] = $status_pesanan;
}

if (!empty($status_pembayaran)) {
    $query .= " AND status_pembayaran = :status_pembayaran";
    $params[':status_pembayaran'] = $status_pembayaran;
}

$query .= " ORDER BY id DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pesanan - Admin HMAN Store</title>
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
    <header class="bg-[#3D2514] text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="dashboard.php" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brandYellow rounded-full flex items-center justify-center text-xl">🍌</div>
                <span class="font-black text-lg text-brandYellow">ADMIN HMAN STORE</span>
            </a>
            <nav class="flex items-center gap-4 text-xs font-bold">
                <a href="dashboard.php" class="hover:text-brandYellow">Dashboard</a>
                <a href="orders.php" class="text-brandYellow underline">Pesanan</a>
                <a href="logout.php" class="bg-red-600 px-3 py-1.5 rounded-xl text-white">Logout</a>
            </nav>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
        <h1 class="text-2xl sm:text-3xl font-black text-brandBrown">Manajemen Daftar Pesanan</h1>

        <!-- FILTER & PENCARIAN -->
        <form method="GET" class="bg-white p-4 sm:p-6 rounded-3xl border-2 border-amber-200 shadow-sm grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Cari (Kode/Nama)</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cth: HMAN-..." class="w-full p-2.5 text-xs border border-amber-200 rounded-xl focus:ring-2 focus:ring-brandYellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Status Pesanan</label>
                <select name="status_pesanan" class="w-full p-2.5 text-xs border border-amber-200 rounded-xl bg-white">
                    <option value="">Semua Status Pesanan</option>
                    <option value="menunggu_konfirmasi" <?= $status_pesanan === 'menunggu_konfirmasi' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                    <option value="diproses" <?= $status_pesanan === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                    <option value="dikirim" <?= $status_pesanan === 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                    <option value="selesai" <?= $status_pesanan === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="dibatalkan" <?= $status_pesanan === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Status Pembayaran</label>
                <select name="status_pembayaran" class="w-full p-2.5 text-xs border border-amber-200 rounded-xl bg-white">
                    <option value="">Semua Status Pembayaran</option>
                    <option value="belum_bayar" <?= $status_pembayaran === 'belum_bayar' ? 'selected' : '' ?>>Belum Bayar</option>
                    <option value="sudah_bayar" <?= $status_pembayaran === 'sudah_bayar' ? 'selected' : '' ?>>Sudah Bayar</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black rounded-xl text-xs uppercase shadow-sm">Filter</button>
                <a href="orders.php" class="px-3 py-2.5 bg-slate-200 text-slate-700 font-bold rounded-xl text-xs text-center">Reset</a>
            </div>
        </form>

        <!-- TABEL PESANAN -->
        <div class="bg-white border-2 border-amber-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-amber-50 text-brandBrown border-b border-amber-200">
                            <th class="p-4">Kode Pesanan</th>
                            <th class="p-4">Pelanggan</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Total</th>
                            <th class="p-4">Pembayaran</th>
                            <th class="p-4">Status Pesanan</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100">
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $o): ?>
                                <tr class="hover:bg-amber-50/50">
                                    <td class="p-4 font-mono font-bold text-brandBrown"><?= htmlspecialchars($o['kode_pesanan']) ?></td>
                                    <td class="p-4 font-medium"><?= htmlspecialchars($o['nama_pemesan']) ?></td>
                                    <td class="p-4 text-slate-500"><?= htmlspecialchars($o['created_at']) ?></td>
                                    <td class="p-4 font-bold text-brandBrown">Rp <?= number_format($o['total_harga'], 0, ',', '.') ?></td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold <?= $o['status_pembayaran'] === 'sudah_bayar' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' ?>">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $o['status_pembayaran']))) ?>
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                            <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $o['status_pesanan']))) ?>
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="order_detail.php?id=<?= $o['id'] ?>" class="inline-block px-3 py-1.5 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-bold rounded-xl text-[11px] shadow-sm">
                                            Detail &rarr;
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-500">Tidak ada data pesanan ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>