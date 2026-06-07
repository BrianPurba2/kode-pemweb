<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Diva';

// Menghapus item tertentu dari keranjang jika ada perintah 'hapus'
if (isset($_GET['hapus'])) {
    $id_hapus = intval($_GET['hapus']);
    unset($_SESSION['cart'][$id_hapus]);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Stranger Merch Store</title>
    <!-- Font Awesome untuk Icon Trash & Keranjang -->
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        
        /* NAVBAR STYLE */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 6%; background: rgba(10, 2, 2, 0.95); border-bottom: 2px solid #E50914;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; letter-spacing: 1px; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .nav-links a.active { color: #E50914; font-weight: 600; }
        .user-menu { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;}

        /* LAYOUT UTAMA (2 KOLOM BERDAMPINGAN) */
        .container { padding: 40px 6%; }
        .cart-wrapper {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 35px;
            margin-top: 10px;
        }
        
        .column-title {
            font-size: 1.2rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; color: #fff;
        }

        /* BOX KIRI: DAFTAR KERANJANG BELANJA */
        .cart-section {
            border: 2px solid #E50914; border-radius: 12px; padding: 20px; background: rgba(10,2,2,0.6);
        }
        .cart-item {
            display: flex; align-items: center; gap: 20px;
            padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .cart-item:last-child { border-bottom: none; }
        
        .item-img {
            width: 75px; height: 75px; background: #150505; border-radius: 8px; object-fit: contain; padding: 5px; border: 1px solid rgba(255,255,255,0.05);
        }
        .item-details { flex: 1; }
        .item-name { font-size: 0.9rem; font-weight: 600; margin-bottom: 2px; color: #fff; }
        .item-size { font-size: 0.75rem; color: #888; margin-bottom: 8px; }
        
        /* TIMBOL MINUS PLUS INPUT QUANTITY */
        .qty-action-container { display: flex; align-items: center; justify-content: space-between; max-width: 140px; }
        .qty-control {
            display: flex; align-items: center; background: #110404; 
            border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; overflow: hidden;
        }
        .qty-btn {
            background: none; border: none; color: white; width: 28px; height: 28px; 
            font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s;
        }
        .qty-btn:hover { background: rgba(229, 9, 20, 0.15); }
        .qty-val {
            background: none; border: none; color: white; width: 35px; text-align: center; font-size: 0.85rem; font-weight: 600;
        }
        .qty-val::-webkit-inner-spin-button, .qty-val::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }

        /* ICON SAMPAH ORANJE MERAH */
        .btn-delete-link { color: #FF5722; font-size: 1.1rem; cursor: pointer; transition: 0.2s; padding: 5px; text-decoration: none; }
        .btn-delete-link:hover { color: #E50914; transform: scale(1.1); }

        /* BOX KANAN: RINGKASAN BELANJA */
        .summary-section {
            border: 2px solid rgba(255,255,255,0.15); border-radius: 12px; padding: 25px; background: rgba(10,2,2,0.6); height: max-content;
        }
        .summary-row {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 0.85rem; color: #aaa;
        }
        .summary-row strong { color: #fff; }
        
        .form-label { font-size: 0.8rem; color: #ccc; display: block; margin-bottom: 5px; }
        .summary-input, .summary-select {
            width: 100%; padding: 10px; background: #110404; border: 1px solid rgba(255,255,255,0.15); 
            border-radius: 6px; color: white; font-size: 0.85rem; margin-top: 2px; outline: none; transition: 0.2s;
        }
        .summary-input:focus, .summary-select:focus { border-color: #E50914; }
        
        .shipping-box {
            display: flex; gap: 10px; align-items: flex-end; width: 100%;
        }
        .btn-cek {
            background: #FF5722; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; transition: 0.2s;
        }
        .btn-cek:hover { background: #e64a19; }
        
        .total-box {
            margin-top: 25px; padding-top: 20px; border-top: 1px dashed rgba(255,255,255,0.15);
        }
        .total-title { font-size: 1.1rem; font-weight: 600; color: #fff; }
        .total-amount { font-size: 1.25rem; color: #FF5722; font-weight: 700; }

        .btn-checkout {
            display: block; width: 100%; padding: 12px; background: #E50914; color: white; 
            text-align: center; text-decoration: none; font-weight: 700; border-radius: 6px; 
            margin-top: 20px; letter-spacing: 0.5px; font-size: 0.9rem; text-transform: uppercase; box-shadow: 0 4px 10px rgba(229, 9, 20, 0.2); transition: 0.2s;
        }
        .btn-checkout:hover { background: #b8070f; }
        
        .secure-text {
            display: flex; align-items: center; justify-content: center; gap: 6px; 
            font-size: 0.75rem; color: #888; margin-top: 12px; text-align: center;
        }
        .secure-text i { color: #4CAF50; }

        /* BUTTON NAVIGASI BAWAH KIRI */
        .bottom-links { display: flex; gap: 20px; margin-top: 20px; font-size: 0.8rem; }
        .bottom-links a { color: #888; text-decoration: none; display: flex; align-items: center; gap: 5px; transition: 0.2s; }
        .bottom-links a:hover { color: #fff; text-decoration: underline; }

        .empty-message { text-align: center; padding: 70px 0; color: #888; width: 100%; grid-column: span 2; }
        .empty-message a { color: #E50914; text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>

    <!-- NAVBAR ATAS -->
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="cart.php" class="active"><i class="fa-solid fa-basket-shopping" style="color:#E50914;"></i></a></li>
            <li class="user-menu"><i class="fa-regular fa-user"></i> Hi, <?= htmlspecialchars($nama_user); ?> <i class="fa-solid fa-caret-down"></i></li>
        </ul>
    </nav>

    <!-- CONTAINER UTAMA -->
    <main class="container">
        
        <div class="cart-wrapper">
            
            <!-- KOLOM KIRI: DAFTAR KERANJANG BELANJA -->
            <div>
                <h2 class="column-title">Keranjang Belanja</h2>
                <div class="cart-section">
                    <?php 
                    $grand_total = 0;
                    $total_item = 0;
                    
                    if (!empty($_SESSION['cart'])):
                        foreach ($_SESSION['cart'] as $id_produk => $jumlah): 
                            $q_prod = "SELECT * FROM produk WHERE id_produk='$id_produk'";
                            $res_prod = mysqli_query($conn, $q_prod);
                            $prod = mysqli_fetch_assoc($res_prod);
                            
                            if ($prod) {
                                $subtotal = $prod['harga'] * $jumlah;
                                $grand_total += $subtotal;
                                $total_item += $jumlah;
                            } else { continue; }
                    ?>
                    <!-- ITEM BARANG CARD -->
                    <div class="cart-item">
                        <img src="../assets/img/produk/<?= $prod['foto'] ?? ($prod['gambar_produk'] ?? 'default.png'); ?>" class="item-img" alt="">
                        
                        <div class="item-details">
                            <div class="item-name"><?= htmlspecialchars($prod['nama_produk']); ?></div>
                            <div class="item-size">Ukuran: M</div>
                            
                            <!-- Kontrol Jumlah Kuantitas Plus Minus Otomatis -->
                            <div class="qty-action-container">
                                <form action="update_cart.php" method="POST" id="form-<?= $id_produk; ?>">
                                    <input type="hidden" name="id_produk" value="<?= $id_produk; ?>">
