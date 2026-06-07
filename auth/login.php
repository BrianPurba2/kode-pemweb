<?php
session_start();

include '../config/koneksi.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data user berdasarkan email
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    // Cek apakah email ada
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Cek password (menggunakan password_verify jika di-hash)
        if (password_verify($password, $user['password']) || $password == $user['password']) {
            // Simpan session
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            // Cek role untuk pengalihan halaman
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                header("Location: ../user/home.php");
                exit;
            }
        } else {
            $error_msg = "Password salah!";
        }
    } else {
        $error_msg = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stranger Things Store</title>
    <!-- Menggunakan Font Khas Bertema Horror/Gothic -->
    <link href="https://googleapis.com" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            /* Latar belakang merah gelap gradasi hitam khas Stranger Things */
            background: radial-gradient(circle, #2a0808 0%, #0a0202 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        /* Efek kabut merah samar di latar belakang */
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

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 400px;
            padding: 20px;
            text-align: center;
        }

        /* Desain Judul Utama ala Logo Stranger Things */
        .brand-title {
            font-family: 'Cinzel Decorative', serif;
            color: #E50914;
            font-size: 2rem;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 10px rgba(229, 9, 20, 0.8), 0 0 20px rgba(229, 9, 20, 0.4);
            line-height: 1.2;
        }

        /* Kotak Form Login Hitam Transparan (Card) */
        .login-card {
            background: rgba(15, 5, 5, 0.75);
            border: 1px solid rgba(229, 9, 20, 0.3);
            border-radius: 8px;
            padding: 40px 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5), 0 0 15px rgba(229, 9, 20, 0.1);
            backdrop-filter: blur(8px);
        }

        .login-card h3 {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 25px;
            letter-spacing: 1px;
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

        /* Tombol Login Merah Menyala */
        .btn-login {
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
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #b8070f;
            box-shadow: 0 0 15px rgba(229, 9, 20, 0.8);
            transform: translateY(-1px);
        }

        /* Alert Pesan Error */
        .alert-error {
            background: rgba(229, 9, 20, 0.2);
            border: 1px solid #E50914;
            color: #ff9999;
            padding: 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }

        .register-link {
            margin-top: 20px;
            font-size: 0.85rem;
            color: #aaaaaa;
        }

        .register-link a {
            color: #E50914;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <!-- Judul Web Sesuai Desain Figma -->
    <div class="brand-title">Stranger<br>Things Store Punya Nurul Diva</div>

    <div class="login-card">
        <h3>MASUKKAN DATA DIRI</h3>

        <!-- Memunculkan Notifikasi Jika Gagal Login -->
        <?php if (isset($error_msg)): ?>
                <div class="alert-error"><?= $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <input type="email" name="email" placeholder="Email Address" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="btn-login">LOGIN</button>
        </form>

        <div class="register-link">
            Belum punya akun? <a href="register.php">Buat akun baru</a>
        </div>
    </div>
</div>

</body>
</html>