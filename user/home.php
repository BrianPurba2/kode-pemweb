<?php
session_start();
// Mengunci halaman, jika belum login tidak bisa masuk
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Stranger Merch Store</title>
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

        /* Navigasi Atas (Navbar) */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            background: rgba(15, 5, 5, 0.95);
            border-bottom: 2px solid #E50914;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar .logo {
            font-family: 'Cinzel Decorative', serif;
            color: #E50914;
            font-size: 1.5rem;
            text-decoration: none;
            text-shadow: 0 0 8px rgba(229, 9, 20, 0.6);
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-links a {
            color: #ffffff;
            text-decoration: none;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .nav-links a:hover, .nav-links a.active {
            color: #E50914;
            font-weight: 600;
        }

        /* Banner Utama (Hero Section) Sesuai Figma */
        .hero-banner {
            height: 350px;
            background: linear-gradient(rgba(10,2,2,0.6), #0a0202), radial-gradient(circle, rgba(229, 9, 20, 0.3) 0%, transparent 70%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            border-bottom: 1px solid rgba(229, 9, 20, 0.2);
            padding: 0 20px;
        }

        .hero-banner h1 {
            font-family: 'Cinzel Decorative', serif;
            font-size: 2.8rem;
            color: #ffffff;
            letter-spacing: 3px;
            text-shadow: 0 0 15px rgba(229, 9, 20, 0.8);
            margin-bottom: 10px;
        }

        .hero-banner p {
            color: #aaaaaa;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }

        /* Bagian Konten & Kategori */
        .main-content {
            padding: 40px 8%;
        }

        .section-title {
            font-size: 1.3rem;
            letter-spacing: 1px;
            margin-bottom: 25px;
            border-left: 4px solid #E50914;
            padding-left: 10px;
            text-transform: uppercase;
        }

        /* Grid Tombol Kategori seperti di Figma */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .category-card {
            background: rgba(30, 10, 10, 0.4);
            border: 1px solid rgba(229, 9, 20, 0.3);
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            text-decoration: none;
            color: #ffffff;
            transition: all 0.3s ease;
        }

        .category-card:hover {
            background: rgba(229, 9, 20, 0.15);
            border-color: #E50914;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(229, 9, 20, 0.3);
        }

        .category-card h3 {
            font-size: 1.2rem;
            letter-spacing: 1px;
        }

        .category-card p {
            font-size: 0.8rem;
            color: #888888;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER THINGS</a>
        <ul class="nav-links">
            <li><a href="home.php" class="active">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="cart.php">KERANJANG</a></li>
            <li><a href="kategori.php">KATEGORI</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="tentang.php">TENTANG KAMI</a></li>
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
        </ul>
    </nav>

    <!-- Hero Banner -->
    <header class="hero-banner">
        <p>WELCOME TO</p>
        <h1>TOKO STRANGER MERCH NURUL DIVA</h1>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <h2 class="section-title">Kategori Populer</h2>
        
        <!-- Pilihan Kategori Baju Sesuai Alur Figma -->
        <div class="category-grid">
            <a href="produk.php?kategori=tshirt" class="category-card">
                <h3>T-SHIRT</h3>
                <p>Lihat Koleksi Kaos Hawkins</p>
            </a>
            <a href="produk.php?kategori=hoodie" class="category-card">
                <h3>HOODIE</h3>
                <p>Lihat Koleksi Jaket Tebal</p>
            </a>
            <a href="produk.php?kategori=mug" class="category-card">
                <h3>MUG</h3>
                <p>Lihat Koleksi Cangkir</p>
            </a>
             <a href="produk.php?kategori=had" class="category-card">
                <h3>HAD</h3>
                <p>Lihat Koleksi Topi</p>
            </a>
        </div>
    </main>

</body>
</html>