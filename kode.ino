<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_produk = intval($_GET['id']);
} else {
    header("Location: produk.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Jika produk sudah ada di keranjang, tambah jumlahnya. Jika belum, set = 1
if (isset($_SESSION['cart'][$id_produk])) {
    $_SESSION['cart'][$id_produk]++;
} else {
    $_SESSION['cart'][$id_produk] = 1;
}

// Setelah sukses mencatat, langsung lempar ke halaman keranjang
header("Location: cart.php");
exit();
?>
