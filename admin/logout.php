<?php
// File: admin/logout.php
session_start();

// 1. Hapus seluruh data session khusus milik Admin saja agar aman
unset($_SESSION['admin_login']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_level']);

// 2. Jika ingin memastikan semua session benar-benar bersih, aktifkan baris di bawah ini:
// session_destroy();

// 3. Melempar kembali ke halaman utama depan (index.php)
header("Location: ../index.php");
exit;
?>