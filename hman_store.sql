-- ===================================================
-- SKEMA TABEL PESANAN & DETAIL PESANAN (TAHAP 4)
-- ===================================================

CREATE TABLE IF NOT EXISTS `pesanan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_pesanan` VARCHAR(30) NOT NULL UNIQUE,
  `nama_pemesan` VARCHAR(100) NOT NULL,
  `nomor_wa` VARCHAR(20) NOT NULL,
  `alamat_pengiriman` TEXT NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `ongkir` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `total_harga` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `metode_pembayaran` VARCHAR(50) NOT NULL DEFAULT 'Transfer Bank',
  `status_pembayaran` VARCHAR(30) NOT NULL DEFAULT 'belum_bayar',
  `status_pesanan` VARCHAR(30) NOT NULL DEFAULT 'menunggu_konfirmasi',
  `catatan` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `detail_pesanan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pesanan_id` INT NOT NULL,
  `produk_id` INT NOT NULL,
  `nama_produk` VARCHAR(100) NOT NULL,
  `jumlah` INT NOT NULL,
  `harga_satuan` DECIMAL(10,2) NOT NULL,
  `subtotal` DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`produk_id`) REFERENCES `produk`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;