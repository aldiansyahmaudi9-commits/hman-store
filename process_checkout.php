<?php
// process_checkout.php
session_start();
require_once __DIR__ . '/config/database.php';

// Pastikan permintaan dihantar melalui kaedah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit;
}

// Semak sama ada troli belian mempunyai item
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit;
}

$pdo = getDBConnection();

// Ambil data daripada borang checkout
$nama = trim($_POST['nama'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$metode_pembayaran = trim($_POST['metode_pembayaran'] ?? 'QRIS');

if (empty($nama) || empty($telepon) || empty($alamat)) {
    echo "<script>alert('Sila isi semua maklumat yang diperlukan!'); window.location='checkout.php';</script>";
    exit;
}

try {
    // Mulakan Transaksi Pangkalan Data (Database Transaction)
    $pdo->beginTransaction();

    // Kira jumlah keseluruhan belian
    $total_belanja = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_belanja += $item['harga'] * $item['jumlah'];
    }

    // Simpan data ke dalam jadual 'pesanan'
    $stmtPesanan = $pdo->prepare("INSERT INTO pesanan (nama, telepon, alamat_pengiriman, total_harga, metode_pembayaran, status_pesanan, status_pembayaran, created_at) VALUES (:nama, :telepon, :alamat, :total, :metode, 'Menunggu Konfirmasi', 'Belum Bayar', NOW())");
    $stmtPesanan->execute([
        ':nama' => $nama,
        ':telepon' => $telepon,
        ':alamat' => $alamat,
        ':total' => $total_belanja,
        ':metode' => $metode_pembayaran
    ]);

    $pesanan_id = $pdo->lastInsertId();

    // Simpan butiran item pesanan dan kemas kini (tolak) stok produk
    $stmtItem = $pdo->prepare("INSERT INTO pesanan_item (pesanan_id, produk_id, nama_produk, harga, jumlah) VALUES (:pesanan_id, :produk_id, :nama_produk, :harga, :jumlah)");
    $stmtStock = $pdo->prepare("UPDATE produk SET stok = stok - :jumlah WHERE id = :id");

    foreach ($_SESSION['cart'] as $id => $item) {
        // Masukkan item ke dalam pesanan_item
        $stmtItem->execute([
            ':pesanan_id' => $pesanan_id,
            ':produk_id' => $id,
            ':nama_produk' => $item['nama_produk'],
            ':harga' => $item['harga'],
            ':jumlah' => $item['jumlah']
        ]);

        // Tolak stok produk
        $stmtStock->execute([
            ':jumlah' => $item['jumlah'],
            ':id' => $id
        ]);
    }

    // Sahkan transaksi jika semuanya berjaya
    $pdo->commit();

    // Kosongkan troli belian selepas pesanan berjaya diproses
    unset($_SESSION['cart']);

    // Alihkan ke halaman order_success.php bersama ID pesanan
    header("Location: order_success.php?id=" . $pesanan_id);
    exit;

} catch (Exception $e) {
    // Batalkan transaksi jika berlaku sebarang ralat
    $pdo->rollBack();
    echo "Terdapat ralat semasa memproses pesanan: " . $e->getMessage();
}