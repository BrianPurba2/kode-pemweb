<?php
session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login_admin.php");
    exit;
}
include '../config/koneksi.php';

$id_hapus = isset($_GET['id']) ? $_GET['id'] : '';

if (!empty($id_hapus)) {
    // 1. Ambil info foto lama dari database agar file fotonya ikut terhapus di folder komputer
    $q_foto = mysqli_query($conn, "SELECT foto FROM produk WHERE id_produk='$id_hapus'");
    $data_foto = mysqli_fetch_assoc($q_foto);
    if (!empty($data_foto['foto']) && file_exists("../assets/img/produk/" . $data_foto['foto'])) {
        unlink("../assets/img/produk/" . $data_foto['foto']);
    }

    // 2. Eksekusi hapus baris data di MySQL
    mysqli_query($conn, "DELETE FROM produk WHERE id_produk='$id_hapus'");
}

// Kembalikan Admin secara otomatis ke halaman daftar barang
header("Location: data_produk.php");
exit;
?>