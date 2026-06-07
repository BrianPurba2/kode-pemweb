<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Diva';
$kategori_aktif = isset($_GET['kategori']) ? $_GET['kategori'] : 'tshirt';

$judul_halaman = 'T-SHIRT';
if ($kategori_aktif == 'hoodie') { $judul_halaman = 'HOODIE'; }
elseif ($kategori_aktif == 'mug') { $judul_halaman = 'MUG'; }
elseif ($kategori_aktif == 'topi') { $judul_halaman = 'TOPI'; }

$query_produk = "SELECT * FROM produk WHERE kategori = '$kategori_aktif' ORDER BY id_produk DESC";
$result_produk = mysqli_query($conn, $query_produk);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Stranger Merch Store</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 6%; background: rgba(10, 2, 2, 0.95); border-bottom: 2px solid #E50914; position: sticky; top: 0; z-index: 100; }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .nav-links a.active { color: #E50914; font-weight: 600; }
        .user-menu { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; }
        .main-layout { display: flex; padding: 35px 6%; gap: 30px; align-items: flex-start; }
        .kategori-sidebar { width: 220px; background: rgba(15, 5, 5, 0.4); border: 2px solid #E50914; border-radius: 12px; padding: 20px; }
        .sidebar-head { background: #E50914; color: white; text-align: center; font-size: 0.8rem; font-weight: 700; padding: 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        .sidebar-links { list-style: none; display: flex; flex-direction: column; gap: 5px; text-align: center; }
        .sidebar-links a { color: #ccc; text-decoration: none; font-size: 0.85rem; display: block; padding: 10px; border-radius: 6px; }
        .sidebar-links li.active a { background: #E50914; color: white !important; font-weight: 600; }
        .btn-back { display: block; text-align: center; color: #666; text-decoration: none; font-size: 0.8rem; margin-top: 25px; }
        .display-panel { flex: 1; display: flex; flex-direction: column; gap: 20px; }
        .category-header-title { font-size: 1.4rem; font-weight: 700; text-transform: uppercase; text-align: center; letter-spacing: 2px; margin-bottom: 10px; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 15px; color: #fff; }
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .item-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 8px; padding: 15px; display: flex; flex-direction: column; }
        .image-container { width: 100%; height: 160px; background: #120404; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; overflow: hidden; }
        .image-container img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .info-box h4 { font-size: 0.85rem; font-weight: 500; color: #fff; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .info-box .price { font-size: 0.9rem; color: #aaa; font-weight: 500; margin-bottom: 8px; }
        .stars { color: #FF9800; font-size: 0.7rem; margin-bottom: 12px; }
        .card-actions { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px; margin-top: auto; }
        .card-actions i { font-size: 0.9rem; color: #666; cursor: pointer; }
        .card-actions i:hover { color: #E50914; }
        .card-actions a { color: inherit; text-decoration: none; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php" class="active">PRODUK</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-basket-shopping"></i></a></li>
            <li class="user-menu"><i class="fa-regular fa-user"></i> Hi, <?= htmlspecialchars($nama_user); ?> <i class="fa-solid fa-caret-down"></i></li>
        </ul>
    </nav>
    <div class="main-layout">
        <aside class="kategori-sidebar">
            <div class="sidebar-head">Kategori</div>
            <ul class="sidebar-links">
                <li class="<?= $kategori_aktif == 'tshirt' ? 'active' : ''; ?>"><a href="produk.php?kategori=tshirt">T-Shirt</a></li>
                <li class="<?= $kategori_aktif == 'hoodie' ? 'active' : ''; ?>"><a href="produk.php?kategori=hoodie">Hoodie</a></li>
                <li class="<?= $kategori_aktif == 'mug' ? 'active' : ''; ?>"><a href="produk.php?kategori=mug">Mug</a></li>
                <li class="<?= $kategori_aktif == 'topi' ? 'active' : ''; ?>"><a href="produk.php?kategori=topi">Topi</a></li>
            </ul>
            <a href="home.php" class="btn-back"><i class="fa-solid fa-caret-left"></i> Back</a>
        </aside>
        <main class="display-panel">
            <h2 class="category-header-title"><?= $judul_halaman; ?></h2>
            <div class="product-grid">
                <?php 
                if ($result_produk && mysqli_num_rows($result_produk) > 0):
                    while ($p = mysqli_fetch_assoc($result_produk)):
                        $nama_gambar = !empty($p['foto']) ? $p['foto'] : ($p['gambar_produk'] ?? 'default.png');
                ?>
                    <div class="item-card">
                        <div class="image-container">
                            <img src="../assets/img/produk/<?= $nama_gambar; ?>" onerror="this.src='data:image/svg+xml;utf8,<svg xmlns=\'http://w3.org\' viewBox=\'0 0 24 24\' fill=\'%23222\'><rect width=\'100%\' height=\'100%\'/></svg>'">
                        </div>
                        <div class="info-box">
                            <h4><?= htmlspecialchars($p['nama_produk']); ?></h4>
                            <div class="price">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></div>
                            <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        </div>
                        <div class="card-actions">
                            <i class="fa-regular fa-eye"></i><i class="fa-regular fa-heart"></i>
                            <a href="tambah_cart.php?id=<?= $p['id_produk']; ?>"><i class="fa-solid fa-basket-shopping"></i></a>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else: 
                    $mockups = ($kategori_aktif == 'tshirt') ? [
                        ['t' => 'T-Shirt Hawkins Lab', 'p' => '120.000'],
                        ['t' => 'T-Shirt The Upside Down', 'p' => '149.308']
                    ] : [
                        ['t' => 'Hoodie The Upside Down', 'p' => '230.000'],
                        ['t' => 'Hoodie Hawkins Indiana', 'p' => '220.000']
                    ];
                    foreach ($mockups as $mo):
                ?>
                    <div class="item-card">
                        <div class="image-container"><i class="fa-solid fa-shirt" style="font-size: 3rem; color: rgba(255,255,255,0.03);"></i></div>
                        <div class="info-box">
                            <h4><?= $mo['t']; ?></h4>
                            <div class="price">Rp <?= $mo['p']; ?></div>
                            <div class="stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></div>
                        </div>
                        <div class="card-actions"><i class="fa-regular fa-eye"></i><i class="fa-regular fa-heart"></i><i class="fa-solid fa-basket-shopping"></i></div>
                    </div>
                <?php 
                    endforeach;
                endif; 
                ?>
            </div>
        </main>
    </div>
</body>
</html>
