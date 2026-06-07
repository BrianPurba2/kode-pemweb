<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil ID produk yang diklik pembeli
$id_produk = $_GET['id'];

// Jika keranjang belum ada di memori, buat tempat kosong baru
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Jika produk sudah pernah dimasukkan, tambahkan jumlahnya (+1)
if (isset($_SESSION['cart'][$id_produk])) {
    $_SESSION['cart'][$id_produk]++;
} else {
    // Jika baru pertama kali dimasukkan, set jumlahnya = 1
    $_SESSION['cart'][$id_produk] = 1;
}

// Setelah dicatat, otomatis lempar pembeli ke halaman daftar keranjang belanja
header("Location: cart.php");
exit;
?>