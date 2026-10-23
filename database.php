<?php
// config/database.php
if (session_status() === PHP_SESSION_NONE) {
    ob_start(); // Mencegah error headers already sent
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hman_store');
define('SHIPPING_FEE', 5000.00);

function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die("<h3 style='color:red; text-align:center; margin-top:50px;'>Koneksi Database Gagal: " . $e->getMessage() . "</h3>");
    }
}

function sanitize_input($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

function format_rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function get_cart_items() {
    $items = $_SESSION['cart'] ?? [];
    // Pastikan setiap item memiliki subtotal agar tidak undefined
    foreach ($items as $key => $item) {
        $harga = $item['harga'] ?? 0;
        $jumlah = $item['jumlah'] ?? 0;
        $items[$key]['subtotal'] = $harga * $jumlah;
    }
    return $items;
}

function get_cart_count() {
    $items = get_cart_items();
    $count = 0;
    foreach ($items as $item) {
        $count += $item['jumlah'];
    }
    return $count;
}

function get_cart_totals() {
    $items = get_cart_items();
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['subtotal'];
    }
    $ongkir = $subtotal > 0 ? SHIPPING_FEE : 0;
    return [
        'subtotal'    => $subtotal,
        'ongkir'      => $ongkir,
        'total_harga' => $subtotal + $ongkir
    ];
}