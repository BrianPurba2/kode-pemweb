<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

// Menangkap ID pesanan dari URL
$id_pesanan = isset($_GET['id']) ? $_GET['id'] : '';

if (isset($_POST['update'])) {
    $status = $_POST['status'];

    // Update status pesanan di database (Lunas / Diantar / Selesai)
    $update = "UPDATE pesanan SET status='$status' WHERE id_pesanan='$id_pesanan'";
    $hasil = mysqli_query($conn, $update);

    if ($hasil) {
        // Dilempar kembali ke halaman pesanan masuk
        header("Location: detail_pesanan.php");
        exit;
    } else {
        echo "Status gagal diupdate!";
    }
}

// Ambil data lama untuk ditampilkan di form
$query = "SELECT * FROM pesanan WHERE id_pesanan='$id_pesanan'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Status - Admin</title>
    <link href="https://googleapis.com" rel="stylesheet">
    <style>
        body { background: #0a0202; color: #fff; font-family: 'Poppins', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: rgba(15, 5, 5, 0.8); border: 1px solid rgba(229, 9, 20, 0.3); padding: 30px; border-radius: 8px; width: 100%; max-width: 400px; text-align: center; }
        select { width: 100%; padding: 12px; background: #150a0a; border: 1px solid rgba(255,255,255,0.1); color: white; border-radius: 4px; margin: 20px 0; font-size: 1rem; }
        .btn { width: 100%; padding: 12px; background: #E50914; border: none; color: white; font-weight: 600; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #b8070f; }
    </style>
</head>
<body>
    <div class="card">
        <h3>UPDATE STATUS PESANAN</h3>
        <p style="color: #aaa; font-size: 0.9rem; margin-top: 5px;">ID Invoice: #TRX-00<?= $data['id_pesanan']; ?></p>
        <form method="POST">
            <select name="status">
                <option value="Pending" <?= $data['status'] == 'Pending' ? 'selected' : ''; ?>>Pending (Menunggu)</option>
                <option value="Diantar" <?= $data['status'] == 'Diantar' ? 'selected' : ''; ?>>Diantar (Proses Kirim)</option>
                <option value="Selesai" <?= $data['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai (Diterima)</option>
            </select>
            <button type="submit" name="update" class="btn">SIMPAN PERUBAHAN</button>
        </form>
    </div>
</body>
</html>