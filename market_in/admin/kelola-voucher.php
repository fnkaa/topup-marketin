<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$msg = '';

// Proses Tambah Voucher Baru
if (isset($_POST['tambah_voucher'])) {
    $kode_voucher      = strtoupper(mysqli_real_escape_string($koneksi, $_POST['kode_voucher']));
    $jumlah_potongan   = (float)$_POST['potongan_harga']; 
    $minimal_pembelian = (float)$_POST['minimal_pembelian']; 
    $status            = mysqli_real_escape_string($koneksi, $_POST['status']);
    $tipe_potongan     = 'Nominal'; 
    $expired_at        = date('Y-m-d H:i:s', strtotime('+6 months')); 

    $query = "INSERT INTO vouchers (kode_voucher, tipe_potongan, jumlah_potongan, minimal_pembelian, expired_at, status) 
              VALUES ('$kode_voucher', '$tipe_potongan', '$jumlah_potongan', '$minimal_pembelian', '$expired_at', '$status')";
              
    if (mysqli_query($koneksi, $query)) {
        $msg = "<div class='bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-xl mb-4'>✨ Voucher $kode_voucher berhasil dirilis!</div>";
    } else {
        $msg = "<div class='bg-red-500/10 border border-red-500 text-red-400 text-xs p-3 rounded-xl mb-4'>⚠️ Gagal merilis voucher: " . mysqli_error($koneksi) . "</div>";
    }
}

// Proses Hapus Voucher
if (isset($_GET['hapus'])) {
    $id_voucher = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM vouchers WHERE id_voucher = '$id_voucher'");
    header("Location: kelola-voucher.php");
    exit;
}

$vouchers = mysqli_query($koneksi, "SELECT * FROM vouchers ORDER BY id_voucher DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Voucher Promo - Admin</title>
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
    <a href="semua-transaksi.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">🧾 Semua Riwayat Transaksi</a>
    <a href="kelola-game.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">💎 Kelola Item Game</a>
    <a href="kelola-user.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">👥 Kelola Data Member</a>
    <a href="kelola-voucher.php" class="block bg-zinc-900 text-[#f3af22] border border-zinc-800 px-4 py-2.5 rounded-xl text-xs font-bold">🎫 Kode Voucher Promo</a>
</aside>

        <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-zinc-900 border border-zinc-800 p-6 rounded-2xl h-fit">
                <h3 class="text-xs font-bold text-[#f3af22] uppercase tracking-wider mb-4">🎫 Rilis Voucher Baru</h3>
                
                <?= $msg; ?>

                <form action="" method="POST" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-zinc-400 mb-1">Kode Voucher (Kapital)</label>
                        <input type="text" name="kode_voucher" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#f3af22]" placeholder="Contoh: HEMAT10">
                    </div>
                    <div>
                        <label class="block text-zinc-400 mb-1">Besar Potongan (Rp)</label>
                        <input type="number" name="potongan_harga" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#f3af22]" placeholder="Contoh: 10000">
                    </div>
                    <div>
                        <label class="block text-zinc-400 mb-1">Minimal Pembelian (Rp)</label>
                        <input type="number" name="minimal_pembelian" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-[#f3af22]" placeholder="Contoh: 50000" value="0">
                        <small class="text-[10px] text-zinc-500 mt-1 block">* Isi 0 jika tidak ada syarat minimal belanja</small>
                    </div>
                    <div>
                        <label class="block text-zinc-400 mb-1">Status Validasi</label>
                        <select name="status" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-white focus:outline-none">
                            <option value="Aktif">Aktif (Bisa Dipakai)</option>
                            <option value="Nonaktif">Nonaktif</option>
                        </select>
                    </div>
                    <button type="submit" name="tambah_voucher" class="w-full bg-[#f3af22] text-black font-bold py-3 rounded-xl uppercase tracking-wider text-[11px] transition hover:opacity-90">Rilis Voucher</button>
                </form>
            </div>

            <div class="md:col-span-2 bg-zinc-900 border border-zinc-800 p-6 rounded-2xl h-fit">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4">📋 List Voucher Promo Aktif</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-zinc-300">
                        <thead>
                            <tr class="border-b border-zinc-800 text-zinc-500 font-bold">
                                <th class="pb-3">Kode Unik</th>
                                <th class="pb-3 text-right">Potongan</th>
                                <th class="pb-3 text-right">Min. Belanja</th>
                                <th class="pb-3 text-center">Status</th>
                                <th class="pb-3 text-center">Opsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-800/50">
                            <?php if($vouchers && mysqli_num_rows($vouchers) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($vouchers)): ?>
                                <tr class="hover:bg-zinc-950/20 transition">
                                    <td class="py-3 font-mono font-bold text-[#f3af22] text-sm"><?= htmlspecialchars($row['kode_voucher']); ?></td>
                                    <td class="py-3 text-right font-bold text-emerald-400 font-mono">Rp <?= number_format($row['jumlah_potongan'], 0, ',', '.'); ?></td>
                                    <td class="py-3 text-right font-medium text-zinc-400 font-mono">Rp <?= number_format($row['minimal_pembelian'], 0, ',', '.'); ?></td>
                                    <td class="py-3 text-center">
                                        <span class="px-2 py-0.5 rounded font-bold text-[10px] <?= $row['status'] == 'Aktif' ? 'bg-green-500/20 text-green-400' : 'bg-zinc-800 text-zinc-500' ?>">
                                            <?= $row['status']; ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <a href="?hapus=<?= $row['id_voucher']; ?>" onclick="return confirm('Hapus voucher ini?')" class="bg-red-500/10 text-red-400 border border-red-500/20 px-2 py-1 rounded hover:bg-red-600 hover:text-white transition font-bold text-[10px]">Hapus</a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-6 text-zinc-600">Belum ada kode voucher promo dibuat.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</body>
</html>