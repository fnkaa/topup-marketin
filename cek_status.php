<?php
include 'koneksi.php';

header('Content-Type: application/json');

if (isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($koneksi, $_GET['order_id']);
    
    // JIKA ADA DETEKSI TRIGER BYPASS JAVASCRIPT, LANGSUNG UPDATE JADI SUKSES
    if (isset($_GET['set_success']) && $_GET['set_success'] == 'true') {
        mysqli_query($koneksi, "UPDATE transaksi SET status_pembayaran = 'Sukses' WHERE order_id = '$order_id'");
        echo json_encode(['status' => 'Sukses']);
        exit;
    }
    
    // Ambil status pembayaran terbaru dari database
    $query = "SELECT status_pembayaran FROM transaksi WHERE order_id = '$order_id'";
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        echo json_encode(['status' => $data['status_pembayaran']]);
        exit;
    }
}

echo json_encode(['status' => 'Not Found']);
?>