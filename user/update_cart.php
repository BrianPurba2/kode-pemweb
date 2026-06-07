<?php
session_start();

// Pastikan semua data POST yang dibutuhkan tersedia
if (isset($_POST['id_produk']) && isset($_POST['qty'])) {

    // Ambil data dan pastikan bertipe angka bulat (integer) untuk keamanan
    $id = intval($_POST['id_produk']);
    $qty = intval($_POST['qty']);

    // Logika pengondisian jumlah produk
    if ($qty <= 0) {
        // Jika jumlah diubah ke 0 atau kurang, hapus produk dari keranjang
        unset($_SESSION['cart'][$id]);
    } else {
        // Jika jumlah valid (1 atau lebih), perbarui jumlahnya
        $_SESSION['cart'][$id] = $qty;
    }
}

// Kembalikan pengguna ke halaman keranjang belanja
header("Location: cart.php");
exit;
?>