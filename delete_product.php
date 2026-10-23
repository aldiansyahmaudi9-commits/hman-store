<?php
// admin/delete_order.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $pdo = getDBConnection();

    try {
        // Hapus item detail pesanan terlebih dahulu
        $stmtItem = $pdo->prepare("DELETE FROM pesanan_item WHERE pesanan_id = :id");
        $stmtItem->execute([':id' => $id]);

        // Hapus data utama pesanan
        $stmtOrder = $pdo->prepare("DELETE FROM pesanan WHERE id = :id");
        $stmtOrder->execute([':id' => $id]);
    } catch (PDOException $e) {
        // Abaikan jika terjadi kendala database
    }
}

// Kembali ke halaman dashboard
header("Location: dashboard.php");
exit;