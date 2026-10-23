<?php
// admin/products.php
session_start();
require_once __DIR__ . '/../config/database.php';

$pdo = getDBConnection();
$message = '';

// PROSES TAMBAH PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_produk'])) {
    $nama_produk = trim($_POST['nama_produk'] ?? '');
    $deskripsi   = trim($_POST['deskripsi'] ?? '');
    $harga       = floatval($_POST['harga'] ?? 0);
    $stok        = intval($_POST['stok'] ?? 0);
    
    // Default gambar jika tidak diunggah
    $gambar_nama = 'banner.jpg';

    // Proses Unggah Gambar
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambar']['tmp_name'];
        $fileName    = $_FILES['gambar']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
            $uploadFileDir = __DIR__ . '/../assets/img/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $gambar_nama = $newFileName;
            }
        }
    }

    if (!empty($nama_produk) && $harga > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar) VALUES (:nama, :deskripsi, :harga, :stok, :gambar)");
            $stmt->execute([
                ':nama'      => $nama_produk,
                ':deskripsi' => $deskripsi,
                ':harga'     => $harga,
                ':stok'      => $stok,
                ':gambar'    => $gambar_nama
            ]);
            $message = "Produk berhasil ditambahkan!";
        } catch (PDOException $e) {
            $message = "Gagal menambahkan produk: " . $e->getMessage();
        }
    } else {
        $message = "Nama produk dan harga wajib diisi dengan benar!";
    }
}

// AMBIL SEMUA PRODUK
$stmt = $pdo->query("SELECT * FROM produk ORDER BY id DESC");
$products = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Produk - HMAN Store</title>
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
<body class="bg-brandCream min-h-screen text-slate-800 font-sans p-4 sm:p-8">

    <div class="max-w-5xl mx-auto bg-white border-2 border-amber-200 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        
        <!-- HEADER MANAJEMEN -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 border-b border-amber-100 pb-4">
            <div>
                <h1 class="text-2xl font-black text-brandBrown">Manajemen Produk</h1>
                <p class="text-xs text-slate-500">Kelola katalog Banchips dan Baju Workshirt HMAN Store.</p>
            </div>
            <a href="dashboard.php" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs rounded-xl transition-all">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <?php if (!empty($message)): ?>
            <div class="p-4 bg-amber-100 border border-amber-300 text-amber-800 text-xs font-bold rounded-xl">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- FORM TAMBAH PRODUK -->
        <div class="bg-brandCream/40 border border-amber-200 rounded-2xl p-5 space-y-4">
            <h2 class="text-base font-black text-brandBrown">+ Tambah Produk Baru</h2>
            <form action="products.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-brandBrown mb-1">NAMA PRODUK</label>
                    <input type="text" name="nama_produk" placeholder="Contoh: Banchip Balado / Workshirt HMAN" required class="w-full p-2.5 text-xs rounded-xl border border-amber-200 focus:outline-none focus:border-brandBrown bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-brandBrown mb-1">HARGA (Rp)</label>
                    <input type="number" name="harga" placeholder="Contoh: 12000" required class="w-full p-2.5 text-xs rounded-xl border border-amber-200 focus:outline-none focus:border-brandBrown bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-brandBrown mb-1">STOK</label>
                    <input type="number" name="stok" placeholder="Contoh: 50" required class="w-full p-2.5 text-xs rounded-xl border border-amber-200 focus:outline-none focus:border-brandBrown bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-brandBrown mb-1">FOTO PRODUK</label>
                    <input type="file" name="gambar" accept="image/*" class="w-full p-2 text-xs rounded-xl border border-amber-200 bg-white">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-brandBrown mb-1">DESKRIPSI PRODUK</label>
                    <textarea name="deskripsi" rows="2" placeholder="Tuliskan keunggulan produk di sini..." class="w-full p-2.5 text-xs rounded-xl border border-amber-200 focus:outline-none focus:border-brandBrown bg-white"></textarea>
                </div>

                <div class="md:col-span-2 text-right">
                    <button type="submit" name="tambah_produk" class="px-6 py-3 bg-brandYellow hover:bg-yellow-400 text-brandBrown font-black text-xs uppercase rounded-xl shadow-sm transition-all">
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>

        <!-- TABEL DAFTAR PRODUK -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-amber-100 text-brandBrown text-xs font-black uppercase">
                        <th class="p-3.5 rounded-l-xl">ID</th>
                        <th class="p-3.5">Gambar</th>
                        <th class="p-3.5">Nama Produk</th>
                        <th class="p-3.5">Harga</th>
                        <th class="p-3.5">Stok</th>
                        <th class="p-3.5 rounded-r-xl text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-amber-100 text-xs">
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr class="hover:bg-brandCream/20 transition-all">
                                <td class="p-3.5 font-bold text-brandBrown"><?= $p['id'] ?></td>
                                <td class="p-3.5">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden border border-amber-200 bg-brandCream">
                                        <img src="../assets/img/<?= htmlspecialchars($p['gambar'] ?? 'banner.jpg') ?>" alt="Foto" class="w-full h-full object-cover">
                                    </div>
                                </td>
                                <td class="p-3.5 font-bold text-slate-800"><?= htmlspecialchars($p['nama_produk']) ?></td>
                                <td class="p-3.5 font-bold text-brandBrown">Rp <?= number_format($p['harga'], 0, ',', '.') ?></td>
                                <td class="p-3.5 font-medium text-slate-600"><?= $p['stok'] ?> pcs</td>
                                <td class="p-3.5 text-center">
                                    <a href="delete_product.php?id=<?= $p['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-[10px] uppercase shadow-sm transition-all">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400">Belum ada produk terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</body>
</html>