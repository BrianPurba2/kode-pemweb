<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit;
}
include '../config/koneksi.php';

$error_msg = '';
if (isset($_POST['tambah_produk'])) {
    $nama_produk = mysqli_real_escape_string($conn, $_POST['nama_produk']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga       = intval($_POST['harga']);
    $stok        = intval($_POST['stok']);

    $foto     = $_FILES['foto']['name'];
    $tmp_name = $_FILES['foto']['tmp_name'];
    $folder_tujuan = "../assets/img/produk/";

    if (!file_exists($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }

    if (move_uploaded_file($tmp_name, $folder_tujuan . $foto)) {
        $query = "INSERT INTO produk (nama_produk, kategori, harga, stok, foto) VALUES ('$nama_produk', '$kategori', '$harga', '$stok', '$foto')";
        if (mysqli_query($conn, $query)) {
            header("Location: admin.php");
            exit;
        } else {
            $error_msg = "Gagal simpan ke database: " . mysqli_error($conn);
        }
    } else {
        $error_msg = "Gagal upload gambar!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Control</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 4%; background: #0a0202; border-bottom: 2px solid #E50914; position: sticky; top: 0; z-index: 100; }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .main-layout { display: flex; padding: 25px 4%; gap: 25px; align-items: flex-start; }
        .sidebar { width: 240px; background: rgba(10, 2, 2, 0.6); border: 1px solid rgba(229, 9, 20, 0.3); border-radius: 10px; padding: 20px; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; background: #1a1a1a; border: 2px solid #E50914; object-fit: cover; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; color: #bbb; text-decoration: none; padding: 10px 15px; font-size: 0.85rem; border-radius: 6px; }
        .sidebar-menu li.active a { background: #E50914; color: white !important; font-weight: 500; }
        .content-area { flex: 1; display: flex; flex-direction: column; gap: 20px; }
        .hero-banner-wide { background: linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3)), url('../assets/img/stranger-banner-wide.jpg') no-repeat center center; background-size: cover; border: 2px solid #E50914; border-radius: 12px; height: 140px; }
        .form-box { background: rgba(15, 5, 5, 0.6); border: 1px solid rgba(229, 9, 20, 0.2); border-radius: 8px; padding: 25px; max-width: 600px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 6px; font-size: 0.8rem; color: #ccc; }
        .form-control { width: 100%; padding: 10px; background: #150505; border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 6px; color: #ffffff; font-size: 0.85rem; outline: none; }
        .form-control:focus { border-color: #E50914; }
        .btn-submit { background: #E50914; color: white; border: none; padding: 10px; border-radius: 6px; font-weight: 600; width: 100%; cursor: pointer; text-transform: uppercase; font-size: 0.85rem; }
    </style>
</head>
<body>

    <nav class="navbar">
        <a href="admin.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="admin.php">HOME</a></li>
            <li><a href="tambah_produk.php" style="color: #E50914;">PRODUK</a></li>
            <li><a href="data_pesanan.php">RIWAYAT PEMESANAN</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-basket-shopping"></i></a></li>
        </ul>
    </nav>

    <div class="main-layout">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="../assets/img/avatar_dustin.png" class="user-avatar" alt="">
                <div><h4>Admin Hawkins</h4><p>Online</p></div>
            </div>
            <ul class="sidebar-menu">
                <li><a href="admin.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
                <li class="active"><a href="tambah_produk.php"><i class="fa-solid fa-box-open"></i> Kategori</a></li>
                <li><a href="data_pesanan.php"><i class="fa-solid fa-file-invoice-dollar"></i> Pesanan</a></li>
                <li><a href="../auth/logout.php" style="color: #ff4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="content-area">
            <div class="hero-banner-wide"></div>
            <h3 style="font-size: 0.9rem; text-transform: uppercase; border-left: 3px solid #E50914; padding-left: 10px;">Tambah Produk Baru</h3>
            
            <div class="form-box">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_produk" class="form-control" placeholder="Contoh: Hellfire Club Shirt" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control">
                            <option value="tshirt">T-Shirt</option>
                            <option value="hoodie">Hoodie</option>
                            <option value="mug">Mug</option>
                            <option value="topi">Topi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Gambar Produk</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" name="tambah_produk" class="btn-submit">Simpan Produk</button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>
