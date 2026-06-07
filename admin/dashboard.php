<?php
session_start();
// Kunci keamanan: Hanya user ber-role 'admin' yang boleh masuk halaman ini
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

// 1. Hitung total produk di etalase
$q_prod = mysqli_query($conn, "SELECT COUNT(*) as total FROM produk");
$res_prod = mysqli_fetch_assoc($q_prod);

// 2. Hitung total pelanggan/user terdaftar
$q_user = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'");
$res_user = mysqli_fetch_assoc($q_user);

// 3. Hitung total uang masuk (omset penjualan)
$q_omset = mysqli_query($conn, "SELECT SUM(total_bayar) as total FROM pesanan");
$res_omset = mysqli_fetch_assoc($q_omset);
$total_omset = $res_omset['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Stranger Things Store</title>
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; display: flex; min-height: 100vh; }
        
        /* Sidebar Menu Navigasi Admin */
        .sidebar {
            width: 260px; background: rgba(15, 5, 5, 0.95);
            border-right: 2px solid #E50914; padding: 30px 20px;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .sidebar .brand {
            font-family: 'Cinzel Decorative', serif; color: #E50914;
            font-size: 1.3rem; text-align: center; margin-bottom: 40px;
            text-shadow: 0 0 10px rgba(229, 9, 20, 0.6);
        }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a {
            display: block; color: #bbb; text-decoration: none; padding: 12px 15px;
            border-radius: 4px; font-size: 0.95rem; transition: all 0.3s;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(229, 9, 20, 0.15); color: #ffffff;
            border-left: 4px solid #E50914; font-weight: 600;
        }

        /* Konten Utama */
        .main-content { flex: 1; padding: 40px 4%; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        .header h2 { border-left: 4px solid #E50914; padding-left: 10px; font-size: 1.6rem; letter-spacing: 1px; }
        .admin-profile { font-size: 0.95rem; color: #aaa; }
        .admin-profile strong { color: #E50914; }

        /* Grid Kotak Statistik Seperti Desain Figma */
        .stats-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px; margin-bottom: 40px;
        }
        .stat-card {
            background: rgba(15, 5, 5, 0.8); border: 1px solid rgba(229, 9, 20, 0.2);
            border-radius: 8px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }
        .stat-card h3 { font-size: 0.85rem; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .stat-card .number { font-size: 2rem; font-weight: 600; color: #ffffff; }
        .stat-card .number.highlight { color: #E50914; text-shadow: 0 0 10px rgba(229, 9, 20, 0.3); }

        /* Tabel Transaksi Terakhir */
        .section-title { font-size: 1.2rem; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 0.5px; }
        .data-table {
            width: 100%; border-collapse: collapse; background: rgba(15, 5, 5, 0.5);
            border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden;
        }
        .data-table th, .data-table td { padding: 12px 15px; text-align: left; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .data-table th { background: rgba(229, 9, 20, 0.1); color: #E50914; font-weight: 600; }
        .badge-success { background: rgba(40, 167, 69, 0.2); color: #99ff99; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; border: 1px solid #28a745; }
        .no-data { color: #666; text-align: center; padding: 30px 0; }
    </style>
</head>
<body>

    <!-- Sidebar Menu -->
    <aside class="sidebar">
        <div>
            <div class="brand">Nurul dan Diva</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">DASHBOARD</a></li>
                <li><a href="data_produk.php">DATA PRODUK</a></li>
                <li><a href="data_pesanan.php">PESANAN MASUK</a></li>
            </ul>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
        </ul>
    </aside>

    <!-- Konten Dashboard -->
    <main class="main-content">
        <div class="header">
            <h2>OVERVIEW</h2>
            <div class="admin-profile">Login Sebagai: <strong><?= $_SESSION['nama']; ?></strong> (Admin)</div>
        </div>

        <!-- Tiga Kotak Statistik Utama -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Produk</h3>
                <div class="number"><?= $res_prod['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Pelanggan</h3>
                <div class="number"><?= $res_user['total']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Pendapatan</h3>
                <div class="number highlight">Rp <?= number_format($total_omset, 0, ',', '.'); ?></div>
            </div>
        </div>

        <!-- Tabel Riwayat Pesanan yang Masuk -->
        <h3 class="section-title">Aktivitas Pesanan Terakhir</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Nama Pembeli</th>
                    <th>Tanggal</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th>Aksi</th> <!-- Tambahkan ini-->
                </tr>
            </thead>
            <tbody>
                <?php
                // Mengambil data pesanan digabung dengan nama user pembelinya
                $q_orders = "SELECT pesanan.*, users.nama FROM pesanan JOIN users ON pesanan.id_user = users.id_user ORDER BY pesanan.id_pesanan DESC LIMIT 5";
                $res_orders = mysqli_query($conn, $q_orders);

                if (mysqli_num_rows($res_orders) > 0):
                    while ($order = mysqli_fetch_assoc($res_orders)):
                        ?>
                        <tr>
                            <td>#TRX-00<?= $order['id_pesanan']; ?></td>
                            <td><?= $order['nama']; ?></td>
                            <td><?= date('d M Y, H:i', strtotime($order['tanggal'])); ?> WIB</td>
                            <td style="color: #E50914; font-weight: 600;">Rp <?= number_format($order['total_bayar'], 0, ',', '.'); ?></td>
                            <td><span class="badge-success"><?= $order['status']; ?></span></td>
                            <td>
                                 <a href="update_status.php?id=<?= $order['id_pesanan']; ?>" style="color: #ffc107; text-decoration: none; font-weight: 600; margin-right: 10px; font-size: 0.85rem;">Ubah</a>
                                 <a href="dashboard.php?hapus_trx=<?= $order['id_pesanan']; ?>" style="color: #ff4444; text-decoration: none; font-weight: 600; font-size: 0.85rem;" onclick="return confirm('Hapus permanen transaksi ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php
                    endwhile;
                else:
                    ?>
                    <tr>
                        <td colspan="5" class="no-data">Belum ada transaksi pembelian masuk dari customer.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

</body>
</html>