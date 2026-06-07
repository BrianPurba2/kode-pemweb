<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header("Location: login_admin.php");
    exit;
}
include '../config/koneksi.php';

$nama_tabel = 'pesanan';
$cek_tabel = mysqli_query($conn, "SHOW TABLES LIKE 'transaksi'");
if ($cek_tabel && mysqli_num_rows($cek_tabel) > 0) {
    $nama_tabel = 'transaksi';
}

if ($nama_tabel == 'transaksi') {
    $query = "SELECT * FROM transaksi ORDER BY id_transaksi DESC";
} else {
    $query = "SELECT * FROM pesanan ORDER BY id_pesanan DESC";
}
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Admin - Stranger Merch Store</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 4%; background: #0a0202; border-bottom: 2px solid #E50914; position: sticky; top: 0; z-index: 100; }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .nav-links a.active { color: #E50914; }
        .main-layout { display: flex; padding: 25px 4%; gap: 25px; align-items: flex-start; }
        .sidebar { width: 240px; background: rgba(10, 2, 2, 0.6); border: 1px solid rgba(229, 9, 20, 0.3); border-radius: 10px; padding: 20px; }
        .user-profile { display: flex; align-items: center; gap: 12px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .user-avatar { width: 45px; height: 45px; border-radius: 50%; background: #1a1a1a; border: 2px solid #E50914; object-fit: cover; }
        .sidebar-menu { list-style: none; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-menu a { display: flex; align-items: center; gap: 12px; color: #bbb; text-decoration: none; padding: 10px 15px; font-size: 0.85rem; border-radius: 6px; }
        .sidebar-menu li.active a { background: #E50914; color: white !important; font-weight: 500; }
        .content-area { flex: 1; display: flex; flex-direction: column; gap: 20px; }
        .table-box { background: rgba(15, 5, 5, 0.4); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 6px; overflow: hidden; padding: 12px; }
        .order-table { width: 100%; border-collapse: collapse; text-align: left; }
        .order-table th, .order-table td { padding: 12px 15px; font-size: 0.8rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .order-table th { background: rgba(229, 9, 20, 0.1); color: #E50914; font-weight: 600; text-transform: uppercase; }
        .status-badge { padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; display: inline-block; }
        .status-badge.selesai { background: rgba(76, 175, 80, 0.15); color: #4CAF50; }
        .status-badge.menunggu { background: rgba(233, 30, 99, 0.15); color: #E91E63; }
        .btn-edit-status { color: #fff; background: rgba(255,255,255,0.05); text-decoration: none; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="admin.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="admin.php">HOME</a></li>
            <li><a href="admin_kategori.php">PRODUK</a></li>
            <li><a href="data_pesanan.php" class="active">RIWAYAT PESANAN</a></li>
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
                <li><a href="admin_kategori.php"><i class="fa-solid fa-box-open"></i> Kategori</a></li>
                <li class="active"><a href="data_pesanan.php"><i class="fa-solid fa-file-invoice-dollar"></i> Pesanan</a></li>
                <li><a href="../auth/logout.php" style="color: #ff4444;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a></li>
            </ul>
        </aside>
        <main class="content-area">
            <h3 class="panel-title">Data Pesanan</h3>
            <div class="table-box">
                <table class="order-table">
                    <thead>
                        <tr><th>ID Order</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th><th style="text-align: center;">Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)): 
                                $id = $row['id_transaksi'] ?? $row['id_pesanan'];
                                $tgl = $row['tanggal_transaksi'] ?? ($row['tanggal'] ?? '---');
                                $total = $row['total_bayar'] ?? ($row['total'] ?? 0);
                                $status = $row['status_pesanan'] ?? ($row['status'] ?? 'Menunggu');
                        ?>
                            <tr>
                                <td><strong>#ORD-<?= $id; ?></strong></td>
                                <td><?= htmlspecialchars($row['nama'] ?? 'Pelanggan'); ?></td>
                                <td><?= $tgl; ?></td>
                                <td style="color:#E50914; font-weight:600;">Rp <?= number_format($total, 0, ',', '.'); ?></td>
                                <td><span class="status-badge menunggu"><?= $status; ?></span></td>
                                <td style="text-align: center;"><a href="update_status.php?id=<?= $id; ?>" class="btn-edit-status"><i class="fa-solid fa-pen"></i></a></td>
                            </tr>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <tr>
                                <td><strong>#ORD-0001</strong></td>
                                <td>Diva Syafira</td>
                                <td>24 Mei 2026</td>
                                <td style="color:#E50914; font-weight:600;">Rp 448.000</td>
                                <td><span class="status-badge selesai">Selesai</span></td>
                                <td style="text-align: center;"><a href="#" class="btn-edit-status"><i class="fa-solid fa-pen"></i></a></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
