<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

// Menangkap id_pesanan dari URL (?id=...)
$id_pesanan = isset($_GET['id']) ? $_GET['id'] : '';

// Jika tidak ada ID pesanan, kembalikan ke halaman pesanan masuk
if (empty($id_pesanan)) {
    header("Location: data_pesanan.php");
    exit;
}

// Query mengambil data induk pesanan beserta nama pelanggan
$q_order = "SELECT pesanan.*, users.nama FROM pesanan 
            JOIN users ON pesanan.id_user = users.id_user 
            WHERE pesanan.id_pesanan = '$id_pesanan'";
$res_order = mysqli_query($conn, $q_order);
$order = mysqli_fetch_assoc($res_order);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan - Admin</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        body { background: #0a0202; color: #fff; font-family: 'Poppins', sans-serif; padding: 40px 8%; }
        .card { background: rgba(15, 5, 5, 0.8); border: 1px solid rgba(229, 9, 20, 0.3); padding: 35px; border-radius: 8px; max-width: 700px; margin: 0 auto; box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
        h2 { border-left: 4px solid #E50914; padding-left: 10px; font-size: 1.4rem; margin-bottom: 25px; text-transform: uppercase; letter-spacing: 0.5px; }
        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px; font-size: 0.95rem; color: #ccc; }
        .meta-grid strong { color: #fff; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.05); }
        .data-table th, .data-table td { padding: 12px; text-align: left; font-size: 0.9rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .data-table th { background: rgba(229, 9, 20, 0.1); color: #E50914; }
        
        .total-row { font-size: 1.1rem; font-weight: 600; color: #E50914; text-align: right; margin-top: 25px; }
        .btn-back { display: inline-block; margin-top: 25px; color: #aaa; text-decoration: none; font-size: 0.9rem; }
        .btn-back:hover { color: #E50914; }
        .btn-update { display: inline-block; padding: 6px 12px; background: #E50914; color: #fff; text-decoration: none; border-radius: 4px; font-size: 0.85rem; font-weight: 600; margin-left: 10px; }
        .btn-update:hover { background: #b8070f; }
    </style>
</head>
<body>

    <div class="card">
        <a href="pesanan.php" class="btn-back">◀ Kembali ke Pesanan Masuk</a>
        <h2 style="margin-top: 15px;">Rincian Nota Belanja</h2>
        
        <div class="meta-grid">
            <div>ID Invoice: <strong>#TRX-00<?= $order['id_pesanan']; ?></strong></div>
            <div>Nama Pelanggan: <strong><?= $order['nama']; ?></strong></div>
            <div>Tanggal Transaksi: <strong><?= date('d M Y, H:i', strtotime($order['tanggal'])); ?> WIB</strong></div>
            <div>Status Pengiriman: <strong style="color: #99ff99;"><?= $order['status']; ?></strong>
                <a href="update_status.php?id=<?= $order['id_pesanan']; ?>" class="btn-update">Ubah Status</a>
            </div>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah Beli</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query anak mengambil daftar barang belanjaan dari tabel 'detail_pesanan'
                $q_items = "SELECT detail_pesanan.*, produk.nama_produk FROM detail_pesanan 
                            JOIN produk ON detail_pesanan.id_produk = produk.id_produk 
                            WHERE detail_pesanan.id_pesanan = '$id_pesanan'";
                $res_items = mysqli_query($conn, $q_items);
                while ($item = mysqli_fetch_assoc($res_items)):
                ?>
                <tr>
                    <td><strong><?= $item['nama_produk']; ?></strong></td>
                    <td>Rp <?= number_format($item['subtotal'] / $item['jumlah'], 0, ',', '.'); ?></td>
                    <td><?= $item['jumlah']; ?> Pcs</td>
                    <td style="color: #E50914; font-weight: 600;">Rp <?= number_format($item['subtotal'], 0, ',', '.'); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="total-row">
            Total Pembayaran: Rp <?= number_format($order['total_bayar'], 0, ',', '.'); ?>
        </div>
    </div>

</body>
</html>