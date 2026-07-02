<?php
// File: admin/kelola-item.php (Disinkronkan sebagai kelola-game.php)
session_start();
include '../koneksi.php';

// Proteksi Keamanan Admin
if (!isset($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header("Location: ../login.php");
    exit;
}

$msg = '';
$filter_param = isset($_GET['filter_game']) ? '&filter_game=' . urlencode($_GET['filter_game']) : '';

// 1. PROSES TAMBAH ITEM GAME
if (isset($_POST['tambah_item'])) {
    $id_game    = (int)$_POST['id_game'];
    $nama_item  = mysqli_real_escape_string($koneksi, $_POST['nama_item']);
    $harga_jual = (float)$_POST['harga_jual'];
    $status     = mysqli_real_escape_string($koneksi, $_POST['status']);
    
    if (!empty($nama_item) && $id_game > 0) {
        $query_insert = "INSERT INTO game_items (id_game, nama_item, harga_jual, status) VALUES ($id_game, '$nama_item', $harga_jual, '$status')";
        if (mysqli_query($koneksi, $query_insert)) {
            $msg = "<div class='bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-xl mb-4'>✨ Item game baru berhasil ditambahkan!</div>";
        } else {
            $msg = "<div class='bg-red-500/10 border border-red-500 text-red-400 text-xs p-3 rounded-xl mb-4'>❌ Gagal menambah item: " . mysqli_error($koneksi) . "</div>";
        }
    }
}

// 🛠️ KODE BARU: PROSES UPDATE / UBAH NAMA DAN HARGA ITEM SECARA MASAL
if (isset($_POST['update_items'])) {
    if (isset($_POST['items']) && is_array($_POST['items'])) {
        $sukses = 0;
        foreach ($_POST['items'] as $id_item => $data) {
            $id_item    = (int)$id_item;
            $nama_item  = mysqli_real_escape_string($koneksi, $data['nama_item']);
            $harga_jual = (float)$data['harga_jual'];
            
            $query_update_bulk = "UPDATE game_items SET nama_item = '$nama_item', harga_jual = $harga_jual WHERE id_item = $id_item";
            if (mysqli_query($koneksi, $query_update_bulk)) {
                $sukses++;
            }
        }
        header("Location: kelola-game.php?bulk_updated=1" . $filter_param);
        exit;
    }
}

// 2. PROSES UBAH STATUS STOK (TOGGLE)
if (isset($_GET['toggle_status']) && isset($_GET['current'])) {
    $id_item = (int)$_GET['toggle_status'];
    $current = mysqli_real_escape_string($koneksi, $_GET['current']);
    $new_status = ($current === 'Tersedia') ? 'Kosong' : 'Tersedia';

    $query_update = "UPDATE game_items SET status = '$new_status' WHERE id_item = $id_item";
    if (mysqli_query($koneksi, $query_update)) {
        header("Location: kelola-game.php?status_updated=1" . $filter_param);
        exit;
    }
}

// 3. PROSES HAPUS ITEM GAME
if (isset($_GET['hapus'])) {
    $id_item = (int)$_GET['hapus'];
    $query_delete = "DELETE FROM game_items WHERE id_item = $id_item";
    if (mysqli_query($koneksi, $query_delete)) {
        header("Location: kelola-game.php?deleted=1" . $filter_param);
        exit;
    } else {
        $msg = "<div class='bg-red-500/10 border border-red-500 text-red-400 text-xs p-3 rounded-xl mb-4'>❌ Gagal menghapus item game.</div>";
    }
}

// Notifikasi Feedback
if (isset($_GET['bulk_updated'])) {
    $msg = "<div class='bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-xl mb-4'>✨ Nama item dan harga berhasil diperbarui seluruhnya!</div>";
}
if (isset($_GET['status_updated'])) {
    $msg = "<div class='bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-xl mb-4'>✨ Status item berhasil diperbarui!</div>";
}
if (isset($_GET['deleted'])) {
    $msg = "<div class='bg-green-500/10 border border-green-500 text-green-400 text-xs p-3 rounded-xl mb-4'>✨ Item game berhasil dihapus!</div>";
}

// 4. AMBIL DATA GAME UNTUK DROPDOWN
$list_game = mysqli_query($koneksi, "SELECT * FROM game ORDER BY nama_game ASC");

// MENANGKAP PARAMETER FILTER GAME
$filter_game = isset($_GET['filter_game']) ? (int)$_GET['filter_game'] : 0;

// 5. AMBIL DATA ITEM GAME + LEFT JOIN KATEGORI GAME
$where_clause = "";
if ($filter_game > 0) {
    $where_clause = "WHERE i.id_game = $filter_game";
}

$query_items = "SELECT i.*, g.nama_game 
                FROM game_items i
                LEFT JOIN game g ON i.id_game = g.id_game
                $where_clause
                ORDER BY g.nama_game ASC, i.harga_jual ASC";
$result_items = mysqli_query($koneksi, $query_items);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Item Game - Market.in</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0a0a0a] text-white">

    <header class="bg-zinc-950 border-b border-zinc-800 px-6 py-4 flex justify-between items-center fixed top-0 left-0 w-full z-50">
        <div class="flex items-center gap-2">
            <span class="text-lg font-bold tracking-wider text-[#f3af22]">MARKET.<span class="text-white">IN</span></span>
            <span class="text-[9px] bg-zinc-800 border border-zinc-700 text-zinc-400 px-2 py-0.5 rounded uppercase font-bold">Admin Panel</span>
        </div>
        <div class="flex items-center gap-4 text-xs">
            <a href="dashboard.php" class="text-zinc-400 hover:text-[#f3af22] transition">← Kembali ke Dashboard</a>
        </div>
    </header>

    <div class="max-w-[1200px] mx-auto px-6 pt-24 pb-12 grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <aside class="space-y-1 lg:col-span-1">
            <div class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest px-3 mb-2">Menu Utama</div>
            <a href="dashboard.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">📊 Dashboard Utama</a>
            <a href="semua-transaksi.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">🧾 Semua Riwayat Transaksi</a>
            <a href="kelola-game.php" class="block bg-zinc-900 text-[#f3af22] border border-zinc-800 px-4 py-2.5 rounded-xl text-xs font-bold">💎 Kelola Item Game</a>
            <a href="kelola-user.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">👥 Kelola Data Member</a>
            <a href="kelola-voucher.php" class="block text-zinc-400 hover:bg-zinc-900/50 hover:text-white px-4 py-2.5 rounded-xl text-xs transition">🎫 Kode Voucher Promo</a>
        </aside>

        <section class="lg:col-span-3 space-y-6">
            <div>
                <h2 class="text-xl font-black text-white">💎 Kelola Varian Item Game</h2>
                <p class="text-xs text-zinc-500 mt-0.5">Atur varian nominal item top-up dan harga jual untuk tiap game.</p>
            </div>

            <?= $msg; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                
                <!-- FORM TAMBAH ITEM -->
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-5 md:col-span-1">
                    <h3 class="text-xs font-bold text-[#f3af22] tracking-wider uppercase mb-4">➕ Tambah Varian Item</h3>
                    <form action="" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">Pilih Game</label>
                            <select name="id_game" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#f3af22] text-white">
                                <option value="">-- Pilih Game --</option>
                                <?php 
                                mysqli_data_seek($list_game, 0);
                                if($list_game): 
                                    while($g = mysqli_fetch_assoc($list_game)): 
                                ?>
                                        <option value="<?= $g['id_game']; ?>"><?= htmlspecialchars($g['nama_game']); ?></option>
                                <?php 
                                    endwhile; 
                                endif; 
                                ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">Nominal Item</label>
                            <input type="text" name="nama_item" placeholder="Contoh: 504 Diamonds" required
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#f3af22] text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">Harga Jual (Rp)</label>
                            <input type="number" name="harga_jual" placeholder="Contoh: 130000" required
                                class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#f3af22] text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-zinc-400 mb-1">Status Awal Stok</label>
                            <select name="status" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#f3af22] text-white">
                                <option value="Tersedia">Tersedia</option>
                                <option value="Kosong">Kosong</option>
                            </select>
                        </div>
                        <button type="submit" name="tambah_item" 
                            class="w-full bg-[#f3af22] hover:bg-[#d6991d] text-black font-black py-2 rounded-xl text-xs uppercase tracking-wider transition">
                            Simpan Item
                        </button>
                    </form>
                </div>

                <!-- TABEL DATA ITEM GAME -->
                <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 md:col-span-2 space-y-4">
                    
                    <!-- INTERFACE PILIHAN FILTER GAME -->
                    <div class="flex flex-wrap items-center gap-1.5 bg-zinc-950/40 p-2 rounded-xl border border-zinc-800/50">
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-wider px-2 block w-full sm:w-auto mb-1 sm:mb-0">Filter:</span>
                        <a href="kelola-game.php" class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition <?= $filter_game === 0 ? 'bg-[#f3af22] text-black font-bold' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white' ?>">Semua</a>
                        <a href="kelola-game.php?filter_game=1" class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition <?= $filter_game === 1 ? 'bg-[#f3af22] text-black font-bold' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white' ?>">Mobile Legends</a>
                        <a href="kelola-game.php?filter_game=3" class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition <?= $filter_game === 3 ? 'bg-[#f3af22] text-black font-bold' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white' ?>">Free Fire</a>
                        <a href="kelola-game.php?filter_game=2" class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition <?= $filter_game === 2 ? 'bg-[#f3af22] text-black font-bold' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white' ?>">PUBG Mobile</a>
                        <a href="kelola-game.php?filter_game=4" class="px-2.5 py-1 rounded-lg text-[11px] font-medium transition <?= $filter_game === 4 ? 'bg-[#f3af22] text-black font-bold' : 'bg-zinc-800 text-zinc-400 hover:bg-zinc-700 hover:text-white' ?>">Roblox</a>
                    </div>

                    <!-- 🛠️ FORM EDIT PENGIKUT TABEL -->
                    <form action="" method="POST">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-zinc-300">
                                <thead>
                                    <tr class="border-b border-zinc-800 text-zinc-500 font-bold">
                                        <th class="pb-3">Game</th>
                                        <th class="pb-3">Varian Item</th>
                                        <th class="pb-3 text-right w-28">Harga (Rp)</th>
                                        <th class="pb-3 text-center">Status</th>
                                        <th class="pb-3 text-center w-28">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-800/50">
                                    <?php if ($result_items && mysqli_num_rows($result_items) > 0): ?>
                                        <?php while($item = mysqli_fetch_assoc($result_items)): ?>
                                            <tr class="hover:bg-zinc-950/20 transition">
                                                <td class="py-3 font-medium text-[#f3af22]"><?= $item['nama_game'] ? htmlspecialchars($item['nama_game']) : '<span class="text-zinc-600">Game Terhapus</span>'; ?></td>
                                                
                                                <!-- INPUT INLINE UNTUK NAMA ITEM -->
                                                <td class="py-2 pr-2">
                                                    <input type="text" name="items[<?= $item['id_item']; ?>][nama_item]" value="<?= htmlspecialchars($item['nama_item']); ?>" required
                                                        class="w-full bg-zinc-950/50 border border-zinc-800 hover:border-zinc-700 focus:border-[#f3af22] focus:bg-zinc-950 rounded-lg px-2 py-1 text-xs text-white transition font-semibold">
                                                </td>
                                                
                                                <!-- INPUT INLINE UNTUK HARGA -->
                                                <td class="py-2">
                                                    <input type="number" name="items[<?= $item['id_item']; ?>][harga_jual]" value="<?= (int)$item['harga_jual']; ?>" required
                                                        class="w-full bg-zinc-950/50 border border-zinc-800 hover:border-zinc-700 focus:border-[#f3af22] focus:bg-zinc-950 rounded-lg px-2 py-1 text-xs text-right text-emerald-400 font-mono font-bold transition">
                                                </td>

                                                <td class="py-3 text-center">
                                                    <a href="?toggle_status=<?= $item['id_item']; ?>&current=<?= $item['status'] . (isset($_GET['filter_game']) ? '&filter_game='.$_GET['filter_game'] : ''); ?>" 
                                                       title="Klik untuk mengubah status stok"
                                                       class="px-2 py-0.5 rounded text-[10px] font-bold cursor-pointer transition hover:opacity-80 <?= $item['status'] == 'Tersedia' ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400' ?>">
                                                        <?= $item['status']; ?> 🔄
                                                    </a>
                                                </td>
                                                <td class="py-3 text-center">
                                                    <div class="flex gap-1 justify-center">
                                                        <a href="?toggle_status=<?= $item['id_item']; ?>&current=<?= $item['status'] . (isset($_GET['filter_game']) ? '&filter_game='.$_GET['filter_game'] : ''); ?>" 
                                                           class="bg-zinc-800 text-zinc-300 border border-zinc-700 px-2 py-1 rounded-lg hover:bg-zinc-700 transition font-bold text-[10px]">
                                                            Ubah Stok
                                                        </a>
                                                        <a href="?hapus=<?= $item['id_item']; ?><?= isset($_GET['filter_game']) ? '&filter_game='.$_GET['filter_game'] : ''; ?>" 
                                                           onclick="return confirm('Hapus varian item ini?')"
                                                           class="bg-red-600/20 text-red-400 border border-red-500/30 px-2 py-1 rounded-lg hover:bg-red-600 hover:text-white transition font-bold text-[10px]">
                                                            Hapus
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-zinc-600">Belum ada item game yang dibuat atau filter tidak mencocokkan data.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- 🛠️ TOMBOL PEMICU SIMPAN PERUBAHAN -->
                        <?php if ($result_items && mysqli_num_rows($result_items) > 0): ?>
                            <div class="pt-4 flex justify-end">
                                <button type="submit" name="update_items" class="bg-emerald-500 hover:bg-emerald-600 text-black font-black px-4 py-2 rounded-xl text-xs uppercase tracking-wider transition shadow-lg shadow-emerald-500/10">
                                    💾 Simpan Perubahan Nama & Harga
                                </button>
                            </div>
                        <?php endif; ?>
                    </form>

                </div>

            </div>
        </section>
    </div>

</body>
</html>