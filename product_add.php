<?php
// admin/product_add.php
require_once __DIR__ . '/header.php';

$pdo = getDBConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Validasi CSRF gagal.");
    }

    $nama_produk = sanitize_input($_POST['nama_produk'] ?? '');
    $kategori    = sanitize_input($_POST['kategori'] ?? '');
    $deskripsi   = sanitize_input($_POST['deskripsi'] ?? '');
    $harga       = floatval($_POST['harga'] ?? 0);
    $berat_gram  = intval($_POST['berat_gram'] ?? 100);
    $stok        = intval($_POST['stok'] ?? 0);
    $slug        = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nama_produk)));
    $gambar_nama = '';

    if (empty($nama_produk) || $harga <= 0) {
        $error = "Nama produk dan harga yang valid wajib diisi.";
    } else {
        // Penanganan Unggahan Gambar yang Aman
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp  = $_FILES['gambar']['tmp_name'];
            $file_name = $_FILES['gambar']['name'];
            $file_size = $_FILES['gambar']['size'];
            $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
            $max_size = 2 * 1024 * 1024; // Maksimal 2MB

            if (!in_array($file_ext, $allowed_exts)) {
                $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
            } elseif ($file_size > $max_size) {
                $error = "Ukuran gambar maksimal 2MB.";
            } else {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $gambar_nama = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
                $destination = $upload_dir . $gambar_nama;

                if (!move_uploaded_file($file_tmp, $destination)) {
                    $error = "Gagal mengunggah file gambar ke server.";
                }
            }
        }

        if (empty($error)) {
            $stmt = $pdo->prepare("INSERT INTO produk (nama_produk, kategori, slug, deskripsi, harga, berat_gram, stok, gambar) VALUES (:nama, :kat, :slug, :desc, :harga, :berat, :stok, :gambar)");
            $stmt->execute([
                ':nama'   => $nama_produk,
                ':kat'    => $kategori,
                ':slug'   => $slug,
                ':desc'   => $deskripsi,
                ':harga'  => $harga,
                ':berat'  => $berat_gram,
                ':stok'   => $stok,
                ':gambar' => $gambar_nama
            ]);

            $_SESSION['flash_success'] = "Produk berhasil ditambahkan!";
            header("Location: products.php");
            exit;
        }
    }
}
?>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="border-b border-amber-200 pb-4">
        <h1 class="text-2xl font-black text-brandBrown">Tambah Produk Baru</h1>
        <p class="text-xs text-slate-600">Masukkan rincian varian keripik pisang baru.</p>
    </div>

    <?php if (!empty($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-800 p-3 rounded-xl text-xs">⚠️ <?= sanitize_input($error) ?></div>
    <?php endif; ?>

    <form action="product_add.php" method="POST" enctype="multipart/form-data" class="bg-white border-2 border-amber-200 rounded-3xl p-6 sm:p-8 space-y-4 shadow-sm">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div>
            <label class="block text-xs font-bold text-brandBrown mb-1">Nama Produk *</label>
            <input type="text" name="nama_produk" required class="w-full px-4 py-2 text-xs sm:text-sm border border-amber-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brandYellow">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Kategori / Rasa</label>
                <input type="text" name="kategori" placeholder="Contoh: Manis / Cokelat" class="w-full px-4 py-2 text-xs sm:text-sm border border-amber-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brandYellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Harga (Rp) *</label>
                <input type="number" name="harga" required min="0" step="500" class="w-full px-4 py-2 text-xs sm:text-sm border border-amber-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brandYellow">
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Stok Awal *</label>
                <input type="number" name="stok" required min="0" value="50" class="w-full px-4 py-2 text-xs sm:text-sm border border-amber-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brandYellow">
            </div>
            <div>
                <label class="block text-xs font-bold text-brandBrown mb-1">Berat Bersih (Gram)</label>
                <input type="number" name="berat_gram" value="100" class="w-full px-4 py-2 text-xs sm:text-sm border border-amber-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brandYellow">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-brandBrown mb-1">Foto Produk (Opsional)</label>
            <input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-brandBrown hover:file:bg-amber-200">
            <span class="text-[10px] text-slate-400 mt-1 block">Format: JPG, PNG, WEBP. Maksimal 2MB.</span>
        </div>
        <div>
            <label class="block text-xs font-bold text-brandBrown mb-1">Deskripsi Produk</label>
            <textarea name="deskripsi" rows="3" class="w-full px-4 py-2 text-xs sm:text-sm border border-amber-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brandYellow"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="products.php" class="px-5 py-2.5 rounded-xl border border-amber-300 text-xs font-bold text-slate-600 hover:bg-slate-100">Batal</a>
            <button type="submit" class="bg-brandYellow hover:bg-yellow-400 text-brandBrown font-extrabold px-6 py-2.5 rounded-xl text-xs uppercase shadow-sm">Simpan Produk</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>