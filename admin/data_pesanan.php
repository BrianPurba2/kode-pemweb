<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

// Ambil semua data pesanan diurutkan dari transaksi terbaru yang masuk
$query = "SELECT pesanan.*, users.nama FROM pesanan 
          JOIN users ON pesanan.id_user = users.id_user 
          ORDER BY pesanan.id_pesanan DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Incoming Orders - Stranger Merch Store</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: rgba(15, 5, 5, 0.95); border-right: 2px solid #E50914; padding: 30px 20px; display: flex; flex-direction: column; justify-content: space-between; }
        .sidebar .brand { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-align: center; text-shadow: 0 0 10px rgba(229, 9, 20, 0.6); }
        .sidebar-menu { list-style: none; margin-top: 40px; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a { display: block; color: #bbb; text-decoration: none; padding: 12px 15px; border-radius: 4px; font-size: 0.95rem; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(229, 9, 20, 0.15); color: #ffffff; border-left: 4px solid #E50914; font-weight: 600; }
        
        .main-content { flex: 1; padding: 40px 4%; }
        .header h2 { border-left: 4px solid #E50914; padding-left: 10px; font-size: 1.6rem; text-transform: uppercase; margin-bottom: 35px; }
        
        .data-table { width: 100%; border-collapse: collapse; background: rgba(15, 5, 5, 0.5); border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5); }
        .data-table th, .data-table td { padding: 15px; text-align: left; font-size: 0.95rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .data-table th { background: rgba(229, 9, 20, 0.1); color: #E50914; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; }
        .badge-success { background: rgba(40, 167, 69, 0.2); color: #99ff99; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; border: 1px solid #28a745; }
        .no-data { color: #666; text-align: center; padding: 40px 0; }
        
        /* Dropdown Detail List Item */
        .items-list { font-size: 0.85rem; color: #aaa; list-style-position: inside; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="brand">Nurul dan Diva</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">DASHBOARD</a></li>
                <li><a href="data_produk.php">DATA PRODUK</a></li>
                <li><a href="detail_pesanan.php" class="active">PESANAN MASUK</a></li>
            </ul>
        </div>
        <ul class="sidebar-menu"><li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li></ul>
    </aside>

    <main class="main-content">
        <div class="header"><h2>Pesanan Masuk</h2></div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Invoice</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal Pembelian</th>
                    <th>Detail Barang Belanjaan</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><a href="detail_pesanan.php?id=<?= $row['id_pesanan']; ?>" style="color: #ffc107; text-decoration: none; font-weight: 600;"></a>#TRX-00<?= $row['id_pesanan']; ?></a></td>
                        <td><?= $row['nama']; ?></td>
                        <td><?= date('d M Y, H:i', strtotime($row['tanggal'])); ?> WIB</td>
                        <td>
                            <ul class="items-list">
                            <?php
                            // Ambil list barang belanjaan dari detail_pesanan berdasarkan ID pesanan baris ini
                            $id_p = $row['id_pesanan'];
                            $q_detail = "SELECT detail_pesanan.*, produk.nama_produk FROM detail_pesanan 
                                         JOIN produk ON detail_pesanan.id_produk = produk.id_produk 
                                         WHERE detail_pesanan.id_pesanan = '$id_p'";
                            $res_detail = mysqli_query($conn, $q_detail);
                            while ($item = mysqli_fetch_assoc($res_detail)) {
                                echo "<li>" . $item['nama_produk'] . " (" . $item['jumlah'] . " Pcs)</li>";
                            }
                            ?>
                            </ul>
                        </td>
                        <td style="color: #E50914; font-weight: 600;">Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                        <td><span class="badge-success"><?= $row['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="no-data">Belum ada invoice pesanan masuk dari pembeli.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

</body>
</html>