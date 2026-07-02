-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 11 Jun 2026 pada 07.48
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `market_in`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(10) NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('Super Admin','Customer Service') DEFAULT 'Super Admin',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `username`, `email`, `password`, `level`, `last_login`, `created_at`) VALUES
(1, 'M4rket.in Owner', 'adminmarketin', 'admin@marketin.com', '$2y$10$o3vU0R0B9b7OuxC1bA5XeuB2C7rD6Efe8A9WjMvC5O5.3BThXN9zG', 'Super Admin', NULL, '2026-06-11 03:34:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `game`
--

CREATE TABLE `game` (
  `id_game` int(10) NOT NULL,
  `nama_game` varchar(100) NOT NULL,
  `sub_game` varchar(100) NOT NULL,
  `slug_game` varchar(100) NOT NULL,
  `icon_text` varchar(100) NOT NULL,
  `bg_initial` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `game`
--

INSERT INTO `game` (`id_game`, `nama_game`, `sub_game`, `slug_game`, `icon_text`, `bg_initial`, `created_at`) VALUES
(1, 'Mobile Legends', 'Bang Bang', 'topup-ml.php', '?? Diamond', 'img/logo-ml.jpg', '2026-06-11 02:32:11'),
(2, 'PUBG Mobile', 'Level Infinite', 'topup-pubg.php', '?? UC', 'img/logo-pubg.jpg', '2026-06-11 02:32:11'),
(3, 'Free Fire', 'Garena', 'topup-ff.php', '?? Diamond & Member', 'img/logo-ff.png', '2026-06-11 02:32:11'),
(4, 'Roblox', 'Roblox Corp', 'topup-roblox.php', '?? Gift Card / Robux', 'img/logo-roblox.webp', '2026-06-11 02:32:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `game_items`
--

CREATE TABLE `game_items` (
  `id_item` int(10) NOT NULL,
  `id_game` int(10) NOT NULL,
  `nama_item` varchar(100) NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `status` enum('Tersedia','Kosong') DEFAULT 'Tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id_metode` int(10) NOT NULL,
  `kode_metode` varchar(20) NOT NULL,
  `nama_metode` varchar(45) NOT NULL,
  `kategori` enum('VA','E-Wallet','Retail') NOT NULL,
  `biaya_admin` decimal(10,2) DEFAULT 0.00,
  `logo_path` varchar(255) DEFAULT NULL,
  `status` enum('Aktif','Gangguan','Nonaktif') DEFAULT 'Aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `payment_methods`
--

INSERT INTO `payment_methods` (`id_metode`, `kode_metode`, `nama_metode`, `kategori`, `biaya_admin`, `logo_path`, `status`) VALUES
(1, 'bni_va', 'BNI Virtual Account', 'VA', 2500.00, NULL, 'Aktif'),
(2, 'bca_va', 'BCA Virtual Account', 'VA', 2500.00, NULL, 'Aktif'),
(3, 'qris', 'QRIS Dana/OVO/Gopay', 'E-Wallet', 0.00, NULL, 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_logs`
--

CREATE TABLE `system_logs` (
  `id_log` int(10) NOT NULL,
  `tipe_log` varchar(50) NOT NULL,
  `pesan` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(10) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `id_user` int(10) NOT NULL,
  `id_game` int(10) NOT NULL,
  `target_id` varchar(50) NOT NULL,
  `nominal_item` varchar(100) NOT NULL,
  `total_pembayaran` decimal(10,2) NOT NULL,
  `id_metode` int(10) NOT NULL,
  `status_pembayaran` enum('Pending','Sukses','Expired') DEFAULT 'Pending',
  `batas_waktu` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `whatsapp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `whatsapp`, `password`, `created_at`) VALUES
(1, 'Pembeli Marketin', 'usergaming', 'user@gmail.com', '081234567890', '$2y$10$7R0Zq8tZ6iB.3IskhZ2K9uxr0D62xEq7/jKxM9x6pIscB3Xh.g9wW', '2026-06-11 03:33:55'),
(2, '', 'kein24', 'afankaa.24@gmail.com', '', '$2y$10$ipyftMpwwBL7TQXKv8zzsOvymGE1lFnIvs0dKRtKhl5gsQQNvc3Ma', '2026-06-11 03:37:11'),
(3, 'anjar', 'kein25', 'garfildugaming@gmail.com', '0858585026589', '$2y$10$ok7jk.I9QfgDB4HasuuyZuHHUOKgDnPzLVavzQ1idHhd1q7qvQ..q', '2026-06-11 04:05:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vouchers`
--

CREATE TABLE `vouchers` (
  `id_voucher` int(10) NOT NULL,
  `kode_voucher` varchar(20) NOT NULL,
  `tipe_potongan` enum('Persen','Nominal') NOT NULL,
  `jumlah_potongan` decimal(10,2) NOT NULL,
  `minimal_pembelian` decimal(10,2) DEFAULT 0.00,
  `kuota` int(5) DEFAULT 0,
  `expired_at` datetime NOT NULL,
  `status` enum('Aktif','Nonaktif') DEFAULT 'Aktif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `vouchers`
--

INSERT INTO `vouchers` (`id_voucher`, `kode_voucher`, `tipe_potongan`, `jumlah_potongan`, `minimal_pembelian`, `kuota`, `expired_at`, `status`, `created_at`) VALUES
(1, 'TOPUPM4RKETIN', 'Nominal', 10000.00, 50000.00, 100, '2026-12-31 23:59:59', 'Aktif', '2026-06-11 02:33:28');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id_game`),
  ADD UNIQUE KEY `slug_game` (`slug_game`);

--
-- Indeks untuk tabel `game_items`
--
ALTER TABLE `game_items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `fk_items_game` (`id_game`);

--
-- Indeks untuk tabel `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id_metode`),
  ADD UNIQUE KEY `kode_metode` (`kode_metode`);

--
-- Indeks untuk tabel `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id_log`);

--
-- Indeks untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD UNIQUE KEY `order_id` (`order_id`),
  ADD KEY `fk_transaksi_user` (`id_user`),
  ADD KEY `fk_transaksi_game` (`id_game`),
  ADD KEY `fk_transaksi_metode` (`id_metode`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id_voucher`),
  ADD UNIQUE KEY `kode_voucher` (`kode_voucher`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `game`
--
ALTER TABLE `game`
  MODIFY `id_game` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `game_items`
--
ALTER TABLE `game_items`
  MODIFY `id_item` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id_metode` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id_log` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id_voucher` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `game_items`
--
ALTER TABLE `game_items`
  ADD CONSTRAINT `fk_items_game` FOREIGN KEY (`id_game`) REFERENCES `game` (`id_game`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_game` FOREIGN KEY (`id_game`) REFERENCES `game` (`id_game`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_metode` FOREIGN KEY (`id_metode`) REFERENCES `payment_methods` (`id_metode`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
