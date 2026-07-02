<?php
session_start();
include '../koneksi.php';

// Proteksi Akun Admin
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$msg = '';

// Proses Hapus Akun User Terdaftar
if (isset($_GET['hapus'])) {
    $id_user = (int)$_GET['hapus'];
    $query_delete = "DELETE FROM users WHERE id = '$id_user'";
    if (mysqli_query($koneksi, $query_delete)) {
        $msg = "<div class='bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-xl mb-4'>✨ Akun member berhasil dihapus permanen dari server!</div>";
    }
}

// Tarik data seluruh user terdaftar
$users_query = mysqli_query($koneksi, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Data Member - Admin</title>
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
    <a href="kelola-user.php" class="block bg-zinc-900 text-[#f3af22] border border-zinc-800 px-4 py-2.5 rounded-xl text-xs font-bold">👥 Kelola Data Member</a>
    <a href="kelola-voucher.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">🎫 Kode Voucher Promo</a>
</aside>

        <section class="lg:col-span-3 bg-zinc-900 border border-zinc-800 p-6 rounded-2xl">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">👥 Daftar Akun Member Terdaftar</h3>
            
            <?= $msg; ?>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-zinc-300">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-500 font-bold">
                            <th class="pb-3">ID Member</th>
                            <th class="pb-3">Nama Lengkap</th>
                            <th class="pb-3">Username</th>
                            <th class="pb-3">Email Address</th>
                            <th class="pb-3">WhatsApp</th>
                            <th class="pb-3 font-mono">Join Date</th>
                            <th class="pb-3 text-center">Tindakan Keamanan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800/50">
                        <?php if ($users_query && mysqli_num_rows($users_query) > 0): ?>
                            <?php while($user = mysqli_fetch_assoc($users_query)): ?>
                            <tr class="hover:bg-zinc-950/20 transition">
                                <td class="py-4 font-mono font-bold text-zinc-500">#MBR-<?= $user['id']; ?></td>
                                <td class="py-4 text-white font-semibold"><?= htmlspecialchars($user['fullname'] ?? 'N/A'); ?></td>
                                <td class="py-4 text-[#f3af22] font-medium">@<?= htmlspecialchars($user['username']); ?></td>
                                <td class="py-4 text-zinc-400"><?= htmlspecialchars($user['email']); ?></td>
                                <td class="py-4 font-mono text-emerald-400">
                                    <?= !empty($user['whatsapp']) ? htmlspecialchars($user['whatsapp']) : "<span class='text-zinc-600'>-</span>"; ?>
                                </td>
                                <td class="py-4 text-zinc-500 font-mono"><?= $user['created_at'] ?? 'N/A'; ?></td>
                                <td class="py-4 text-center">
                                    <a href="?hapus=<?= $user['id']; ?>" onclick="return confirm('⚠️ Menghapus member ini akan menghilangkan seluruh hak akses login secara permanen. Lanjutkan?')" class="bg-red-500/10 text-red-400 border border-red-500/20 px-3 py-1.5 rounded-lg hover:bg-red-600 hover:text-white transition font-medium text-[11px]">
                                        Hapus & Blokir
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-10 text-zinc-600 font-medium">Belum ada user/pembeli yang mendaftar akun di website Anda.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</body>
</html>