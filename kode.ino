<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit;
}
include '../config/koneksi.php';

// Inisialisasi Angka Statistik Sesuai Gambar Figma
$total_pesanan = 128; 
$total_penjualan = "12.450.000"; 
$stok_aktif = 24; 
$total_pelanggan = 80; 

if (isset($conn)) {
    // Hitung Produk Aktif Asli dari DB jika tabelnya tersedia
    $cek_p = mysqli_query($conn, "SHOW TABLES LIKE 'produk'");
    if ($cek_p && mysqli_num_rows($cek_p) > 0) {
        $q_p = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
        $dt_p = mysqli_fetch_assoc($q_p); $stok_aktif = $dt_p['total'];
    }
    // Hitung Pelanggan Asli dari DB jika tabelnya tersedia
    $cek_u = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if (!$cek_u) $cek_u = mysqli_query($conn, "SHOW TABLES LIKE 'user'");
    if ($cek_u && mysqli_num_rows($cek_u) > 0) {
        $nama_tbl = mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE 'users'")) > 0 ? 'users' : 'user';
        $q_u = mysqli_query($conn, "SELECT COUNT(*) as total FROM $nama_tbl");
        $dt_u = mysqli_fetch_assoc($q_u); $total_pelanggan = $dt_u['total'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Stranger Merch Store</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        
        /* TOP NAVBAR */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 4%; background: #0a0202; border-bottom: 2px solid #E50914;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase; }
        .nav-links a.active { color: #E50914; }
        .user-menu { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;}

        /* LAYOUT UTAMA KIRI & KANAN */
        .main-layout { display: flex; padding: 25px 4%; gap: 25px; align-items: flex-start; }

        /* SIDEBAR (KIRI) */
        .sidebar { width: 240px; background: rgba(10, 2, 2, 0.6); border: 1px solid rgba(229, 9, 20, 0.3); border-radius: 10px; padding: 20px; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; background: #1a1a1a; border: 2px solid #E50914; object-fit: cover; }
        .user-profile h4 { font-size: 0.85rem; font-weight: 600; color: white; }
        .user-profile p { font-size: 0.7rem; color: #888; }
        .status-dot { width: 8px; height: 8px; background: #4CAF50; border-radius: 50%; display: inline-block; margin-right: 4px; }
        
        .menu-heading { font-size: 0.75rem; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; font-weight: 600; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; margin-bottom: 20px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; color: #bbb; text-decoration: none; padding: 10px 15px; font-size: 0.85rem; border-radius: 6px; transition: 0.2s; }
        .sidebar-menu li.active a { background: #E50914; color: white !important; font-weight: 500; box-shadow: 0 4px 10px rgba(229, 9, 20, 0.3); }
        .sidebar-menu a:hover { color: #E50914; background: rgba(229,9,20,0.05); }
        .btn-back-sidebar { display: block; color: #666; text-decoration: none; font-size: 0.8rem; padding-left: 15px; }

        /* PANEL KONTEN (KANAN) */
        .content-area { flex: 1; display: flex; flex-direction: column; gap: 20px; }

        /* BANNER HORIZONTAL MERAH ATAS */
        .hero-banner-wide {
            background: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3)), url('../assets/img/stranger-banner-wide.jpg') no-repeat center center;
            background-size: cover;
            border: 2px solid #E50914;
            border-radius: 12px;
            height: 145px;
            box-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
        }

        /* GRID STATISTIK 4 BARIS */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .stat-card { background: rgba(15, 5, 5, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 6px; padding: 15px; }
        .stat-card p { font-size: 0.65rem; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
        .stat-card h3 { font-size: 1.3rem; font-weight: 700; color: white; }
        .stat-card span { font-size: 0.65rem; color: #666; display: block; margin-top: 2px; }

        /* SUSUNAN DATA DUA PANEL BAWAH */
        .split-container { display: flex; gap: 20px; }
        .left-panel { flex: 1.4; }
        .right-panel { flex: 1; }
        
        .panel-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px; color: #fff; }

        /* TABEL PESANAN MASUK */
        .table-box { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 6px; overflow: hidden; padding: 8px; }
        .order-table { width: 100%; border-collapse: collapse; text-align: left; }
        .order-table th, .order-table td { padding: 10px 12px; font-size: 0.75rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .order-table th { background: rgba(229, 9, 20, 0.1); color: #E50914; font-weight: 600; text-transform: uppercase; }
        
        /* BADGE KATEGORI STATUS */
        .status-badge { padding: 2px 6px; border-radius: 3px; font-size: 0.65rem; font-weight: 600; text-transform: uppercase; display: inline-block; }
        .status-badge.selesai { background: rgba(76, 175, 80, 0.2); color: #4CAF50; }
        .status-badge.dikemas { background: rgba(33, 150, 243, 0.2); color: #2196F3; }
        .status-badge.proses { background: rgba(255, 152, 0, 0.2); color: #FF9800; }
        .status-badge.menunggu { background: rgba(233, 30, 99, 0.2); color: #E91E63; }
        .status-badge.dibatalkan { background: rgba(244, 67, 54, 0.2); color: #F44336; }

        /* GRID KATALOG KATEGORI TERLARIS */
        .products-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .prod-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 6px; padding: 12px; text-align: center; }
        .prod-img-mini-box { width: 100%; height: 85px; background: #150505; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px; overflow: hidden;}
        .prod-img-mini-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .prod-card h4 { font-size: 0.7rem; font-weight: 500; margin-bottom: 6px; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .prod-card p { font-size: 0.75rem; color: #E50914; font-weight: 600; margin-bottom: 6px; }
        .mini-actions { display: flex; justify-content: center; gap: 12px; font-size: 0.7rem; color: #555; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 6px; }
    </style>
</head>
<body>

    <!-- TOP NAVBAR SAMA PERSIS -->
    <nav class="navbar">
        <a href="admin.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="admin.php" class="active">HOME</a></li>
            <li><a href="admin_kategori.php">PRODUK</a></li>
            <li><a href="data_pesanan.php">RIWAYAT PEMESANAN</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-basket-shopping"></i></a></li>
            <li class="user-menu"><i class="fa-regular fa-user"></i> Hi, Admin <i class="fa-solid fa-caret-down"></i></li>
        </ul>
    </nav>

    <!-- CONTAINER MAIN LAYOUT -->
    <div class="main-layout">
        
        <!-- SIDEBAR NAVIGASI UTAMA (KIRI) -->
        <aside class="sidebar">
            <div class="user-profile">
                <img src="../assets/img/avatar_dustin.png" class="user-avatar" alt="Avatar">
                <div>
                    <h4>Admin Hawkins</h4>
                    <p><span class="status-dot"></span>Online</p>
                </div>
            </div>
            
            <div class="menu-heading">Menu</div>
            <ul class="sidebar-menu">
                <!-- DASHBOARD MENYALA AKTIF MERAH -->
                <li class="active"><a href="admin.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="admin_kategori.php"><i class="fa-solid fa-box-open"></i> Kategori</a></li>
                <li><a href="data_pesanan.php"><i class="fa-solid fa-file-invoice-dollar"></i> Pesanan</a></li>
                <li><a href="../auth/logout.php" style="color: #ff4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
            </ul>
            <a href="javascript:history.back()" class="btn-back-sidebar"><i class="fa-solid fa-caret-left"></i> Back</a>
        </aside>

        <!-- AREA PANEL KONTEN KANAN -->
        <main class="content-area">
            
            <!-- BANNER WIDE ATAS -->
