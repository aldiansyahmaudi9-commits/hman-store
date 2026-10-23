<?php
// order_save.php
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$cart_items = get_cart_items();
if (empty($cart_items)) {
    $_SESSION['checkout_error'] = "Keranjang belanja kosong.";
    header('Location: cart.php');
    exit;
}

$nama_pemesan       = sanitize_input($_POST['nama_pemesan'] ?? '');
$nomor_wa           = preg_replace('/[^0-9+]/', '', trim($_POST['nomor_wa'] ?? ''));
$wilayah_pengiriman = sanitize_input($_POST['wilayah_pengiriman'] ?? '');
$alamat_ringkas     = sanitize_input($_POST['alamat_pengiriman'] ?? '');
$metode_pembayaran  = sanitize_input($_POST['metode_pembayaran'] ?? 'Transfer Bank');
$catatan            = sanitize_input($_POST['catatan'] ?? '');

if (empty($nama_pemesan) || empty($nomor_wa) || empty($alamat_ringkas)) {
    $_SESSION['checkout_error'] = "Semua kolom wajib diisi dengan benar.";
    header('Location: checkout.php');
    exit;
}

$alamat_lengkap = "Wilayah: " . $wilayah_pengiriman . " | Alamat: " . $alamat_ringkas;
if (!empty($catatan)) {
    $alamat_lengkap .= " | Catatan: " . $catatan;
}

$subtotal_server = 0;
foreach ($cart_items as $item) {
    $subtotal_server += ($item['harga'] * $item['jumlah']);
}
$ongkir_server = $subtotal_server > 0 ? SHIPPING_FEE : 0;
$total_harga_server = $subtotal_server + $ongkir_server;

$kode_pesanan = 'HMAN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

$pdo = getDBConnection();

try {
    $pdo->beginTransaction();

    $sql_pesanan = "INSERT INTO pesanan (kode_pesanan, nama_pemesan, nomor_wa, alamat_pengiriman, subtotal, ongkir, total_harga, metode_pembayaran, status_pembayaran, status_pesanan, catatan) 
                    VALUES (:kode_pesanan, :nama_pemesan, :nomor_wa, :alamat_pengiriman, :subtotal, :ongkir, :total_harga, :metode_pembayaran, 'belum_bayar', 'menunggu_konfirmasi', :catatan)";
    $stmt_pesanan = $pdo->prepare($sql_pesanan);
    $stmt_pesanan->execute([
        ':kode_pesanan'      => $kode_pesanan,
        ':nama_pemesan'      => $nama_pemesan,
        ':nomor_wa'          => $nomor_wa,
        ':alamat_pengiriman' => $alamat_lengkap,
        ':subtotal'          => $subtotal_server,
        ':ongkir'            => $ongkir_server,
        ':total_harga'       => $total_harga_server,
        ':metode_pembayaran' => $metode_pembayaran,
        ':catatan'           => $catatan
    ]);

    $pesanan_id = $pdo->lastInsertId();

    // Query detail pesanan disesuaikan tanpa kolom nama_produk
    $sql_detail = "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal) 
                   VALUES (:pesanan_id, :produk_id, :jumlah, :harga_satuan, :subtotal)";
    $stmt_detail = $pdo->prepare($sql_detail);

    $sql_update_stok = "UPDATE produk SET stok = stok - :jumlah WHERE id = :produk_id";
    $stmt_update_stok = $pdo->prepare($sql_update_stok);

    foreach ($cart_items as $item) {
        $item_subtotal = $item['harga'] * $item['jumlah'];

        $stmt_detail->execute([
            ':pesanan_id'   => $pesanan_id,
            ':produk_id'    => $item['id'],
            ':jumlah'       => $item['jumlah'],
            ':harga_satuan' => $item['harga'],
            ':subtotal'     => $item_subtotal
        ]);

        $stmt_update_stok->execute([
            ':jumlah'    => $item['jumlah'],
            ':produk_id' => $item['id']
        ]);
    }

    $pdo->commit();
    $_SESSION['cart'] = [];

    header('Location: order_success.php?kode=' . urlencode($kode_pesanan));
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['checkout_error'] = "Terjadi kendala sistem: " . $e->getMessage();
    header('Location: checkout.php');
    exit;
}