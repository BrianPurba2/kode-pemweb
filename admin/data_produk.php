<?php
session_start();
// Kunci keamanan: Hanya user ber-role 'admin' yang boleh masuk
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

// Proses Hapus Produk jika tombol hapus diklik
if (isset($_GET['hapus'])) {
    $id_hapus = $_GET['hapus'];
    
    // Ambil nama file foto lama agar tidak menyampah di folder hosting/komputer
    $q_foto = mysqli_query($conn, "SELECT foto FROM produk WHERE id_produk='$id_hapus'");
    $data_foto = mysqli_fetch_assoc($q_foto);
    if (!empty($data_foto['foto']) && file_exists("../assets/img/produk/" . $data_foto['foto'])) {
        unlink("../assets/img/produk/" . $data_foto['foto']); // Hapus file foto dari folder
    }

    // Hapus data dari database
    mysqli_query($conn, "DELETE FROM produk WHERE id_produk='$id_hapus'");
    header("Location: data_produk.php");
    exit;
}

// Ambil semua data produk dari database
$query = "SELECT * FROM produk ORDER BY id_produk DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Stranger Merch Store</title>
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #0a0202; color: #ffffff; display: flex; min-height: 100vh; }
        
        /* Sidebar Menu Navigasi Admin (Sama dengan Dashboard) */
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
        
        /* Tombol Tambah Produk */
        .btn-add {
            display: inline-block; padding: 10px 20px; background: #E50914;
            color: white; text-decoration: none; font-weight: 600; font-size: 0.9rem;
            border-radius: 4px; box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4); transition: 0.3s;
        }
        .btn-add:hover { background: #b8070f; box-shadow: 0 0 15px rgba(229, 9, 20, 0.7); }

        /* Tabel Data Produk */
        .data-table {
            width: 100%; border-collapse: collapse; background: rgba(15, 5, 5, 0.5);
            border: 1px solid rgba(255,255,255,0.05); border-radius: 6px; overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }
        .data-table th, .data-table td { padding: 15px; text-align: left; font-size: 0.95rem; border-bottom: 1px solid rgba(255,255,255,0.05); }
        .data-table th { background: rgba(229, 9, 20, 0.1); color: #E50914; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        
        /* Foto Produk Mini di Tabel */
        .prod-img { width: 50px; height: 50px; object-fit: contain; background: #150a0a; border-radius: 4px; padding: 3px; border: 1px solid rgba(255,255,255,0.05); }
        
        .badge-category { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; text-transform: uppercase; }
        
        /* Tombol Aksi */
        .btn-delete { color: #ff4444; text-decoration: none; font-weight: 600; margin-left: 10px; font-size: 0.9rem; }
        .btn-delete:hover { text-decoration: underline; }
        .no-data { color: #666; text-align: center; padding: 40px 0; }
       
        .btn-edit {
          color: #ffc107; /* Warna kuning emas */
          text-decoration: none;
          font-weight: 600;
          font-size: 0.9rem;
          margin-right: 12px; /* Jarak spasi ke tombol hapus */
        }
        .btn-edit:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <!-- Sidebar Menu Navigasi -->
    <aside class="sidebar">
        <div>
            <div class="brand">Nurul dan Diva</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">DASHBOARD</a></li>
                <li><a href="data_produk.php" class="active">DATA PRODUK</a></li>
                <li><a href="detail_pesanan.php">PESANAN MASUK</a></li>
            </ul>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../auth/logout.php" style="color: #ff4444;">LOGOUT</a></li>
        </ul>
    </aside>

    <!-- Konten Manajemen Produk -->
    <main class="main-content">
        <div class="header">
            <h2>DATA PRODUK</h2>
            <a href="tambah_produk.php" class="btn-add">+ TAMBAH PRODUK BARU</a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>
                            <!-- Menampilkan foto produk asli atau placeholder gambar kosong jika file tidak ada -->
                            <img src="../assets/img/produk/<?= $row['foto']; ?>" class="prod-img" onerror="this.src='https://placehold.co'">
                        </td>
                        <td><strong><?= $row['nama_produk']; ?></strong></td>
                        <td><span class="badge-category"><?= $row['kategori']; ?></span></td>
                        <td style="color: #E50914; font-weight: 600;">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                        <td><?= $row['stok']; ?> Pcs</td>
                        <td>
                            <!-- Link konfirmasi hapus menggunakan pop up konfirmasi bawaan browser -->
                            <a href="edit_produk.php?id=<?= $row['id_produk']; ?>" class="btn-edit">Edit</a>
                            <a href="data_produk.php?hapus=<?= $row['id_produk']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini dari Nurul Diva?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" class="no-data">Belum ada barang di etalase toko. Silakan klik tombol tambah di atas.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

</body>
</html>