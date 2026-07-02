<?php
session_start();
include 'koneksi.php';

// Proteksi: Jika belum login, arahkan ke login.php
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil riwayat transaksi user yang login bersangkutan beserta nama gamenya
$query_history = "SELECT t.*, g.nama_game 
                  FROM transaksi t 
                  JOIN game g ON t.id_game = g.id_game 
                  WHERE t.id_user = '$user_id' 
                  ORDER BY t.created_at DESC";
$result_history = mysqli_query($koneksi, $query_history);
$total_transaksi = mysqli_num_rows($result_history);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Transaksi - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0d0b04] text-white p-6 flex justify-center items-center min-h-screen">

    <div class="bg-[#0f1113] w-full max-w-[1150px] rounded-2xl border border-zinc-800 shadow-2xl p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="space-y-6 border-r border-zinc-800/50 pr-6">
            <div>
                <a href="index.php" class="text-xs text-[#f3af22] hover:underline">← Kembali ke Beranda</a>
                <h2 class="text-xl font-black mt-4">Halo, <?= htmlspecialchars($username); ?> 👋</h2>
                <p class="text-xs text-zinc-500">Selamat datang di riwayat belanja Anda.</p>
            </div>
            
            <div class="bg-zinc-900 p-4 rounded-xl border border-zinc-800 flex justify-between items-center">
                <div>
                    <h3 class="text-sm font-bold text-zinc-400">Total Transaksi</h3>
                    <p class="text-xs text-zinc-600">Seluruh pesanan Anda</p>
                </div>
                <span class="text-2xl font-black text-[#f3af22]"><?= $total_transaksi; ?></span>
            </div>
        </div>

        <div class="md:col-span-2 space-y-4 max-h-[450px] overflow-y-auto pr-2">
            <h3 class="font-bold text-sm text-[#f3af22] tracking-wider uppercase">📋 RIWAYAT INVOICE</h3>
            
            <?php if($total_transaksi > 0): ?>
                <?php while($transaksi = mysqli_fetch_assoc($result_history)): ?>
                    
                    <a href="pembayaran.php?order_id=<?= urlencode($transaksi['order_id']); ?>" class="block bg-zinc-900 p-4 rounded-xl border border-zinc-800 flex justify-between items-center text-sm hover:border-[#f3af22] hover:bg-zinc-800/50 transition cursor-pointer group">
                        <div>
                            <span class="text-[10px] text-zinc-500 block"><?= $transaksi['created_at']; ?></span>
                            <h4 class="font-bold text-white mt-0.5 group-hover:text-[#f3af22] transition"><?= htmlspecialchars($transaksi['nama_game']); ?> - <?= htmlspecialchars($transaksi['nominal_item']); ?></h4>
                            <p class="text-xs text-zinc-400">ID: <?= htmlspecialchars($transaksi['target_id']); ?> | <span class="text-zinc-500"><?= htmlspecialchars($transaksi['order_id']); ?></span></p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold block text-[#f3af22]">Rp <?= number_format($transaksi['total_pembayaran'], 0, ',', '.'); ?></span>
                            <span class="text-[10px] px-2 py-0.5 rounded inline-block font-semibold mt-1 <?= $transaksi['status_pembayaran'] == 'Sukses' ? 'bg-green-500/20 text-green-400' : ($transaksi['status_pembayaran'] == 'Expired' ? 'bg-red-500/20 text-red-400' : 'bg-yellow-500/20 text-yellow-400') ?>">
                                <?= $transaksi['status_pembayaran']; ?>
                            </span>
                        </div>
                    </a>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-20 text-zinc-600">
                    <p class="text-3xl mb-2">📥</p>
                    <p class="text-xs">Belum ada transaksi yang dilakukan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>