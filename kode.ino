<?php
session_start();
// Pastikan pengguna sudah login sebelum melihat produk
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// 1. Ambil nama user untuk profil navbar
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Diva';

// 2. LOGIKA UTAMA FILTER: Tangkap parameter kategori dari URL (?kategori=xxx)
// Jika tidak ada parameter, maka set default menjadi 'tshirt' sesuai mockup pertama Anda
$kategori_aktif = isset($_GET['kategori']) ? $_GET['kategori'] : 'tshirt';

// 3. Siapkan judul halaman agar dinamis mengikuti pilihan menu
$judul_halaman = 'T-SHIRT';
if ($kategori_aktif == 'hoodie') { $judul_halaman = 'HOODIE'; }
elseif ($kategori_aktif == 'mug') { $judul_halaman = 'MUG'; }
elseif ($kategori_aktif == 'topi') { $judul_halaman = 'TOPI'; }

// 4. Jalankan Query pencarian barang ke database berdasarkan kategori yang sedang dipilih
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
        
        /* NAVBAR UTAMA */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 15px 6%; background: rgba(10, 2, 2, 0.95); border-bottom: 2px solid #E50914;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .nav-links a.active { color: #E50914; font-weight: 600; }
        .user-menu { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;}

        /* LAYOUT SPLIT DUA KOLOM */
        .main-layout { display: flex; padding: 35px 6%; gap: 30px; align-items: flex-start; }

        /* SIDEBAR KATEGORI (KIRI) */
        .kategori-sidebar { width: 220px; background: rgba(15, 5, 5, 0.4); border: 2px solid #E50914; border-radius: 12px; padding: 20px; }
        .sidebar-head { background: #E50914; color: white; text-align: center; font-size: 0.8rem; font-weight: 700; padding: 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        
        .sidebar-links { list-style: none; display: flex; flex-direction: column; gap: 5px; text-align: center; }
        .sidebar-links a { color: #ccc; text-decoration: none; font-size: 0.85rem; display: block; padding: 10px; border-radius: 6px; transition: 0.2s; }
        
        /* CLASS ACTIVE: MENGUBAH WARNA MENJADI KOTAK MERAH SSUAI MOCKUP FIGMA */
        .sidebar-links li.active a { background: #E50914; color: white !important; font-weight: 600; box-shadow: 0 4px 12px rgba(229, 9, 20, 0.3); }
        .sidebar-links a:hover { color: #E50914; background: rgba(255,255,255,0.01); }
        .btn-back-home { display: block; text-align: center; color: #666; text-decoration: none; font-size: 0.8rem; margin-top: 25px; }

        /* AREA DISPLAY PRODUK (KANAN) */
        .display-panel { flex: 1; display: flex; flex-direction: column; gap: 20px; }
        
        /* HEADER JUDUL KATEGORI TENGAH */
        .category-header-title { font-size: 1.4rem; font-weight: 700; text-transform: uppercase; text-align: center; letter-spacing: 2px; margin-bottom: 10px; border-bottom: 1px dashed rgba(255,255,255,0.1); padding-bottom: 15px; color: #fff; }

        /* GRID KATALOG PRODUK 3 KOLOM SEJAJAR */
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .item-card { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 8px; padding: 15px; display: flex; flex-direction: column; transition: 0.3s; }
        .item-card:hover { border-color: #E50914; box-shadow: 0 0 15px rgba(229, 9, 20, 0.15); }
        
        /* IMAGE BOX UKURAN PROPORSIONAL */
        .image-container { width: 100%; height: 160px; background: #120404; border-radius: 6px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; overflow: hidden; }
        .image-container img { max-width: 100%; max-height: 100%; object-fit: contain; }
        
        .info-box { text-align: left; }
        .info-box h4 { font-size: 0.85rem; font-weight: 500; color: #fff; margin-bottom: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .info-box .price { font-size: 0.9rem; color: #aaa; font-weight: 500; margin-bottom: 8px; }
        .stars { color: #FF9800; font-size: 0.7rem; margin-bottom: 12px; }

        /* TOMBOL AKSI BAWAH CARD */
        .card-actions { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 10px; margin-top: auto; }
        .card-actions i { font-size: 0.9rem; color: #666; cursor: pointer; transition: 0.2s; }
        .card-actions i:hover { color: #E50914; }
        .card-actions a { color: inherit; text-decoration: none; }
        
        .empty-notify { text-align: center; padding: 60px 0; color: #666; font-style: italic; grid-column: span 3; }
    </style>
</head>
<body>

    <!-- NAVBAR ATAS -->
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

    <!-- LAYOUT SEJAJAR KIRI KANAN -->
    <div class="main-layout">
        
        <!-- SIDEBAR NAVIGASI KATEGORI (KIRI) -->
        <aside class="kategori-sidebar">
            <div class="sidebar-head">Kategori</div>
            <ul class="sidebar-links">
                <!-- Logika PHP mengecek parameter URL untuk menentukan class="active" -->
                <li class="<?= $kategori_aktif == 'tshirt' ? 'active' : ''; ?>"><a href="produk.php?kategori=tshirt">T-Shirt</a></li>
                <li class="<?= $kategori_aktif == 'hoodie' ? 'active' : ''; ?>"><a href="produk.php?kategori=hoodie">Hoodie</a></li>
                <li class="<?= $kategori_aktif == 'mug' ? 'active' : ''; ?>"><a href="produk.php?kategori=mug">Mug</a></li>
                <li class="<?= $kategori_aktif == 'topi' ? 'active' : ''; ?>"><a href="produk.php?kategori=topi">Topi</a></li>
            </ul>
            <a href="home.php" class="btn-back"><i class="fa-solid fa-caret-left"></i> Back</a>
        </aside>

        <!-- AREA DISPLAY PRODUK KATALOG (KANAN) -->
        <main class="display-panel">
            
            <!-- JUDUL DAFTAR KATEGORI DINAMIS -->
            <h2 class="category-header-title"><?= $judul_halaman; ?></h2>

            <div class="product-grid">
                <?php 
                // Jika database memiliki barang yang sesuai dengan kategori yang dipilih
                if ($result_produk && mysqli_num_rows($result_produk) > 0):
                    while ($p = mysqli_fetch_assoc($result_produk)):
                        // Mendukung pembacaan nama kolom 'foto' atau 'gambar_produk'
                        $nama_gambar = !empty($p['foto']) ? $p['foto'] : ($p['gambar_produk'] ?? 'default.png');
                ?>
                    <!-- CARD PRODUK DINAMIS DB -->
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
                            <i class="fa-regular fa-eye"></i>
                            <i class="fa-regular fa-heart"></i>
                            <!-- Terhubung langsung dengan file penambah keranjang belanja -->
                            <a href="tambah_cart.php?id=<?= $p['id_produk']; ?>"><i class="fa-solid fa-basket-shopping"></i></a>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else: 
                    // TAMPILAN MOCKUP CADANGAN PERSIS FIGMA JIKA BARANG DI DATABASE MASIH KOSONG
                    if ($kategori_aktif == 'tshirt') {
