<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// Menghapus item tertentu dari keranjang jika ada perintah 'hapus'
if (isset($_GET['hapus'])) {
    // Dipastikan aman dengan casting ke integer
    $id_hapus = intval($_GET['hapus']);
    unset($_SESSION['cart'][$id_hapus]);
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Stranger Merch Store</title>
    <!-- Perbaikan link Google Fonts -->
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
        }

        .nav-links a.active {
            color: #E50914;
            font-weight: 600;
        }

        .container {
            padding: 40px 8%;
        }

        .page-title {
            font-size: 1.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 30px;
            border-left: 4px solid #E50914;
            padding-left: 10px;
        }

        /* Desain Tabel Keranjang Hitam Transparan */
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background: rgba(15, 5, 5, 0.8);
            border: 1px solid rgba(229, 9, 20, 0.2);
            border-radius: 8px;
            overflow: hidden;
        }

        .cart-table th,
        .cart-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .cart-table th {
            background: rgba(229, 9, 20, 0.1);
            color: #E50914;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Form Input Jumlah Baru */
        .qty-form {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .qty-input {
            width: 60px;
            padding: 6px;
            background: #150505;
            color: #fff;
            border: 1px solid rgba(229, 9, 20, 0.4);
            border-radius: 4px;
            text-align: center;
        }

        .btn-update {
            padding: 6px 10px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: 0.2s;
        }

        .btn-update:hover {
            background: #E50914;
            border-color: #E50914;
        }

        .btn-delete {
            color: #ff4444;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .btn-delete:hover {
            text-decoration: underline;
        }

        /* Ringkasan Belanja Total */
        .cart-summary {
            background: rgba(30, 10, 10, 0.4);
            border: 1px solid rgba(229, 9, 20, 0.3);
            border-radius: 8px;
            padding: 25px;
            max-width: 400px;
            margin-left: auto;
            text-align: right;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .total-price {
            color: #E50914;
            font-weight: 600;
        }

        .btn-checkout {
            display: inline-block;
            width: 100%;
            padding: 12px;
            background: #E50914;
            color: white;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4);
            transition: all 0.3s;
        }

        .btn-checkout:hover {
            background: #b8070f;
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.7);
        }

        .empty-message {
            text-align: center;
            padding: 50px;
            color: #888888;
        }

        .empty-message a {
            color: #E50914;
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER THINGS</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="cart.php" class="active">KERANJANG</a></li>
            <li><a href="kategori.php">KATEGORI</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="tentang.php">TENTANG KAMI</a></li>
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
        </ul>
    </nav>

    <main class="container">
        <h2 class="page-title">Keranjang Belanja</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th>Jumlah</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    foreach ($_SESSION['cart'] as $id_produk => $jumlah):
                        // Ambil detail info produk dari database berdasarkan ID di session
                        $q_prod = "SELECT * FROM produk WHERE id_produk='$id_produk'";
                        $res_prod = mysqli_query($conn, $q_prod);
                        $prod = mysqli_fetch_assoc($res_prod);

                        if ($prod) {
                            $subtotal = $prod['harga'] * $jumlah;
                            $grand_total += $subtotal;
                        } else {
                            continue; // Lewati jika produk tidak ditemukan di DB
                        }
                        ?>
                        <tr>
                            <td><strong><?= $prod['nama_produk']; ?></strong></td>
                            <td>Rp <?= number_format($prod['harga'], 0, ',', '.'); ?></td>

                            <!-- KOLOM JUMLAH DIUBAH MENJADI FORM UPDATE -->
                            <td>
                                <form action="update_cart.php" method="POST" class="qty-form">
                                    <input type="hidden" name="id_produk" value="<?= $id_produk; ?>">
                                    <input type="number" name="qty" value="<?= $jumlah; ?>" min="0" class="qty-input">
                                    <button type="submit" class="btn-update">Simpan</button>
                                </form>
                            </td>

                            <td style="color: #E50914; font-weight:600;">Rp <?= number_format($subtotal, 0, ',', '.'); ?></td>
                            <td><a href="cart.php?hapus=<?= $id_produk; ?>" class="btn-delete">Hapus</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Total Pembayaran:</span>
                    <span class="total-price">Rp <?= number_format($grand_total, 0, ',', '.'); ?></span>
                </div>
                <a href="checkout.php" class="btn-checkout">PROSES CHECKOUT (BELI)</a>
            </div>

        <?php else: ?>
            <div class="empty-message">
                <p>Keranjang belanja Anda masih kosong.</p>
                <p><a href="produk.php">Mulai cari baju atau hoodie keren di sini ◀</a></p>
            </div>
        <?php endif; ?>
    </main>

</body>

</html>