<?php
session_start();
// 1. Proteksi Keamanan: Pastikan yang masuk adalah USER/PEMBELI yang sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// Ambil nama user dari session pendaftaran (Default: Diva jika belum ter-set)
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Diva';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Stranger Merch Store</title>
    <!-- Font Awesome untuk Ikon Menu & Keranjang -->
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        
        /* TOP NAVBAR KHUSUS USER (PERSIS GAMBAR) */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 6%; background: rgba(10, 2, 2, 0.95); border-bottom: 2px solid #E50914;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; letter-spacing: 1px; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; letter-spacing: 0.5px; text-transform: uppercase; transition: 0.2s; }
        .nav-links a:hover, .nav-links a.active { color: #E50914; }
        .user-menu { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;}

        /* CONTAINER CONTENT */
        .container { padding: 30px 6%; display: flex; flex-direction: column; gap: 25px; }

        /* HERO BANNER BESAR GAMBAR POSTER */
        .hero-banner {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), url('../assets/img/stranger-banner-wide.jpg') no-repeat center center;
            background-size: cover; border: 2px solid #E50914; border-radius: 12px; padding: 60px 20px; text-align: center;
            box-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
        }
        .hero-banner .welcome { font-size: 0.85rem; color: #aaa; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 5px; }
        .hero-banner .welcome::before, .hero-banner .welcome::after { content: " -------- "; color: #E50914; }
        .hero-banner h1 { font-family: 'Cinzel Decorative', serif; font-size: 2.5rem; color: #ffffff; letter-spacing: 1px; font-weight: 700; text-shadow: 0 0 15px rgba(229, 9, 20, 0.8); text-transform: uppercase; line-height: 1.2; margin-bottom: 10px; }
        .hero-banner p.desc { font-size: 0.85rem; color: #ccc; max-width: 500px; margin: 0 auto; line-height: 1.5; }

        /* TATA LETAK PERPADUAN BAWAH */
        .split-layout { display: flex; gap: 30px; align-items: flex-start; }

        /* SIDEBAR KATEGORI (KIRI) */
        .kategori-sidebar-box { width: 220px; background: rgba(15, 5, 5, 0.4); border: 2px solid #E50914; border-radius: 12px; padding: 20px; }
        .sidebar-head-title { background: #E50914; color: white; text-align: center; font-size: 0.8rem; font-weight: 700; padding: 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        .sidebar-links-list { list-style: none; display: flex; flex-direction: column; gap: 4px; text-align: center; }
        .sidebar-links-list a { color: #ccc; text-decoration: none; font-size: 0.85rem; display: block; padding: 8px; border-radius: 4px; transition: 0.2s; }
        .sidebar-links-list a:hover { color: #E50914; background: rgba(255,255,255,0.02); }
        .btn-back-link { display: block; text-align: center; color: #666; text-decoration: none; font-size: 0.85rem; margin-top: 25px; transition: 0.2s; }

        /* PANEL KONTEN KANAN */
        .content-display-panel { flex: 1; display: flex; flex-direction: column; gap: 25px; }
        .section-header-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; margin-bottom: 15px; border-left: 3px solid #E50914; padding-left: 10px; }

        /* GRID MINI KATEGORI POPULER */
        .populer-category-grid { display: flex; gap: 15px; }
        .populer-item-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; padding: 15px; width: 110px; text-align: center; }
        .populer-item-card i { font-size: 1.8rem; color: #E50914; display: block; margin-bottom: 6px; }
        .populer-item-card p { font-size: 0.75rem; color: #ccc; font-weight: 500; }

        /* GRID DAFTAR PRODUK KATALOG */
        .product-catalog-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .catalog-item-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 6px; padding: 12px; display: flex; flex-direction: column; }
        .image-render-box { width: 100%; height: 110px; background: #150505; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; overflow: hidden; }
        .image-render-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .image-render-box i { font-size: 2.2rem; color: rgba(255,255,255,0.05); }
        
        .meta-text-box h4 { font-size: 0.75rem; font-weight: 500; color: #fff; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .meta-text-box .price-tag { font-size: 0.75rem; color: #aaa; margin-bottom: 6px; }
        .gold-stars { color: #FF9800; font-size: 0.65rem; margin-bottom: 10px; }
        
        /* FOOTER CARD AKSI */
        .card-footer-buttons { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px; margin-top: auto; }
        .card-footer-buttons i { font-size: 0.8rem; color: #666; cursor: pointer; transition: 0.2s; }
        .card-footer-buttons i:hover { color: #E50914; }
        .card-footer-buttons a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>

    <!-- NAVBAR ATAS VERSI USER -->
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="home.php" class="active">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-basket-shopping"></i></a></li>
            <li class="user-menu"><i class="fa-regular fa-user"></i> Hi, <?= htmlspecialchars($nama_user); ?> <i class="fa-solid fa-caret-down"></i></li>
        </ul>
    </nav>

    <!-- MAIN CONTAINER LAYOUT -->
    <div class="container">
        
        <!-- HERO BANNER POSTER BESAR -->
        <header class="hero-banner">
            <div class="welcome">Welcome To</div>
            <h1>Stranger Merch Store</h1>
            <p class="desc">Temukan merchandise eksklusif Stranger Things dan tunjukkan sisi Hawkins-mu!</p>
        </header>

        <!-- SPLIT GRID BAWAH -->
        <div class="split-layout">
            
            <!-- SIDEBAR NAVIGASI MENU KATEGORI (KIRI) -->
            <aside class="kategori-sidebar-box">
                <div class="sidebar-head-title">Kategori</div>
                <ul class="sidebar-links-list">
                    <li><a href="produk.php?kategori=tshirt">T-Shirt</a></li>
                    <li><a href="produk.php?kategori=hoodie">Hoodie</a></li>
                    <li><a href="produk.php?kategori=mug">Mug</a></li>
                    <li><a href="produk.php?kategori=topi">Topi</a></li>
                </ul>
                <a href="../auth/logout.php" class="btn-back-link" style="color: #ff4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </aside>

            <!-- PANEL KATALOG KANAN -->
            <main class="content-display-panel">
                
                <!-- 1. SEKSI KATEGORI POPULER MINI -->
                <div>
                    <h3 class="section-header-title">Kategori Populer</h3>
                    <div class="populer-category-grid">
                        <div class="populer-item-card">
                            <i class="fa-solid fa-shirt"></i>
                            <p>Hoodie</p>
                        </div>
                        <div class="populer-item-card">
                            <i class="fa-solid fa-hat-cowboy"></i>
                            <p>Topi</p>
                        </div>
                    </div>
                </div>

                <!-- 2. SEKSI KATALOG PRODUK TERLARIS -->
                <div>
                    <h3 class="section-header-title">Kategori Terlaris</h3>
                    <div class="product-catalog-grid">
                        
                        <?php
                        // Menarik data katalog produk jualan asli langsung dari database Anda secara otomatis
                        $q_ambil = mysqli_query($conn, "SELECT * FROM produk LIMIT 4");
                        if ($q_ambil && mysqli_num_rows($q_ambil) > 0):
                            while ($p = mysqli_fetch_assoc($q_ambil)):
                                $img_name = !empty($p['foto']) ? $p['foto'] : ($p['gambar_produk'] ?? '');
                        ?>
                            <!-- Item Card Dinamis Database -->
