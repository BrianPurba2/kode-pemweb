<?php
session_start();
// Proteksi halaman
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// Ambil parameter filter kategori jika ada
$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';

// Query ambil data produk dari database
if (!empty($kategori_filter)) {
    // Jika ada filter kategori (tshirt / hoodie)
    $query = "SELECT * FROM produk WHERE kategori='$kategori_filter'";
} else {
    // Ambil semua produk jika tidak ada filter
    $query = "SELECT * FROM produk";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Stranger Merch Store</title>
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

        /* Navbar Sesuai Halaman Home */
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

        /* Konten Utama */
        .container {
            padding: 40px 8%;
        }

        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-left: 4px solid #E50914;
            padding-left: 10px;
        }

        .page-title {
            font-size: 1.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Filter Kiri Kanan Mini */
        .filter-buttons a {
            display: inline-block;
            padding: 6px 15px;
            border: 1px solid rgba(229, 9, 20, 0.5);
            color: #ffffff;
            text-decoration: none;
            font-size: 0.85rem;
            border-radius: 4px;
            margin-left: 5px;
            transition: all 0.3s;
        }

        .filter-buttons a.active-filter, .filter-buttons a:hover {
            background: #E50914;
            border-color: #E50914;
            box-shadow: 0 0 8px rgba(229, 9, 20, 0.5);
        }

        /* Grid Catalog Baju Ala Figma */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 25px;
        }

        /* Kartu Produk (Card) */
        .product-card {
            background: rgba(15, 5, 5, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .product-card:hover {
            border-color: rgba(229, 9, 20, 0.5);
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6), 0 0 10px rgba(229, 9, 20, 0.1);
        }

        /* Area Foto Produk */
        .image-container {
            background: #150a0a;
            border-radius: 6px;
            height: 180px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 15px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.02);
        }

        .image-container img {
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            transition: transform 0.3s;
        }

        .product-card:hover .image-container img {
            transform: scale(1.05);
        }

        .product-info h4 {
            font-size: 0.95rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Rating Bintang Sesuai Layout Desain */
        .rating {
            color: #ffc107;
            font-size: 0.8rem;
            margin-bottom: 8px;
        }

        .price {
            color: #E50914;
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 15px;
        }

        /* Tombol Beli / Masuk Keranjang */
        .btn-buy {
            display: block;
            width: 100%;
            padding: 10px;
            background: transparent;
            border: 1px solid #E50914;
            color: #ffffff;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            border-radius: 4px;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }

        .btn-buy:hover {
            background: #E50914;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4);
        }

        .no-data {
            text-align: center;
            grid-column: 1 / -1;
            padding: 50px 0;
            color: #888888;
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER THINGS</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php" class="active">PRODUK</a></li>
            <li><a href="cart.php">KERANJANG</a></li>
            <li><a href="kategori.php">KATEGORI</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="tentang.php">TENTANG KAMI</a></li>
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
        </ul>
    </nav>

    <!-- Konten Utama Katalog -->
    <main class="container">
        <div class="header-section">
            <h2 class="page-title">
                <?= !empty($kategori_filter) ? $kategori_filter : 'Semua Produk'; ?>
            </h2>
            
            <!-- Tombol Filter Cepat -->
            <div class="filter-buttons">
                <a href="produk.php" class="<?= empty($kategori_filter) ? 'active-filter' : ''; ?>">Semua</a>
                <a href="produk.php?kategori=tshirt" class="<?= $kategori_filter == 'tshirt' ? 'active-filter' : ''; ?>">T-Shirt</a>
                <a href="produk.php?kategori=hoodie" class="<?= $kategori_filter == 'hoodie' ? 'active-filter' : ''; ?>">Hoodie</a>
            </div>
        </div>

        <!-- Grid Etalase Produk -->
        <div class="product-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="product-card">
                        <div>
                            <div class="image-container">
                                <!-- Memanggil gambar dari folder assets/img/produk -->
                                <img src="../assets/img/produk/<?= $row['foto']; ?>" alt="<?= $row['nama_produk']; ?>" onerror="this.src='https://placehold.co'">
                            </div>
                            <div class="product-info">
                                <h4><?= $row['nama_produk']; ?></h4>
                                <div class="rating">★★★★★</div>
                                <div class="price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <a href="tambah_cart.php?id=<?= $row['id_produk']; ?>" class="btn-buy">TAMBAH KE KERANJANG</a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-data">
                    <p>Belum ada produk yang diunggah di kategori ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>