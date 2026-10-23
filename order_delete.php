<?php
// admin/order_delete.php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['id'] ?? 0);

    if ($order_id > 0) {
        $pdo = getDBConnection();
        try {
            $pdo->beginTransaction();

            // Hapus item pesanan jika tabelnya ada
            $check = $pdo->query("SHOW TABLES LIKE 'pesanan_item'")->fetch();
            if ($check) {
                $stmt_item = $pdo->prepare("DELETE FROM pesanan_item WHERE pesanan_id = :id");
                $stmt_item->execute([':id' => $order_id]);
            }

            // Hapus data utama pesanan
            $stmt = $pdo->prepare("DELETE FROM pesanan WHERE id = :id");
            $stmt->execute([':id' => $order_id]);

            $pdo->commit();
            $_SESSION['flash_success'] = "Pesanan berhasil dihapus dari sistem.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Gagal menghapus pesanan: " . $e->getMessage();
        }
    }
}

header("Location: orders.php");
exit;