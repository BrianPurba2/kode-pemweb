<?php
session_start();
// Proteksi Halaman: Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// Ambil data session nama pengguna
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Diva';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Stranger Merch Store</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        
        /* TOP NAVBAR */
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

        /* CONTAINER UTAMA */
        .container { padding: 30px 6%; display: flex; flex-direction: column; gap: 25px; }

        /* HERO BANNER JALANAN HAWKINS (PERSIS FIGMA) */
        .hero-banner {
            position: relative;
            background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.4)), url('../assets/img/stranger-banner-wide.jpg') no-repeat center center;
            background-size: cover;
            border: 2px solid #E50914;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 0 20px rgba(229, 9, 20, 0.4);
        }
        .hero-banner .welcome { font-size: 0.85rem; color: #aaa; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 5px; }
        .hero-banner .welcome::before, .hero-banner .welcome::after { content: " -------- "; color: #E50914; }
        .hero-banner h1 { font-family: 'Cinzel Decorative', serif; font-size: 2.5rem; color: #ffffff; letter-spacing: 1px; font-weight: 700; text-shadow: 0 0 15px rgba(229, 9, 20, 0.8); text-transform: uppercase; line-height: 1.2; margin-bottom: 10px; }
        .hero-banner p.desc { font-size: 0.85rem; color: #ccc; max-width: 500px; margin: 0 auto; line-height: 1.5; }

        /* TATA LETAK SPLIT BAWAH (MENU KIRI vs KONTEN KANAN) */
        .split-layout { display: flex; gap: 30px; align-items: flex-start; }

        /* SIDEBAR NAV KATEGORI (KIRI) */
        .kategori-box { width: 220px; background: rgba(15, 5, 5, 0.4); border: 2px solid #E50914; border-radius: 12px; padding: 20px; }
        .kat-title { background: #E50914; color: white; text-align: center; font-size: 0.8rem; font-weight: 700; padding: 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        .kat-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; text-align: center; }
        .kat-menu a { color: #ccc; text-decoration: none; font-size: 0.85rem; display: block; padding: 8px; border-radius: 4px; transition: 0.2s; }
        .kat-menu a:hover { color: #E50914; background: rgba(255,255,255,0.02); }
        .btn-back { display: block; text-align: center; color: #666; text-decoration: none; font-size: 0.85rem; margin-top: 25px; transition: 0.2s; }
        .btn-back:hover { color: #fff; }

        /* AREA DISPLAY PRODUK (KANAN) */
        .content-panel { flex: 1; display: flex; flex-direction: column; gap: 25px; }
        .section-title { font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #fff; margin-bottom: 15px; border-left: 3px solid #E50914; padding-left: 10px; }

        /* GRID KATEGORI POPULER MINI */
        .populer-grid { display: flex; gap: 15px; }
        .populer-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; padding: 15px; width: 110px; text-align: center; }
        .populer-card i { font-size: 1.8rem; color: #E50914; display: block; margin-bottom: 6px; }
        .populer-card p { font-size: 0.75rem; color: #ccc; font-weight: 500; }

        /* GRID KATALOG PRODUK TERLARIS */
        .catalog-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .product-item-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 6px; padding: 12px; display: flex; flex-direction: column; }
        .prod-img-box { width: 100%; height: 110px; background: #150505; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; overflow: hidden; }
        .prod-img-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .prod-img-box i { font-size: 2.2rem; color: rgba(255,255,255,0.05); }
        
        .prod-info-meta h4 { font-size: 0.7rem; font-weight: 500; color: #fff; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .prod-info-meta .price { font-size: 0.75rem; color: #aaa; margin-bottom: 6px; }
        .rating-stars { color: #FF9800; font-size: 0.65rem; margin-bottom: 10px; }
        
        .card-footer-action { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px; margin-top: auto; }
        .card-footer-action i { font-size: 0.8rem; color: #666; cursor: pointer; transition: 0.2s; }
        .card-footer-action i:hover { color: #E50914; }
        .card-footer-action a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>

    <!-- NAVBAR ATAS FIGMA -->
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

    <!-- CONTAINER UTAMA -->
    <div class="container">
        
        <!-- HERO BANNER BESAR -->
        <header class="hero-banner">
            <div class="welcome">Welcome To</div>
            <h1>Stranger Merch Store</h1>
            <p class="desc">Temukan merchandise eksklusif Stranger Things dan tunjukkan sisi Hawkins-mu!</p>
        </header>

        <!-- LAYOUT PERPADUAN BAWAH -->
        <div class="split-layout">
            
            <!-- MENU KATEGORI KOTAK (KIRI) -->
            <aside class="kategori-box">
                <div class="kat-title">Kategori</div>
                <ul class="kat-menu">
                    <li><a href="produk.php?kategori=tshirt">T-Shirt</a></li>
                    <li><a href="produk.php?kategori=hoodie">Hoodie</a></li>
                    <li><a href="produk.php?kategori=mug">Mug</a></li>
                    <li><a href="produk.php?kategori=topi">Topi</a></li>
                </ul>
                <a href="../auth/logout.php" class="btn-back" style="color: #ff4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
            </aside>

            <!-- PANEL KONTEN DISPLAY UTAMA (KANAN) -->
            <main class="content-panel">
                
                <!-- 1. SEKSI KATEGORI POPULER MINI -->
                <div>
                    <h3 class="section-title">Kategori Populer</h3>
                    <div class="populer-grid">
                        <div class="populer-card">
                            <i class="fa-solid fa-shirt"></i>
                            <p>Hoodie</p>
                        </div>
                        <div class="populer-card">
                            <i class="fa-solid fa-hat-cowboy"></i>
                            <p>Topi</p>
                        </div>
                    </div>
                </div>

                <!-- 2. SEKSI KATEGORI TERLARIS (GRID KATALOG) -->
                <div>
                    <h3 class="section-title">Kategori Terlaris</h3>
                    <div class="catalog-grid">
                        
                        <?php
                        // Menarik data katalog produk jualan asli langsung dari database Anda
                        $q_ambil = mysqli_query($conn, "SELECT * FROM produk LIMIT 4");
                        if ($q_ambil && mysqli_num_rows($q_ambil) > 0):
                            while ($p = mysqli_fetch_assoc($q_ambil)):
                                $img_name = !empty($p['foto']) ? $p['foto'] : ($p['gambar_produk'] ?? '');
                        ?>
                            <!-- Card Produk Dinamis Database -->
                            <div class="product-item-card">
                                <div class="prod-img-box">
                                    <img src="../assets/img/produk/<?= $img_name; ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
