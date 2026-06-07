<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

if (isset($_POST['tambah_produk'])) {
    $nama_produk = $_POST['nama_produk'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    
    // Proses Upload Foto
    $foto = $_FILES['foto']['name'];
    $tmp_name = $_FILES['foto']['tmp_name'];
    
    // Tentukan folder tujuan penyimpanan gambar
    $folder_tujuan = "../assets/img/produk/";
    
    // Buat foldernya otomatis jika belum ada di komputer Anda
    if (!file_exists($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }

    // Pindahkan file dari memori sementara ke folder tujuan
    move_uploaded_file($tmp_name, $folder_tujuan . $foto);

    // Masukkan data ke database
    $query = "INSERT INTO produk (nama_produk, kategori, harga, stok, foto) 
              VALUES ('$nama_produk', '$kategori', '$harga', '$stok', '$foto')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: data_produk.php");
        exit;
    } else {
        $error_msg = "Gagal menambah produk!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Stranger Merch Store</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; display: flex; min-height: 100vh; }
        .sidebar { width: 260px; background: rgba(15, 5, 5, 0.95); border-right: 2px solid #E50914; padding: 30px 20px; display: flex; flex-direction: column; justify-content: space-between; }
        .sidebar .brand { font-family: 'Cinzel Decorative', serif; color: #E50914; font-size: 1.3rem; text-align: center; margin-bottom: 40px; text-shadow: 0 0 10px rgba(229, 9, 20, 0.6); }
        .sidebar-menu { list-style: none; }
        .sidebar-menu li { margin-bottom: 15px; }
        .sidebar-menu a { display: block; color: #bbb; text-decoration: none; padding: 12px 15px; border-radius: 4px; font-size: 0.95rem; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(229, 9, 20, 0.15); color: #ffffff; border-left: 4px solid #E50914; font-weight: 600; }
        
        .main-content { flex: 1; padding: 40px 4%; }
        .header { margin-bottom: 35px; }
        .header h2 { border-left: 4px solid #E50914; padding-left: 10px; font-size: 1.6rem; text-transform: uppercase; }
        
        /* Form Box */
        .form-card { background: rgba(15, 5, 5, 0.6); border: 1px solid rgba(229, 9, 20, 0.2); border-radius: 8px; padding: 35px; max-width: 600px; box-shadow: 0 8px 32px rgba(0,0,0,0.5); }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 0.85rem; color: #aaa; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input, .form-group select { width: 100%; padding: 12px; background: rgba(30, 10, 10, 0.5); border: 1px solid rgba(255,255,255,0.1); border-radius: 4px; color: white; font-size: 0.95rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #E50914; box-shadow: 0 0 8px rgba(229, 9, 20, 0.4); }
        
        .btn-submit { padding: 12px 25px; background: #E50914; color: white; border: none; font-weight: 600; border-radius: 4px; cursor: pointer; box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4); transition: 0.3s; }
        .btn-submit:hover { background: #b8070f; box-shadow: 0 0 15px rgba(229, 9, 20, 0.8); }
        .btn-cancel { display: inline-block; margin-left: 15px; color: #aaa; text-decoration: none; font-size: 0.9rem; }
        .btn-cancel:hover { color: #ffffff; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div>
            <div class="brand">HAWKINS LAB</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">DASHBOARD</a></li>
                <li><a href="data_produk.php" class="active">DATA PRODUK</a></li>
                <li><a href="pesanan.php">PESANAN MASUK</a></li>
            </ul>
        </div>
        <ul class="sidebar-menu"><li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li></ul>
    </aside>

    <main class="main-content">
        <div class="header"><h2>Tambah Produk Baru</h2></div>

        <div class="form-card">
            <!-- Atribut enctype="multipart/form-data" WAJIB ada agar input file/foto bisa bekerja -->
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Nama Produk / Baju</label>
                    <input type="text" name="nama_produk" placeholder="Contoh: Kaos Hellfire Club Black" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="tshirt">T-Shirt (Kaos)</option>
                        <option value="hoodie">Hoodie (Jaket)</option>
                        <option value="mug">Mug (Cangkir)</option>
                        <option value="had">Had (Topi)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Harga (Rupiah)</label>
                    <input type="number" name="harga" placeholder="Contoh: 150000" required>
                </div>
                <div class="form-group">
                    <label>Stok Barang</label>
                    <input type="number" name="stok" placeholder="Contoh: 50" required>
                </div>
                <div class="form-group">
                    <label>Foto Produk</label>
                    <input type="file" name="foto" accept="image/*" required style="border: none; background: transparent; padding-left: 0;">
                </div>
                
                <button type="submit" name="tambah_produk" class="btn-submit">SIMPAN KE ETALASE</button>
                <a href="data_produk.php" class="btn-cancel">Batal</a>
            </form>
        </div>
    </main>

</body>
</html>