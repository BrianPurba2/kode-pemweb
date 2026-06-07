<?php
session_start();
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
    <title>Tentang Kami - Stranger Merch Store</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 4%; background: #0a0202; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 20px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; }
        .nav-links a.active { color: #E50914; }
        .container { padding: 50px 15%; text-align: center; }
        .about-box { background: rgba(15, 5, 5, 0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 40px; margin-top: 20px; }
        .brand-name { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 2rem; margin-bottom: 20px; }
        .about-box p { font-size: 1rem; color: #ccc; line-height: 1.8; margin-bottom: 20px; text-align: justify; }
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
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 6%;
            background: #0a0202;

            /* INI KUNCI GARIS MERAH PANJANG DI BAWAH NAVBAR */
            border-bottom: 2px solid #E50914; 
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER THINGS</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="cart.php">KERANJANG</a></li>
            <li><a href="kategori.php">KATEGORI</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="tentang.php" class="active">TENTANG KAMI</a></li>
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
        </ul>
    </nav>

    <main class="container">
        <div class="about-box">
            <h1 class="brand-name">Stranger Merch Store</h1>
            <p>Selamat datang di Stranger Merch Store, destinasi utama bagi para penggemar serial fiksi ilmiah populer untuk mendapatkan koleksi merchandise eksklusif dan berkualitas tinggi. Kami berkomitmen menyediakan produk fashion terbaik, mulai dari Kaos Hellfire Club, Hoodie ikonik Hawkins, hingga berbagai aksesoris unik pelengkap koleksi Anda.</p>
            <p>Terinspirasi dari estetika retro tahun 1980-an yang misterius dan penuh petualangan, setiap merchandise dirancang dengan detail presisi demi kepuasan Anda sebagai penggemar sejati. Jelajahi dunia "Upside Down" bersama koleksi produk andalan kami sekarang juga!</p>
            <p>Salam hangat Diva dan Nurul❤</p>
        </div>
    </main>

</body>
</html>