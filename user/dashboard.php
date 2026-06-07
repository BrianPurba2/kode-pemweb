<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$id_user = $_SESSION['id_user'];

// Menggunakan data dummy profil agar tidak crash dengan database yang belum ada tabelnya
$user = [
    'nama' => 'Diva',
    'email' => 'diva@strangerthings.com',
    'foto' => 'avatar_dustin.png'
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Stranger Merch Store</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #0a0202;
            color: #ffffff;
            min-height: 100vh;
        }

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 4%;
            background: #0a0202;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .navbar .logo {
            font-family: 'Cinzel Decorative', serif;
            color: #E50914;
            font-size: 1.3rem;
            text-decoration: none;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 20px;
            align-items: center;
        }

        .nav-links a {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .nav-links a.active {
            color: #E50914;
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.05);
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
        }

        /* MAIN LAYOUT SPLIT */
        .main-layout {
            display: flex;
            padding: 30px 4%;
            gap: 30px;
        }

        /* SIDEBAR (KIRI) */
        .sidebar {
            width: 260px;
            background: rgba(15, 5, 5, 0.6);
            border-radius: 8px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            height: max-content;
        }

        .user-profile {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 65px;
            height: 65px;
            border-radius: 50%;
            background: #1a1a1a;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid #E50914;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .user-profile h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-top: 5px;
        }

        .user-profile p {
            font-size: 0.75rem;
            color: #888;
            margin-bottom: 5px;
        }

        .role-badge {
            background: rgba(229, 9, 20, 0.2);
            color: #E50914;
            font-size: 0.7rem;
            padding: 2px 10px;
            border-radius: 10px;
            font-weight: 600;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #bbb;
            text-decoration: none;
            padding: 10px 15px;
            font-size: 0.9rem;
            border-radius: 6px;
            transition: 0.2s;
            text-align: left;
        }

        .sidebar-menu a:hover,
        .sidebar-menu li.active a {
            background: #E50914;
            color: white;
            font-weight: 500;
        }

        /* CONTENT AREA (KANAN) */
        .content-area {
            flex: 1;
        }

        .page-header-title {
            font-size: 1.4rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .breadcrumb {
            font-size: 0.8rem;
            color: #888;
            margin-bottom: 25px;
        }

        .breadcrumb a {
            color: #888;
            text-decoration: none;
        }

        /* TABS FILTER STATUS */
        .status-tabs {
            display: flex;
            gap: 10px;
            list-style: none;
            margin-bottom: 25px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .tab-item {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 0.8rem;
            cursor: pointer;
            color: #ccc;
            white-space: nowrap;
        }

        .tab-item.active {
            background: #E50914;
            border-color: #E50914;
            color: white;
            font-weight: 500;
        }

        /* ORDER CARD LIST */
        .order-card {
            background: rgba(15, 5, 5, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            text-align: left;
        }

        .order-meta {
            width: 220px;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding-right: 20px;
        }

        .order-id {
            font-size: 0.75rem;
            color: #888;
        }

        .order-id strong {
            color: white;
            font-size: 0.9rem;
            display: block;
            margin-top: 2px;
        }

        .meta-group {
            margin-top: 12px;
            font-size: 0.8rem;
            color: #aaa;
        }

        .meta-group span {
            display: block;
            color: white;
            font-weight: 500;
            margin-top: 1px;
        }

        .btn-detail {
            display: inline-block;
            margin-top: 15px;
            color: #E50914;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-detail:hover {
            text-decoration: underline;
        }

        /* ITEM PRODUCTS INSIDE ORDER */
        .order-items {
            flex: 1;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-content: center;
        }

        .prod-snap {
            text-align: center;
            width: 85px;
        }

        .prod-snap-img {
            width: 60px;
            height: 60px;
            background: #1a1a1a;
            border-radius: 6px;
            object-fit: cover;
            margin-bottom: 6px;
        }

        .prod-snap-name {
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: #fff;
        }

        .prod-snap-meta {
            font-size: 0.65rem;
            color: #888;
        }

        /* ORDER STATUS SIDE */
        .order-status-side {
            width: 180px;
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-end;
        }

        .status-row {
            font-size: 0.75rem;
            color: #888;
            margin-bottom: 8px;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .status-badge.menunggu {
            background: rgba(255, 152, 0, 0.2);
            color: #FF9800;
        }

        .status-badge.dikemas {
            background: rgba(33, 150, 243, 0.2);
            color: #2196F3;
        }

        .btn-cancel {
            background: none;
            border: 1px solid #ff4444;
            color: #ff4444;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 0.75rem;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.2s;
        }

        .btn-cancel:hover {
            background: #ff4444;
            color: white;
        }
        .navbar {
             display: flex;
             justify-content: space-between;
             align-items: center;
             padding: 20px 6%;
             background: #0a0202;

             /* INI KUNCI GARIS MERAH PANJANG DI BAWAH NAVBAR */
             border-bottom: 2px solid #E50914; 
        }
        .page-title {
             font-size: 1.5rem;
             font-weight: 700;
             letter-spacing: 1px;
             text-transform: uppercase;
             margin-bottom: 30px;
             text-align: left; /* Memastikan teks rata kiri seperti di keranjang */

             /* INI KUNCI GARIS MERAH VERTIKALNYA */
             border-left: 4px solid #E50914; 
             padding-left: 12px; 
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER THINGS</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="cart.php">KERANJANG</a></li>
            <li><a href="kategori.php">KATEGORI</a></li>
            <li><a href="riwayat.php" class="active">RIWAYAT PESANAN</a></li>
            <li><a href="tentang.php">TENTANG KAMI</a></li>
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
            <li class="user-menu"><i class="fa-regular fa-user"></i> Hi, <?= $user['nama']; ?> <i
                    class="fa-solid fa-caret-down"></i></li>
        </ul>
    </nav>

    <!-- MAIN SPLIT LAYOUT -->
    <div class="main-layout">

        <!-- SIDEBAR (KIRI) -->
        <aside class="sidebar">
            <div class="user-profile">
                <img src="../assets/img/avatar_dustin.png" class="user-avatar" alt="Avatar">
                <h4><?= $user['nama']; ?></h4>
                <p><?= $user['email']; ?></p>
                <span class="role-badge">Customer</span>
            </div>
            <ul class="sidebar-menu">
                <li class="active"><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="profil.php"><i class="fa-regular fa-user"></i> Profil Saya</a></li>
                <li><a href="riwayat.php"><i class="fa-solid fa-file-invoice"></i> Riwayat Pesanan</a></li>
                <li><a href="cart.php"><i class="fa-solid fa-basket-shopping"></i> Keranjang Saya</a></li>
                <li><a href="wishlist.php"><i class="fa-regular fa-heart"></i> Wishlist</a></li>
                <li><a href="alamat.php"><i class="fa-solid fa-location-dot"></i> Alamat Pengiriman</a></li>
                <li><a href="password.php"><i class="fa-solid fa-lock"></i> Ubah Password</a></li>
                <li style="margin-top: 20px;"><a href="../auth/logout.php" style="color: #ff4444;"><i
                            class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- CONTENT AREA (KANAN) -->
        <main class="content-area">
            <h2 class="page-title">Riwayat Pesanan</h2>
            <div class="breadcrumb"><a href="home.php">Home</a> &gt; Riwayat Pesanan</div>

            <!-- TAB STATUS FILTER -->
            <ul class="status-tabs">
                <li class="tab-item active">Semua Pesanan</li>
                <li class="tab-item">Belum Bayar</li>
                <li class="tab-item">Dikemas</li>
                <li class="tab-item">Dikirim</li>
                <li class="tab-item">Selesai</li>
            </ul>

            <!-- CONTOH DATA VISUAL AMAN (ANTI ERROR DATABASE) -->
            <div class="order-card">
                <div class="order-meta">
                    <div class="order-id">Order ID <strong>#ORD-00123</strong></div>
                    <div class="meta-group">Tanggal Pemesanan <span>24 Mei 2026, 14:30</span></div>
                    <div class="meta-group">Total Pembayaran <span style="color:#E50914; font-weight:600;">Rp
                            567.000</span></div>
                    <a href="detail_pesanan.php?id=123" class="btn-detail">Lihat Detail</a>
                </div>

                <div class="order-items">
                    <div class="prod-snap">
                        <div
                            style="width:60px; height:60px; background:#222; border-radius:6px; margin:0 auto 6px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-shirt" style="color:#E50914;"></i></div>
                        <div class="prod-snap-name">T-Shirt Hellfire</div>
                        <div class="prod-snap-meta">(M) x 1</div>