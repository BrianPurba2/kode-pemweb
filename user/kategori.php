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
    <title>Kategori Produk - Stranger Merch Store</title>
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
        .container { padding: 40px 6%; text-align: center; }
        .page-title { font-size: 1.8rem; font-weight: 700; text-transform: uppercase; margin-bottom: 30px; letter-spacing: 1px; }
        .grid-kategori { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; margin-top: 20px; }
        .card-kategori { background: rgba(15, 5, 5, 0.6); border: 2px solid #E50914; border-radius: 8px; padding: 30px; text-decoration: none; color: white; transition: 0.3s; }
        .card-kategori:hover { transform: translateY(-5px); background: rgba(229, 9, 20, 0.1); box-shadow: 0 0 15px rgba(229, 9, 20, 0.4); }
        .card-kategori i { font-size: 2.5rem; color: #E50914; margin-bottom: 15px; }
        .card-kategori h3 { font-size: 1.2rem; font-weight: 600; text-transform: uppercase; }
        .page-title {
             font-size: 1.5rem;
             font-weight: 700;
             letter-spacing: 1px;
             text-transform: uppercase;
             margin-bottom: 30px;
             text-align: left; /* Memastikan teks rata kiri seperti di keranjang */

             /* INI KUNCI GARIS MERAH VERTIKALNYA */
             border-left: 4px solid #E50914; 
             padding-left: 12px; }
        .navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 6%;
    background: #0a0202;
    
    /* INI KUNCI GARIS MERAH PANJANG DI BAWAH NAVBAR */
    border-bottom: 2px solid #E50914; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER THINGS</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="cart.php">KERANJANG</a></li>
            <li><a href="kategori.php" class="active">KATEGORI</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="tentang.php">TENTANG KAMI</a></li>
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
        </ul>
    </nav>

    <main class="container">
        <h2 class="page-title">Kategori Populer</h2>
        <div class="grid-kategori">
            <a href="produk.php?kategori=tshirt" class="card-kategori">
                <i class="fa-solid fa-shirt"></i>
                <h3>T-Shirt</h3>
            </a>
            <a href="produk.php?kategori=hoodie" class="card-kategori">
                <i class="fa-solid fa-user-ninja"></i>
                <h3>Hoodie</h3>
            </a>
            <a href="produk.php?kategori=mug" class="card-kategori">
                <i class="fa-solid fa-mug-hot"></i>
                <h3>Mug / Cangkir</h3>
            </a>
            <a href="produk.php?kategori=hat" class="card-kategori">
                <i class="fa-solid fa-hat-cowboy"></i>
                <h3>Hat / Topi</h3>
            </a>
        </div>
    </main>

</body>
</html>