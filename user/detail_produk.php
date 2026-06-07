<?php
session_start();
// Kunci keamanan halaman
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// Ambil ID produk dari parameter URL (?id=...)
$id = isset($_GET['id']) ? $_GET['id'] : '';

// Query disesuaikan dengan nama tabel 'produk' di database kita
$query = "SELECT * FROM produk WHERE id_produk='$id'";
$result = mysqli_query($conn, $query);

// Jika ID produk tidak ditemukan, kembalikan ke halaman produk
if (mysqli_num_rows($result) == 0) {
    header("Location: produk.php");
    exit;
}

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail - <?= $data['nama_produk']; ?></title>
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        
        /* Navbar Konsisten */
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 8%; background: rgba(15, 5, 5, 0.95); border-bottom: 2px solid #E50914;
            position: sticky; top: 0; z-index: 100;
        }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.5rem; text-decoration: none; }
        .nav-links { display: flex; list-style: none; gap: 30px; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.95rem; }
        .nav-links a:hover, .nav-links a.active { color: #E50914; font-weight: 600; }

        .container { padding: 50px 8%; display: flex; gap: 50px; align-items: center; justify-content: center; }
        
        /* Layout Kiri (Foto) & Kanan (Detail) */
        .detail-img-box {
            flex: 1; max-width: 400px; background: #150a0a; border: 1px solid rgba(229, 9, 20, 0.2);
            border-radius: 8px; padding: 30px; display: flex; justify-content: center; align-items: center;
            box-shadow: 0 8px 25px rgba(0,0,0,0.5);
        }
        .detail-img-box img { max-width: 100%; object-fit: contain; }
        
        .detail-info-box { flex: 1; max-width: 500px; }
        .back-link { display: inline-block; color: #aaa; text-decoration: none; font-size: 0.85rem; margin-bottom: 15px; }
        .back-link:hover { color: #E50914; }
        
        h1 { font-family: 'Cinzel Decorative', serif; color: #ffffff; font-size: 2rem; margin-bottom: 15px; text-shadow: 0 0 10px rgba(229,9,20,0.4); }
        .price { font-size: 1.5rem; color: #E50914; font-weight: 600; margin-bottom: 20px; }
        
        .meta-text { font-size: 0.95rem; color: #ccc; margin-bottom: 10px; }
        .meta-text strong { color: #ffffff; }
        .desc-box { background: rgba(15, 5, 5, 0.6); border: 1px solid rgba(255,255,255,0.05); padding: 20px; border-radius: 6px; margin: 25px 0; font-size: 0.9rem; color: #aaa; line-height: 1.6; }
        
        /* Input & Tombol Form Belanja */
        .purchase-form { display: flex; gap: 15px; align-items: center; }
        .qty-input { width: 70px; padding: 11px; background: rgba(30, 10, 10, 0.6); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: white; text-align: center; font-size: 1rem; }
        .qty-input:focus { outline: none; border-color: #E50914; }
        
        .btn-submit {
            flex: 1; padding: 12px; background: #E50914; color: white; border: none;
            font-weight: 600; font-size: 1rem; border-radius: 4px; cursor: pointer;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4); transition: 0.3s; letter-spacing: 0.5px;
        }
        .btn-submit:hover { background: #b8070f; box-shadow: 0 0 15px rgba(229, 9, 20, 0.8); }
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

    <!-- Konten Utama Detail -->
    <main class="container">
        <div class="detail-img-box">
            <!-- Panggilan nama kolom foto disesuaikan -->
            <img src="../assets/img/produk/<?= $data['foto']; ?>" alt="<?= $data['nama_produk']; ?>" onerror="this.src='https://placehold.co'">
        </div>
        
        <div class="detail-info-box">
            <a href="produk.php" class="back-link">◀ Kembali ke Katalog</a>
            <h1><?= $data['nama_produk']; ?></h1>
            <div class="price">Rp <?= number_format($data['harga'], 0, ',', '.'); ?></div>
            
            <div class="meta-text">Kategori: <span style="text-transform: uppercase; color: #E50914; font-weight:600;"><?= $data['kategori']; ?></span></div>
            <div class="meta-text">Sisa Stok: <strong><?= $data['stok']; ?> Pcs</strong></div>
            
            <div class="desc-box">
                <!-- Deskripsi otomatis bawaan jika kolom deskripsi di database Anda belum diisi -->
                <?= !empty($data['deskripsi']) ? $data['deskripsi'] : 'Koleksi merchandise eksklusif bertema Stranger Things dengan bahan premium 100% katun murni. Sangat nyaman dipakai untuk aktivitas harian warga Hawkins.'; ?>
            </div>

            <!-- Form Pengiriman data ke tambah_cart.php secara aman -->
            <form method="POST" action="tambah_cart.php?id=<?= $data['id_produk']; ?>" class="purchase-form">
                <input type="number" name="qty" value="1" min="1" max="<?= $data['stok']; ?>" class="qty-input">
                <button type="submit" name="cart" class="btn-submit">TAMBAH KE KERANJANG</button>
            </form>
        </div>
    </main>

</body>
</html>