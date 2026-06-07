<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// Menghitung grand total dari keranjang belanja
$grand_total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $id_produk => $jumlah) {
        $q = mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk='$id_produk'");
        $prod = mysqli_fetch_assoc($q);
        if ($prod) {
            $grand_total += ($prod['harga'] * $jumlah);
        }
    }
} else {
    header("Location: cart.php");
    exit;
}

// Proses ketika tombol "KONFIRMASI PEMBELIAN" diklik
if (isset($_POST['proses_beli'])) {
    $id_user = $_SESSION['id_user'];
    
    // 1. Simpan ke tabel induk (pesanan)
    $q_order = "INSERT INTO pesanan (id_user, total_bayar, status) VALUES ('$id_user', '$grand_total', 'Lunas')";
    if (mysqli_query($conn, $q_order)) {
        $id_pesanan_baru = mysqli_insert_id($conn); // Mengambil ID transaksi yang baru masuk
        
        // 2. Simpan setiap item ke tabel anak (detail_pesanan)
        foreach ($_SESSION['cart'] as $id_produk => $jumlah) {
            $q_p = mysqli_query($conn, "SELECT harga FROM produk WHERE id_produk='$id_produk'");
            $p_data = mysqli_fetch_assoc($q_p);
            $sub = $p_data['harga'] * $jumlah;
            
            mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, subtotal) VALUES ('$id_pesanan_baru', '$id_produk', '$jumlah', '$sub')");
            
            // Potong stok produk secara otomatis
            mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id_produk='$id_produk'");
        }
        
        // 3. Kosongkan kembali keranjang belanja karena sudah dibeli
        unset($_SESSION['cart']);
        
        $sukses = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Stranger Things Store</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .checkout-card {
            background: rgba(15, 5, 5, 0.85); border: 1px solid rgba(229, 9, 20, 0.3);
            border-radius: 8px; padding: 40px; width: 100%; max-width: 500px; text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), 0 0 15px rgba(229, 9, 20, 0.1); backdrop-filter: blur(8px);
        }
        h2 { font-family: 'Cinzel Decorative', serif; color: #E50914; margin-bottom: 10px; letter-spacing: 1px; }
        p { color: #aaa; font-size: 0.95rem; margin-bottom: 30px; }
        .total-box { background: rgba(229, 9, 20, 0.1); border: 1px dashed #E50914; padding: 15px; border-radius: 4px; font-size: 1.3rem; font-weight: 600; color: #ffffff; margin-bottom: 30px; }
        .btn-confirm { display: block; width: 100%; padding: 12px; background: #E50914; color: white; border: none; font-weight: 600; font-size: 1rem; border-radius: 4px; cursor: pointer; box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4); transition: 0.3s; }
        .btn-confirm:hover { background: #b8070f; box-shadow: 0 0 15px rgba(229, 9, 20, 0.8); }
        .alert-success { background: rgba(40, 167, 69, 0.2); border: 1px solid #28a745; color: #99ff99; padding: 20px; border-radius: 6px; margin-bottom: 20px; }
        .btn-back { display: inline-block; margin-top: 20px; color: #aaa; text-decoration: none; font-size: 0.9rem; }
        .btn-back:hover { color: #E50914; }
    </style>
</head>
<body>

<div class="checkout-card">
    <?php if (isset($sukses)): ?>
        <div class="alert-success">
            <h3>Pemberitahuan</h3>
            <p style="color: #99ff99; margin-top: 10px; margin-bottom: 0;">Pembelian berhasil dikonfirmasi! Barang Anda sedang diproses oleh Nurul dan Diva.</p>
        </div>
        <a href="home.php" class="btn-confirm" style="text-decoration: none;">KEMBALI KE BERANDA</a>
    <?php else: ?>
        <h2>CHECKOUT</h2>
        <p>Konfirmasi tagihan belanjaan Anda</p>
        
        <div class="total-box">
            Total: Rp <?= number_format($grand_total, 0, ',', '.'); ?>
        </div>

        <form method="POST">
            <button type="submit" name="proses_beli" class="btn-confirm">KONFIRMASI PEMBELIAN</button>
        </form>
        <a href="cart.php" class="btn-back">◀ Kembali ke Keranjang</a>
    <?php endif; ?>
</div>

</body>
</html>