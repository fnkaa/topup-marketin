<?php
include 'koneksi.php';

// Membaca data JSON otomatis yang dikirim oleh server Midtrans
$json_result = file_get_contents('php://input');
$notif = json_decode($json_result, true);

if ($notif) {
    $order_id           = mysqli_real_escape_string($koneksi, $notif['order_id']);
    $transaction_status = $notif['transaction_status'];
    $fraud_status       = isset($notif['fraud_status']) ? $notif['fraud_status'] : '';

    // Menentukan status akhir berdasarkan feedback Midtrans
    $status_db = 'Pending';

    if ($transaction_status == 'capture') {
        if ($fraud_status == 'challenge') {
            $status_db = 'Pending';
        } else if ($fraud_status == 'accept') {
            $status_db = 'Sukses';
        }
    } else if ($transaction_status == 'settlement') {
        $status_db = 'Sukses';
    } else if ($transaction_status == 'cancel' || $transaction_status == 'deny' || $transaction_status == 'expire') {
        $status_db = 'Expired';
    } else if ($transaction_status == 'pending') {
        $status_db = 'Pending';
    }

    // Eksekusi update otomatis ke tabel transaksi SQL Anda
    $query_update = "UPDATE transaksi SET status_pembayaran = '$status_db' WHERE order_id = '$order_id'";
    $eksekusi = mysqli_query($koneksi, $query_update);
    
    // Simpan catatan log ke file teks lokal untuk debugging jika terjadi error SQL
    if (!$eksekusi) {
        file_put_contents('midtrans_error_log.txt', date('Y-m-d H:i:s') . " - SQL Error: " . mysqli_error($koneksi) . "\n", FILE_APPEND);
    } else {
        file_put_contents('midtrans_success_log.txt', date('Y-m-d H:i:s') . " - Order $order_id Berhasil diupdate jadi $status_db\n", FILE_APPEND);
    }
    
    echo "OK"; 
}
?>