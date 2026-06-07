<?php
include '../config/koneksi.php';

if (isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Enkripsi password menggunakan bcrypt standar PHP
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Query insert (Kolom role otomatis terisi 'user' secara default sesuai struktur MySQL kita)
    $query = "INSERT INTO users (nama, email, password) VALUES ('$nama', '$email', '$hash')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $success_msg = "Registrasi berhasil! Silakan login.";
    } else {
        $error_msg = "Registrasi gagal! Email mungkin sudah terdaftar.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Stranger Things Store</title>
    <!-- Mengambil font horror dan font teks dari Google Fonts -->
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Latar belakang merah gelap gradasi hitam sesuai tema figma */
            background: radial-gradient(circle, #2a0808 0%, #0a0202 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Efek kilatan lampu/kabut merah di latar belakang */
        body::before {
            content: '';
            position: absolute;
            width: 150%;
            height: 150%;
            background: radial-gradient(circle, rgba(229, 9, 20, 0.15) 0%, transparent 60%);
            top: -25%;
            left: -25%;
            z-index: 1;
            pointer-events: none;
        }

        .register-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            text-align: center;
        }

        /* Judul teks merah menyala bergaya gothic logo Stranger Things */
        .brand-title {
            font-family: 'Cinzel Decorative', serif;
            color: #E50914;
            font-size: 2rem;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(229, 9, 20, 0.8), 0 0 20px rgba(229, 9, 20, 0.4);
            line-height: 1.2;
        }

        /* Card form transparan efek blur kaca kegelapan */
        .register-card {
            background: rgba(15, 5, 5, 0.75);
            border: 1px solid rgba(229, 9, 20, 0.3);
            border-radius: 8px;
            padding: 35px 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), 0 0 15px rgba(229, 9, 20, 0.1);
            backdrop-filter: blur(8px);
        }

        .register-card h3 {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }

        .register-card p {
            color: #888888;
            font-size: 0.85rem;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            background: rgba(30, 10, 10, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            color: #ffffff;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #E50914;
            box-shadow: 0 0 8px rgba(229, 9, 20, 0.5);
            background: rgba(40, 10, 10, 0.8);
        }

        /* Tombol daftar warna merah/oranye menyala */
        .btn-register {
            width: 100%;
            padding: 12px;
            background: #E50914;
            border: none;
            border-radius: 4px;
            color: #ffffff;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(229, 9, 20, 0.4);
            margin-top: 5px;
        }

        .btn-register:hover {
            background: #b8070f;
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.8);
            transform: translateY(-1px);
        }

        /* Notifikasi sukses/gagal */
        .alert-error {
            background: rgba(229, 9, 20, 0.2);
            border: 1px solid #E50914;
            color: #ff9999;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #99ff99;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .login-link {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #aaaaaa;
        }

        .login-link a {
            color: #E50914;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        /* Tombol kembali di pojok kiri atas form */
        .back-btn {
            display: block;
            text-align: left;
            color: #888888;
            font-size: 0.85rem;
            text-decoration: none;
            margin-bottom: 15px;
            transition: color 0.3s ease;
        }

        .back-btn:hover {
            color: #E50914;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="brand-title">Stranger<br>Things Store Punya Nurul Diva</div>

    <div class="register-card">
        <a href="login.php" class="back-btn">◀ Kembali</a>
        
        <h3>BUAT AKUN BARU</h3>
        <p>Bergabung sebagai Anak Nurul Diva</p>

        <!-- Memunculkan Notifikasi Alert Berwarna -->
        <?php if (isset($error_msg)): ?>
            <div class="alert-error"><?= $error_msg; ?></div>
        <?php endif; ?>
        <?php if (isset($success_msg)): ?>
            <div class="alert-success"><?= $success_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="nama" placeholder="Full Name" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="register" class="btn-register">REGISTRASI</button>
        </form>

        <div class="login-link">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </div>
    </div>
</div>

</body>
</html>