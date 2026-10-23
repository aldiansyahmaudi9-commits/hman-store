<?php
// cart_action.php
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'add') {
    $produk_id = intval($_POST['produk_id'] ?? 0);
    $jumlah    = intval($_POST['jumlah'] ?? 1);

    if ($produk_id > 0) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM produk WHERE id = :id AND is_active = 1");
        $stmt->execute([':id' => $produk_id]);
        $product = $stmt->fetch();

        if ($product) {
            if (isset($_SESSION['cart'][$produk_id])) {
                $_SESSION['cart'][$produk_id]['jumlah'] += $jumlah;
            } else {
                $_SESSION['cart'][$produk_id] = [
                    'id'          => $product['id'],
                    'nama_produk' => $product['nama_produk'],
                    'harga'       => $product['harga'],
                    'jumlah'      => $jumlah
                ];
            }
            $_SESSION['flash_success'] = "Produk berhasil ditambahkan ke keranjang!";
        }
    }
    header("Location: cart.php");
    exit;

} elseif ($action === 'change_qty') {
    $produk_id = intval($_POST['produk_id'] ?? 0);
    $change    = intval($_POST['change'] ?? 0);

    if ($produk_id > 0 && isset($_SESSION['cart'][$produk_id])) {
        $_SESSION['cart'][$produk_id]['jumlah'] += $change;
        if ($_SESSION['cart'][$produk_id]['jumlah'] < 1) {
            unset($_SESSION['cart'][$produk_id]);
        }
    }
    header("Location: cart.php");
    exit;

} elseif ($action === 'delete') {
    $id = intval($_GET['id'] ?? 0);
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("Location: cart.php");
    exit;

} elseif ($action === 'clear') {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;

} else {
    header("Location: index.php");
    exit;
}