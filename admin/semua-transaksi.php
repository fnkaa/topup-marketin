<?php
session_start();
include '../koneksi.php';

// Proteksi Akun Admin
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$msg = '';

// Pemrosesan Ubah Status Transaksi
if (isset($_GET['status']) && isset($_GET['order_id'])) {
    $status   = mysqli_real_escape_string($koneksi, $_GET['status']);
    $order_id = mysqli_real_escape_string($koneksi, $_GET['order_id']);
    
    if (in_array($status, ['Pending', 'Sukses', 'Expired'])) {
        $query_update = "UPDATE transaksi SET status_pembayaran = '$status' WHERE order_id = '$order_id'";
        if (mysqli_query($koneksi, $query_update)) {
            $msg = "<div class='bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-xl mb-4'>✨ Status Order #$order_id berhasil diubah menjadi $status!</div>";
        }
    }
}

// Sinkronisasi data sesuai foreign key market_in.sql (t.id_user = u.id)
$query = "SELECT t.*, u.username, g.nama_game 
          FROM transaksi t
          JOIN users u ON t.id_user = u.id
          JOIN game g ON t.id_game = g.id_game
          ORDER BY t.created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Riwayat Transaksi - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0a0a0a] text-white p-6 pt-24">

    <header class="bg-zinc-950 border-b border-zinc-800 px-6 py-4 flex justify-between items-center fixed top-0 left-0 w-full z-50">
        <div class="flex items-center gap-3">
            <span class="text-lg font-bold tracking-wider text-[#f3af22]">MARKET.<span class="text-white">IN</span></span>
            <span class="text-[9px] bg-zinc-800 border border-zinc-700 text-zinc-400 px-2 py-0.5 rounded uppercase font-bold">Admin Panel</span>
        </div>
        <a href="dashboard.php" class="text-xs text-zinc-400 hover:text-[#f3af22] transition">← Kembali ke Dashboard</a>
    </header>

    <div class="max-w-[1300px] mx-auto grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <aside class="space-y-1 lg:col-span-1">
    <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest px-3 mb-2">Menu Utama</div>
    <a href="dashboard.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">📊 Dashboard Utama</a>
    <a href="semua-transaksi.php" class="block bg-zinc-900 text-[#f3af22] border border-zinc-800 px-4 py-2.5 rounded-xl text-xs font-bold">🧾 Semua Riwayat Transaksi</a>
    <a href="kelola-game.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">💎 Kelola Item Game</a>
    <a href="kelola-user.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">👥 Kelola Data Member</a>
    <a href="kelola-voucher.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">🎫 Kode Voucher Promo</a>
</aside>

        <section class="lg:col-span-3 bg-zinc-900 border border-zinc-800 p-6 rounded-2xl">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">🧾 Semua Log Transaksi Pelanggan</h3>
            
            <?= $msg; ?>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-500 font-bold">
                            <th class="pb-3">Order ID / Tanggal</th>
                            <th class="pb-3">Username</th>
                            <th class="pb-3">Game</th>
                            <th class="pb-3">Varian Item</th>
                            <th class="pb-3">Target ID</th>
                            <th class="pb-3 text-right">Total Bayar</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-center">Aksi Panel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-zinc-950/20 transition">
                                <td class="py-3.5">
                                    <span class="block font-mono font-bold text-white">#<?= $row['order_id']; ?></span>
                                    <span class="text-[10px] text-zinc-500 block font-mono mt-0.5"><?= $row['created_at']; ?></span>
                                </td>
                                <td class="py-3.5 text-zinc-400">@<?= htmlspecialchars($row['username']); ?></td>
                                <td class="py-3.5 font-medium text-[#f3af22]"><?= htmlspecialchars($row['nama_game']); ?></td>
                                <td class="py-3.5 font-medium text-white"><?= htmlspecialchars($row['nominal_item']); ?></td>
                                <td class="py-3.5 font-mono text-zinc-400"><?= htmlspecialchars($row['target_id']); ?></td>
                                <td class="py-3.5 text-right font-mono font-bold text-emerald-400">Rp <?= number_format($row['total_pembayaran'], 0, ',', '.'); ?></td>
                                <td class="py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded font-black text-[9px] uppercase <?= $row['status_pembayaran'] == 'Sukses' ? 'bg-green-500/20 text-green-400' : ($row['status_pembayaran'] == 'Expired' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400') ?>">
                                        <?= $row['status_pembayaran']; ?>
                                    </span>
                                </td>
                                <td class="py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <?php if($row['status_pembayaran'] !== 'Sukses'): ?>
                                            <a href="?status=Sukses&order_id=<?= $row['order_id']; ?>" class="bg-green-600 text-white px-2 py-1 rounded-md hover:bg-green-500 transition font-bold text-[10px]">Sukses ✓</a>
                                        <?php endif; ?>
                                        <?php if($row['status_pembayaran'] !== 'Expired'): ?>
                                            <a href="?status=Expired&order_id=<?= $row['order_id']; ?>" class="bg-zinc-800 text-zinc-400 border border-zinc-700 px-2 py-1 rounded-md hover:bg-red-600 hover:text-white hover:border-transparent transition font-bold text-[10px]">Expired ❌</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-12 text-zinc-600 font-medium">Belum ada riwayat transaksi masuk dari pelanggan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>