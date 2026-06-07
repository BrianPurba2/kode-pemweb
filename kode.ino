<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';
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
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 6%; background: rgba(10, 2, 2, 0.95); border-bottom: 2px solid #E50914; position: sticky; top: 0; z-index: 100; }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .container { padding: 30px 6%; display: flex; flex-direction: column; gap: 25px; }
        .hero-banner { background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), url('../assets/img/stranger-banner-wide.jpg') no-repeat center center; background-size: cover; border: 2px solid #E50914; border-radius: 12px; padding: 60px 20px; text-align: center; }
        .split-layout { display: flex; gap: 30px; align-items: flex-start; }
        .kategori-box { width: 220px; background: rgba(15, 5, 5, 0.4); border: 2px solid #E50914; border-radius: 12px; padding: 20px; }
        .kat-title { background: #E50914; color: white; text-align: center; font-size: 0.8rem; font-weight: 700; padding: 8px; border-radius: 4px; text-transform: uppercase; margin-bottom: 15px; }
        .kat-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; text-align: center; }
        .kat-menu a { color: #ccc; text-decoration: none; font-size: 0.85rem; display: block; padding: 8px; border-radius: 4px; }
        .content-panel { flex: 1; display: flex; flex-direction: column; gap: 25px; }
        .catalog-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .product-item-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 6px; padding: 12px; display: flex; flex-direction: column; }
        .prod-img-box { width: 100%; height: 110px; background: #150505; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
        .card-footer-action { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 8px; margin-top: auto; }
        .card-footer-action a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="home.php" style="color: #E50914;">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-basket-shopping"></i></a></li>
        </ul>
    </nav>
    <div class="container">
        <header class="hero-banner"><h1>Stranger Merch Store</h1></header>
        <div class="split-layout">
            <aside class="kategori-box">
                <div class="kat-title">Kategori</div>
                <ul class="kat-menu">
                    <li><a href="produk.php?kategori=tshirt">T-Shirt</a></li>
                    <li><a href="produk.php?kategori=hoodie">Hoodie</a></li>
                </ul>
            </aside>
            <main class="content-panel">
                <h3 style="border-left: 3px solid #E50914; padding-left: 10px;">Kategori Terlaris</h3>
                <div class="catalog-grid">
                    <?php
                    $q_ambil = mysqli_query($conn, "SELECT * FROM produk LIMIT 4");
                    if ($q_ambil && mysqli_num_rows($q_ambil) > 0):
                        while ($p = mysqli_fetch_assoc($q_ambil)):
                            $img_name = $p['foto'] ?? ($p['gambar_produk'] ?? 'default.png');
                    ?>
                        <div class="product-item-card">
                            <div class="prod-img-box"><img src="../assets/img/produk/<?= $img_name; ?>" style="max-width:100%; max-height:100%;"></div>
                            <h4><?= htmlspecialchars($p['nama_produk']); ?></h4>
                            <div class="card-footer-action"><i class="fa-regular fa-eye"></i><a href="tambah_cart.php?id=<?= $p['id_produk']; ?>"><i class="fa-solid fa-basket-shopping"></i></a></div>
                        </div>
                    <?php 
                        endwhile;
                    else: 
                    ?>
                        <div class="product-item-card">
                            <div class="prod-img-box"><i class="fa-solid fa-shirt" style="color:rgba(255,255,255,0.03); font-size:2rem;"></i></div>
                            <h4>Hoodie Hawkins A.H CLUB</h4>
                            <div class="card-footer-action"><i class="fa-regular fa-eye"></i><i class="fa-solid fa-basket-shopping"></i></div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
