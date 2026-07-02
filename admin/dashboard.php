<?php
// File: admin/dashboard.php
session_start();
include '../koneksi.php';

// Proteksi Keamanan
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

// 📊 STATISTIK DASHBOARD
$count_users = mysqli_num_rows(mysqli_query($koneksi, "SELECT id FROM users"));
$count_games = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_game FROM game"));
$count_trx   = mysqli_num_rows(mysqli_query($koneksi, "SELECT id_transaksi FROM transaksi"));

// Menghitung total omset pendapatan
$omset_query = mysqli_query($koneksi, "SELECT SUM(total_pembayaran) as total FROM transaksi WHERE status_pembayaran = 'Sukses'");
$omset_data  = mysqli_fetch_assoc($omset_query);
$total_omset = $omset_data['total'] ?? 0;

// 📋 MONITORING TRANSAKSI REAL-TIME (Sinkron t.id = u.id)
$query_trx = "SELECT t.*, u.username as nama_user, g.nama_game 
              FROM transaksi t
              LEFT JOIN users u ON t.id_user = u.id 
              LEFT JOIN game g ON t.id_game = g.id_game
              ORDER BY t.created_at DESC LIMIT 5";
$result_trx = mysqli_query($koneksi, $query_trx);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0a0a0a] text-white">

    <header class="bg-zinc-950 border-b border-zinc-800 px-6 py-4 flex justify-between items-center fixed top-0 left-0 w-full z-50">
        <div class="flex items-center gap-2">
            <span class="text-lg font-bold tracking-wider text-[#f3af22]">MARKET.<span class="text-white">IN</span></span>
            <span class="text-[9px] bg-zinc-800 border border-zinc-700 text-zinc-400 px-2 py-0.5 rounded uppercase font-bold">Admin Panel</span>
        </div>
        <div class="flex items-center gap-4 text-xs">
            <div class="text-right">
                <span class="block font-bold text-white"><?= htmlspecialchars($_SESSION['admin_name']); ?></span>
                <span class="block text-[10px] text-[#f3af22]"><?= htmlspecialchars($_SESSION['admin_level']); ?></span>
            </div>
            <a href="logout.php" class="bg-red-600/20 text-red-400 border border-red-500/30 px-3 py-1.5 rounded-xl hover:bg-red-600 hover:text-white transition font-semibold">Keluar 🚪</a>
        </div>
    </header>

    <div class="max-w-[1200px] mx-auto px-6 pt-24 pb-12 grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="space-y-1 lg:col-span-1">
    <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest px-3 mb-2">Menu Utama</div>
    <a href="dashboard.php" class="block bg-zinc-900 text-[#f3af22] border border-zinc-800 px-4 py-2.5 rounded-xl text-xs font-bold">📊 Dashboard Utama</a>
    <a href="semua-transaksi.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">🧾 Semua Riwayat Transaksi</a>
    <a href="kelola-game.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">💎 Kelola Item Game</a>
    <a href="kelola-user.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">👥 Kelola Data Member</a>
    <a href="kelola-voucher.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">🎫 Kode Voucher Promo</a>
</aside>

        <section class="lg:col-span-3 space-y-6">
            <div>
                <h2 class="text-xl font-black text-white">Selamat Datang Kembali! 👋</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Berikut ringkasan statistik server Market.in saat ini.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-zinc-900 p-4 border border-zinc-800 rounded-2xl">
                    <span class="text-[11px] text-zinc-400 font-medium block">Total Member</span>
                    <span class="text-2xl font-black text-white block mt-1"><?= $count_users; ?></span>
                </div>
                <div class="bg-zinc-900 p-4 border border-zinc-800 rounded-2xl">
                    <span class="text-[11px] text-zinc-400 font-medium block">Game Aktif</span>
                    <span class="text-2xl font-black text-white block mt-1"><?= $count_games; ?></span>
                </div>
                <div class="bg-zinc-900 p-4 border border-zinc-800 rounded-2xl">
                    <span class="text-[11px] text-zinc-400 font-medium block">Total Invoice</span>
                    <span class="text-2xl font-black text-white block mt-1"><?= $count_trx; ?></span>
                </div>
                <div class="bg-zinc-900 p-4 border-2 border-emerald-500/20 bg-emerald-950/10 rounded-2xl">
                    <span class="text-[11px] text-emerald-400 font-bold block">Omset Bersih</span>
                    <span class="text-sm font-black text-emerald-300 block mt-2">Rp <?= number_format($total_omset, 0, ',', '.'); ?></span>
                </div>
            </div>

            <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
                <h3 class="text-xs font-bold text-[#f3af22] tracking-wider uppercase mb-4">📋 5 Transaksi Terbaru Masuk</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead>
                            <tr class="border-b border-zinc-800 text-zinc-500 font-bold">
                                <th class="pb-3">Order ID</th>
                                <th class="pb-3">Username</th>
                                <th class="pb-3">Game</th>
                                <th class="pb-3">Item Nominal</th>
                                <th class="pb-3 text-right">Total Bayar</th>
                                <th class="pb-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            <?php if ($result_trx && mysqli_num_rows($result_trx) > 0): ?>
                                <?php while($trx = mysqli_fetch_assoc($result_trx)): ?>
                                    <tr>
                                        <td class="py-3 font-mono text-[#f3af22]"><?= $trx['order_id']; ?></td>
                                        <td class="py-3"><?= $trx['nama_user'] ? htmlspecialchars($trx['nama_user']) : 'Guest / Non-Member'; ?></td>
                                        <td class="py-3"><?= htmlspecialchars($trx['nama_game']); ?></td>
                                        <td class="py-3"><?= htmlspecialchars($trx['nominal_item']); ?></td>
                                        <td class="py-3 text-right font-bold">Rp <?= number_format($trx['total_pembayaran'], 0, ',', '.'); ?></td>
                                        <td class="py-3 text-center">
                                            <span class="px-2 py-0.5 rounded font-bold text-[10px] <?= $trx['status_pembayaran'] == 'Sukses' ? 'bg-green-500/20 text-green-400' : ($trx['status_pembayaran'] == 'Expired' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400') ?>">
                                                <?= $trx['status_pembayaran']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-zinc-600">Belum ada transaksi masuk dari pembeli.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</body>
</html>