<?php
session_start();
// Proteksi Keamanan: Pastikan pembeli sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$id_user = $_SESSION['id_user'];
$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Diva';

// Proteksi otomatis nama tabel transaksi/pesanan agar tidak error database
$nama_tabel = 'pesanan';
$cek_tabel = mysqli_query($conn, "SHOW TABLES LIKE 'transaksi'");
if ($cek_tabel && mysqli_num_rows($cek_tabel) > 0) {
    $nama_tabel = 'transaksi';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - Stranger Merch Store</title>
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
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .nav-links a.active { color: #E50914; font-weight: 600; }
        .user-menu { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.05); padding: 6px 12px; border-radius: 4px; font-size: 0.85rem;}

        /* CONTAINER UTAMA MELEBAR PENUH SSUAI FIGMA */
        .container { padding: 40px 8%; display: flex; flex-direction: column; gap: 20px; }
        
        /* JUDUL HALAMAN GARIS MERAH */
        .page-title {
            font-size: 1.3rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            border-left: 4px solid #E50914; padding-left: 12px; margin-bottom: 10px;
        }

        /* TABS FILTER HORIZONTAL */
        .status-tabs { display: flex; gap: 10px; list-style: none; margin-bottom: 15px; overflow-x: auto; padding-bottom: 5px; }
        .tab-item { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); padding: 6px 16px; border-radius: 6px; font-size: 0.8rem; cursor: pointer; color: #ccc; white-space: nowrap; transition: 0.2s; }
        .tab-item.active { background: rgba(229, 9, 20, 0.1); border-color: #E50914; color: white; font-weight: 500; }

        /* KOTAK DAFTAR PESANAN BINGKAI MERAH MENYALA */
        .order-card-wide {
            background: rgba(15, 5, 5, 0.6);
            border: 2px solid #E50914;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            justify-content: space-between;
            gap: 30px;
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.2);
            margin-bottom: 20px;
        }

        /* KOLOM INFO TEKS KIRI */
        .order-meta-info { width: 220px; display: flex; flex-direction: column; gap: 12px; border-right: 1px solid rgba(255,255,255,0.08); padding-right: 15px; }
        .order-id-label { font-size: 0.75rem; color: #888; text-transform: uppercase; }
        .order-id-label strong { color: #fff; font-size: 1rem; display: block; margin-top: 2px; letter-spacing: 0.5px; }
        .meta-group { font-size: 0.75rem; color: #888; }
        .meta-group span { display: block; color: #fff; font-weight: 500; margin-top: 2px; font-size: 0.85rem; }

        /* KOLOM DAFTAR SNAPS GAMBAR PRODUK TENGAH-KANAN */
        .order-products-snaps { flex: 1; display: flex; gap: 15px; align-items: center; overflow-x: auto; }
        .snap-item { background: rgba(15, 5, 5, 0.5); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; padding: 10px; width: 100px; text-align: center; flex-shrink: 0; }
        .snap-img-box { width: 100%; height: 60px; display: flex; align-items: center; justify-content: center; margin-bottom: 6px; background: #150505; border-radius: 4px; overflow: hidden; }
        .snap-img-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .snap-img-box i { font-size: 1.5rem; color: rgba(255,255,255,0.03); }
        .snap-name { font-size: 0.65rem; color: #ccc; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .snap-qty { font-size: 0.6rem; color: #666; margin-top: 1px; }

        .btn-bottom-back { display: flex; align-items: center; gap: 6px; color: #666; text-decoration: none; font-size: 0.85rem; width: max-content; transition: 0.2s; }
        .btn-bottom-back:hover { color: #fff; }
    </style>
</head>
<body>

    <!-- NAVBAR ATAS -->
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="riwayat.php" class="active">RIWAYAT PEMESANAN</a></li>
            <li><a href="cart.php"><i class="fa-solid fa-basket-shopping"></i></a></li>
            <li class="user-menu"><i class="fa-regular fa-user"></i> Hi, <?= htmlspecialchars($nama_user); ?> <i class="fa-solid fa-caret-down"></i></li>
        </ul>
    </nav>

    <!-- CONTENT BOX MELEBAR -->
    <main class="container">
        <h2 class="page-title">Riwayat Pemesanan</h2>

        <!-- TABS FILTER SSUAI GAMBAR FIGMA -->
        <ul class="status-tabs">
            <li class="tab-item active">Semua Pesanan</li>
            <li class="tab-item">Menunggu</li>
            <li class="tab-item">Dikemas</li>
            <li class="tab-item">Dikirim</li>
            <li class="tab-item">Selesai</li>
            <li class="tab-item">Dibatalkan</li>
        </ul>

        <?php
        // Tarik data riwayat transaksi asli dari DB milik user saat ini
        if ($nama_tabel == 'transaksi') {
            $q_orders = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user='$id_user' ORDER BY id_transaksi DESC");
        } else {
            $q_orders = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_user='$id_user' ORDER BY id_pesanan DESC");
        }

        if ($q_orders && mysqli_num_rows($q_orders) > 0):
            while ($ord = mysqli_fetch_assoc($q_orders)):
                $id_ord = $ord['id_transaksi'] ?? $ord['id_pesanan'];
                $tgl = $ord['tanggal_transaksi'] ?? ($ord['tanggal'] ?? '---');
                $total = $ord['total_bayar'] ?? ($ord['total'] ?? 0);
        ?>
            <!-- KOTAK DATA DINAMIS DARI DATABASE -->
            <div class="order-card-wide">
                <div class="order-meta-info">
                    <div class="order-id-label">Order ID <strong>#ORD-<?= $id_ord; ?></strong></div>
                    <div class="meta-group">Tanggal Pemesanan <span><?= date('d M Y, H:i', strtotime($tgl)); ?></span></div>
                    <div class="meta-group">Total Pemesanan <span style="color:#E50914; font-weight:600;">Rp <?= number_format($total, 0, ',', '.'); ?></span></div>
                </div>
                
                <div class="order-products-snaps">
                    <?php
                    // Ambil item produk di dalam transaksi ini
                    $q_det = mysqli_query($conn, "SELECT dt.*, p.nama_produk, p.foto, p.gambar_produk 
                                                  FROM detail_transaksi dt 
                                                  JOIN produk p ON dt.id_produk = p.id_produk 
                                                  WHERE dt.id_transaksi = '$id_ord'");
                    if(!$q_det) {
                        $q_det = mysqli_query($conn, "SELECT dp.*, p.nama_produk, p.foto, p.gambar_produk 
                                                      FROM detail_pesanan dp 
                                                      JOIN produk p ON dp.id_produk = p.id_produk 
                                                      WHERE dp.id_pesanan = '$id_ord'");
                    }
                    
                    while ($det = mysqli_fetch_assoc($q_det)):
                        $img = !empty($det['foto']) ? $det['foto'] : ($det['gambar_produk'] ?? '');
                    ?>
                        <div class="snap-item">
                            <div class="snap-img-box">
                                <img src="../assets/img/produk/<?= $img; ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <i class="fa-solid fa-shirt" style="display:none;"></i>
                            </div>
                            <div class="snap-name"><?= htmlspecialchars($det['nama_produk']); ?></div>
                            <div class="snap-qty">x<?= $det['jumlah'] ?? ($det['qty'] ?? 1); ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php 
            endwhile;
        else: 
            // TAMPILAN CADANGAN MOCKUP PERSIS SEPERTI DI GAMBAR JIKA DATA DB KOSONG
        ?>
            <!-- KOTAK VISUAL MOCKUP FIGMA -->
            <div class="order-card-wide">
                <div class="order-meta-info">
                    <div class="order-id-label">Order ID <strong>#ORD-0001</strong></div>
                    <div class="meta-group">Tanggal Pemesanan <span>24 Mei 2026, 14:30</span></div>
