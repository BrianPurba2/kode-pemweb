<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

$nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Diva';

if (isset($_GET['hapus'])) {
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
    <title>Keranjang Belanja - Stranger Merch Store</title>
    <link rel="stylesheet" href="https://cloudflare.com">
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; min-height: 100vh; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 15px 6%; background: rgba(10, 2, 2, 0.95); border-bottom: 2px solid #E50914; position: sticky; top: 0; z-index: 100; }
        .navbar .logo { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-decoration: none; font-weight: 700; }
        .nav-links { display: flex; list-style: none; gap: 25px; align-items: center; }
        .nav-links a { color: #ffffff; text-decoration: none; font-size: 0.85rem; font-weight: 500; text-transform: uppercase; }
        .nav-links a.active { color: #E50914; font-weight: 600; }
        .cart-wrapper { display: grid; grid-template-columns: 1.2fr 1fr; gap: 35px; margin-top: 10px; padding: 40px 6%; }
        .cart-section { border: 2px solid #E50914; border-radius: 12px; padding: 20px; background: rgba(10,2,2,0.6); }
        .cart-item { display: flex; align-items: center; gap: 20px; padding: 15px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.08); }
        .item-details { flex: 1; }
        .qty-control { display: flex; align-items: center; background: #110404; border: 1px solid rgba(255,255,255,0.2); border-radius: 4px; overflow: hidden; width: max-content; }
        .qty-btn { background: none; border: none; color: white; width: 28px; height: 28px; cursor: pointer; }
        .qty-val { background: none; border: none; color: white; width: 35px; text-align: center; }
        .btn-delete-link { color: #FF5722; font-size: 1.1rem; text-decoration: none; }
        .summary-section { border: 2px solid rgba(255,255,255,0.15); border-radius: 12px; padding: 25px; background: rgba(10,2,2,0.6); }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 0.85rem; }
        .summary-input { width: 100%; padding: 10px; background: #110404; border: 1px solid rgba(255,255,255,0.15); border-radius: 6px; color: white; margin-top: 5px; }
        .btn-checkout { display: block; width: 100%; padding: 12px; background: #E50914; color: white; text-align: center; text-decoration: none; font-weight: 700; border-radius: 6px; margin-top: 20px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="home.php" class="logo">STRANGER MERCH STORE</a>
        <ul class="nav-links">
            <li><a href="home.php">HOME</a></li>
            <li><a href="produk.php">PRODUK</a></li>
            <li><a href="riwayat.php">RIWAYAT PESANAN</a></li>
            <li><a href="cart.php" class="active"><i class="fa-solid fa-basket-shopping"></i></a></li>
        </ul>
    </nav>
    <main class="cart-wrapper">
        <div>
            <h2 style="margin-bottom:20px;">Keranjang Belanja</h2>
            <div class="cart-section">
                <?php 
                $grand_total = 0; $total_item = 0;
                if (!empty($_SESSION['cart'])):
                    foreach ($_SESSION['cart'] as $id_produk => $jumlah): 
                        $res_prod = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id_produk'");
                        $prod = mysqli_fetch_assoc($res_prod);
                        if ($prod) { $grand_total += ($prod['harga'] * $jumlah); $total_item += $jumlah; }
                ?>
                    <div class="cart-item">
                        <div class="item-details">
                            <h4><?= htmlspecialchars($prod['nama_produk']); ?></h4>
                            <p style="font-size:0.8rem; color:#888;">Ukuran: M</p>
                            <form action="update_cart.php" method="POST" id="form-<?= $id_produk; ?>">
                                <input type="hidden" name="id_produk" value="<?= $id_produk; ?>">
                                <div class="qty-control">
                                    <button type="button" class="qty-btn" onclick="changeQty('<?= $id_produk; ?>', -1)">-</button>
                                    <input type="number" name="qty" id="qty-<?= $id_produk; ?>" value="<?= $jumlah; ?>" class="qty-val" readonly>
                                    <button type="button" class="qty-btn" onclick="changeQty('<?= $id_produk; ?>', 1)">+</button>
                                </div>
                            </form>
                        </div>
                        <a href="cart.php?hapus=<?= $id_produk; ?>" class="btn-delete-link"><i class="fa-solid fa-trash-can"></i></a>
                    </div>
                <?php 
                    endforeach;
                else: 
                ?>
                    <div class="cart-item">
                        <div class="item-details">
                            <h4>Hoodie Hawkins A.V. CLUB</h4>
                            <p style="font-size:0.8rem; color:#888;">Ukuran: M</p>
                            <div class="qty-control"><button class="qty-btn">-</button><input class="qty-val" value="1" readonly><button class="qty-btn">+</button></div>
                        </div>
                        <a href="#" class="btn-delete-link"><i class="fa-solid fa-trash-can"></i></a>
                    </div>
                <?php $grand_total = 428000; $total_item = 3; endif; ?>
            </div>
        </div>
        <div>
            <h2>Ringkasan Belanja</h2>
            <div class="summary-section">
                <div class="summary-row"><span>Subtotal (<?= $total_item; ?> Produk)</span><strong>Rp <?= number_format($grand_total, 0, ',', '.'); ?></strong></div>
                <div style="margin-bottom:15px;"><label>Isi Alamat</label><input type="text" class="summary-input" placeholder="Alamat kirim..."></div>
                <div class="summary-row" style="margin-top:20px; border-top:1px dashed #333; padding-top:15px;"><span>Total Belanja</span><strong style="color:#FF5722;">Rp <?= number_format($grand_total + 20000, 0, ',', '.'); ?></strong></div>
                <a href="checkout.php" class="btn-checkout">CHECKOUT</a>
            </div>
        </div>
    </main>
    <script>
    function changeQty(id, change) {
        var input = document.getElementById('qty-' + id);
        var val = parseInt(input.value) + change;
        if (val >= 0) { input.value = val; document.getElementById('form-' + id).submit(); }
    }
    </script>
</body>
</html>
