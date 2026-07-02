<?php
session_start();
include 'koneksi.php';

// PROTEKSI UTAMA: Jika belum login, dialihkan ke halaman login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo "<script>alert('Anda harus login terlebih dahulu sebelum melakukan transaksi!'); window.location='login.php';</script>";
    exit;
}

// =========================================================================
// KONFIGURASI ACCESS KEYS MIDTRANS
// =========================================================================
$server_key    = "Mid-server-6lBISqQEJ_igf0-MvxPImxQ_";
$client_key    = "Mid-client-8Bz8TE_T17eTEmbf";
$is_production = false; 

// =========================================================================
// 1. PROSES POST: KETIKA MENERIMA DATA DARI FORM TRANSAKSI GAME
// =========================================================================
if (isset($_POST['submit_topup'])) {
    $id_user      = $_SESSION['user_id'];
    $order_id     = mysqli_real_escape_string($koneksi, $_POST['order_id']);
    $user_id      = mysqli_real_escape_string($koneksi, $_POST['user_id']);
    $zone_id      = mysqli_real_escape_string($koneksi, $_POST['zone_id']);
    
    $target_id    = ($zone_id != '0' && !empty($zone_id)) ? $user_id . " (" . $zone_id . ")" : $user_id;
    $nominal_item = mysqli_real_escape_string($koneksi, $_POST['nominal']);
    $kode_metode  = mysqli_real_escape_string($koneksi, $_POST['payment']);
    $promo_code   = mysqli_real_escape_string($koneksi, $_POST['kode_promo']);
    $id_game      = isset($_POST['id_game']) ? (int)$_POST['id_game'] : 1; 

    // Bersihkan format rupiah
    $harga_mentah = isset($_POST['total']) ? $_POST['total'] : '0';
    $clean_harga  = preg_replace('/[^0-9]/', '', $harga_mentah);
    $total_pembayaran = (float) (!empty($clean_harga) ? $clean_harga : 0);

    // Ambil biaya admin
    $query_method = "SELECT id_metode, biaya_admin FROM payment_methods WHERE kode_metode = '$kode_metode'";
    $res_method   = mysqli_query($koneksi, $query_method);
    
    if (mysqli_num_rows($res_method) > 0) {
        $row_method = mysqli_fetch_assoc($res_method);
        $id_metode   = $row_method['id_metode'];
        $biaya_admin = (float) $row_method['biaya_admin'];
        $total_pembayaran += $biaya_admin;
    } else {
        echo "<script>alert('Metode pembayaran tidak valid!'); window.location='index.php';</script>";
        exit;
    }

    // Fitur voucher diskon
    if (!empty($promo_code)) {
        $query_vouch = "SELECT * FROM vouchers WHERE kode_voucher = '$promo_code' AND status = 'Aktif' AND kuota > 0 AND expired_at > NOW()";
        $res_vouch   = mysqli_query($koneksi, $query_vouch);
        
        if (mysqli_num_rows($res_vouch) > 0) {
            $row_vouch = mysqli_fetch_assoc($res_vouch);
            if (($total_pembayaran - $biaya_admin) >= $row_vouch['minimal_pembelian']) {
                if ($row_vouch['tipe_potongan'] == 'Nominal') {
                    $total_pembayaran -= $row_vouch['jumlah_potongan'];
                }
                mysqli_query($koneksi, "UPDATE vouchers SET kuota = kuota - 1 WHERE id_voucher = " . $row_vouch['id_voucher']);
            }
        }
    }

    $batas_waktu = date('Y-m-d H:i:s', strtotime('+2 hours'));

    // REQUEST TOKEN SNAP KE MIDTRANS
    $midtrans_url = $is_production ? "https://app.midtrans.com/snap/v1/transactions" : "https://app.sandbox.midtrans.com/snap/v1/transactions";
    
    $payload = [
        'transaction_details' => [
            'order_id'     => $order_id,
            'gross_amount' => (int)$total_pembayaran,
        ],
        'customer_details' => [
            'first_name' => $_SESSION['username'],
            'email'      => $_POST['email']
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $midtrans_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Basic ' . base64_encode($server_key . ':')
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    $response_data = json_decode($response, true);
    
    $snap_token = isset($response_data['token']) ? $response_data['token'] : '';

    // MASUKKAN DATA BESERTA SNAP TOKEN KE DATABASE SQL
    $query_insert = "INSERT INTO transaksi (order_id, id_user, id_game, target_id, nominal_item, total_pembayaran, id_metode, status_pembayaran, batas_waktu, snap_token, created_at) 
                     VALUES ('$order_id', '$id_user', '$id_game', '$target_id', '$nominal_item', '$total_pembayaran', '$id_metode', 'Pending', '$batas_waktu', '$snap_token', NOW())";
    
    if (!mysqli_query($koneksi, $query_insert)) {
        echo "Gagal menyimpan transaksi: " . mysqli_error($koneksi);
        exit;
    }
    
    header("Location: pembayaran.php?order_id=" . urlencode($order_id));
    exit;
}

// =========================================================================
// 2. PROSES GET: MENGAMBIL DATA UNTUK DITAMPILKAN DI INVOICE
// =========================================================================
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}

$order_id = mysqli_real_escape_string($koneksi, $_GET['order_id']);

$query = "SELECT t.*, g.nama_game, p.nama_metode FROM transaksi t
          JOIN game g ON t.id_game = g.id_game
          JOIN payment_methods p ON t.id_metode = p.id_metode
          WHERE t.order_id = '$order_id'";

$result = mysqli_query($koneksi, $query);
if (mysqli_num_rows($result) === 0) {
    echo "<script>alert('Invoice tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}
$transaksi = mysqli_fetch_assoc($result);

// KUNCI UTAMA: Mengambil token yang sudah ada di DB agar tidak request ulang ke Midtrans
$snap_token = $transaksi['snap_token']; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran <?= htmlspecialchars($transaksi['order_id']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="<?= $is_production ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' ?>" data-client-key="<?= $client_key; ?>"></script>
</head>
<body class="bg-[#0a0a0a] text-white pt-12 pb-24 px-4 font-sans">

    <div class="max-w-[850px] mx-auto bg-[#0f1113] rounded-2xl border border-zinc-800 shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-5">
        
        <div class="md:col-span-2 bg-zinc-900/50 p-6 border-b md:border-b-0 md:border-r border-zinc-800 flex flex-col justify-between">
            <div>
                <a href="history.php" class="text-xs text-[#f3af22] hover:underline">← Lihat Riwayat Transaksi</a>
                <div class="mt-6 text-center md:text-left">
                    <span class="text-[10px] border px-2.5 py-1 rounded-full font-bold uppercase tracking-wider <?= $transaksi['status_pembayaran'] == 'Sukses' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-yellow-500/10 text-gold border-yellow-500/20' ?>">
                        <?= $transaksi['status_pembayaran'] == 'Sukses' ? '✅ SUKSES' : '⚠️ PENDING' ?>
                    </span>
                    <h2 class="text-xs text-zinc-500 mt-4 uppercase font-bold tracking-wider">Total Tagihan Belanja</h2>
                    <p class="text-2xl font-black text-[#f3af22] mt-1">Rp <?= number_format($transaksi['total_pembayaran'], 0, ',', '.'); ?></p>
                    <p class="text-[11px] text-zinc-400 mt-2">Kode Order: <span class="font-mono text-white"><?= htmlspecialchars($transaksi['order_id']); ?></span></p>
                </div>
            </div>
            <div class="mt-8 pt-6 border-t border-zinc-800 text-xs text-zinc-500 space-y-1">
                <p>Item Game: <span class="text-zinc-300 font-semibold"><?= htmlspecialchars($transaksi['nominal_item']); ?></span></p>
                <p>Kategori: <span class="text-zinc-300 font-semibold"><?= htmlspecialchars($transaksi['nama_game']); ?></span></p>
                <p>Target ID Akun: <span class="text-zinc-300 font-semibold"><?= htmlspecialchars($transaksi['target_id']); ?></span></p>
            </div>
        </div>

        <div class="md:col-span-3 p-8 flex flex-col justify-center space-y-6">
            <?php if ($transaksi['status_pembayaran'] == 'Sukses') : ?>
                <div class="text-center py-12 space-y-4">
                    <div class="text-6xl">🎉</div>
                    <h2 class="text-2xl font-black text-green-400">PEMBAYARAN BERHASIL!</h2>
                    <p class="text-sm text-zinc-400 max-w-sm mx-auto">
                        Sistem kami berhasil memverifikasi transfer dana masuk Anda. Item <span class="text-white font-bold"><?= htmlspecialchars($transaksi['nominal_item']); ?></span> sukses dikirimkan ke akun game Anda.
                    </p>
                    <a href="index.php" class="mt-4 inline-block bg-zinc-800 text-white font-bold px-6 py-2.5 rounded-xl text-xs hover:bg-zinc-700 transition">Kembali ke Beranda</a>
                </div>
            <?php else : ?>
                <div class="text-center py-12 space-y-6">
                    <div class="text-5xl">💳</div>
                    <div>
                        <h3 class="text-lg font-bold text-white">Selesaikan Pembayaran</h3>
                        <p class="text-xs text-zinc-400 mt-1">Tekan tombol di bawah untuk menampilkan gerbang pembayaran aman resmi Midtrans.</p>
                    </div>
                    
                    <button id="pay-button" class="w-full bg-[#f3af22] hover:bg-[#d6991d] text-black font-black py-4 rounded-xl uppercase tracking-wider text-sm transition shadow-lg">
                        Bayar Sekarang via Midtrans
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        <?php if ($transaksi['status_pembayaran'] == 'Pending') : ?>
        // AJAX POLING: Cek status berkala ke database
        var checkInterval = setInterval(function() {
            fetch('cek_status.php?order_id=' + encodeURIComponent("<?= $transaksi['order_id']; ?>"))
                .then(response => response.json())
                .then(data => { 
                    if (data.status === 'Sukses') { 
                        clearInterval(checkInterval);
                        window.location.reload();
                    } 
                }).catch(err => console.log("Poling..."));
        }, 3000);

        // Eksekusi Pemicu Pop-Up Snap Payment (Menggunakan token tersimpan)
        const snapToken = '<?= $snap_token; ?>';
        document.getElementById('pay-button').onclick = function(){
            if (snapToken.trim() === "") {
                alert("❌ Token Snap Kosong! Pembuatan invoice pembayaran gagal.");
                return;
            }
            
            snap.pay(snapToken, {
                onSuccess: function(result){
                    fetch('cek_status.php?order_id=' + encodeURIComponent("<?= $transaksi['order_id']; ?>") + '&set_success=true')
                        .then(() => { window.location.href = "pembayaran.php?order_id=" + encodeURIComponent("<?= $transaksi['order_id']; ?>"); });
                },
                onPending: function(result){ window.location.href = "pembayaran.php?order_id=" + encodeURIComponent("<?= $transaksi['order_id']; ?>"); },
                onError: function(result){ alert("Terjadi kegagalan pemrosesan transaksi."); window.location.reload(); }
            });
        };
        <?php endif; ?>
    </script>
</body>
</html>