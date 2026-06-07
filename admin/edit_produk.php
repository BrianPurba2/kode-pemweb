<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

$id = isset($_GET['id']) ? $_GET['id'] : '';

// Ambil data produk lama berdasarkan ID produk yang dipilih
$q_ambil = mysqli_query($conn, "SELECT * FROM produk WHERE id_produk='$id'");
$data = mysqli_fetch_assoc($q_ambil);

if (isset($_POST['edit_produk'])) {
    $nama_produk = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    $foto = $_FILES['foto']['name'];
    $tmp_name = $_FILES['foto']['tmp_name'];

    if (!empty($foto)) {
        // Jika admin mengunggah foto baru, ganti foto lama di folder
        if (file_exists("../assets/img/produk/" . $data['foto'])) {
            unlink("../assets/img/produk/" . $data['foto']);
        }
        move_uploaded_file($tmp_name, "../assets/img/produk/" . $foto);
        $query_update = "UPDATE produk SET nama_produk='$nama_produk', kategori='$kategori', harga='$harga', stok='$stok', foto='$foto' WHERE id_produk='$id'";
    } else {
        // Jika foto tidak diubah, pertahankan foto yang lama
        $query_update = "UPDATE produk SET nama_produk='$nama_produk', kategori='$kategori', harga='$harga', stok='$stok' WHERE id_produk='$id'";
    }

    if (mysqli_query($conn, $query_update)) {
        header("Location: data_produk.php");
        exit;
    } else {
        echo "Gagal memperbarui data produk!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Admin</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        body { background: #0a0202; color: #fff; font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; padding: 40px 0; }
        .form-card { background: rgba(15, 5, 5, 0.8); border: 1px solid rgba(229, 9, 20, 0.3); padding: 35px; border-radius: 8px; width: 100%; max-width: 500px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; color: #aaa; margin-bottom: 8px; text-transform: uppercase; }
        .form-group input, .form-group select { width: 100%; padding: 12px; background: #150a0a; border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 4px; }
        .btn-submit { padding: 12px 25px; background: #E50914; color: white; border: none; font-weight: 600; border-radius: 4px; cursor: pointer; width: 100%; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="form-card">
        <h2 style="margin-bottom: 25px; text-transform: uppercase; font-size: 1.3rem; border-left: 4px solid #E50914; padding-left: 10px;">Edit Data Produk</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="nama_produk" value="<?= $data['nama_produk']; ?>" required>
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="tshirt" <?= $data['kategori'] == 'tshirt' ? 'selected' : ''; ?>>T-Shirt (Kaos)</option>
                    <option value="hoodie" <?= $data['kategori'] == 'hoodie' ? 'selected' : ''; ?>>Hoodie (Jaket)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" value="<?= $data['harga']; ?>" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $data['stok']; ?>" required>
            </div>
            <div class="form-group">
                <label>Ganti Foto (Biarkan kosong jika tidak ingin diubah)</label>
                <input type="file" name="foto" accept="image/*" style="border:none; background:transparent; padding:0;">
            </div>
            <button type="submit" name="edit_produk" class="btn-submit">SIMPAN DATA BARU</button>
        </form>
    </div>
</body>
</html>