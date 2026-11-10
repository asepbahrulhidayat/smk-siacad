<?php
// ======================================
// SESSION DAN KONEKSI DATABASE
// ======================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "koneksi.php";

// ======================================
// CEK ROLE ADMIN
// ======================================
if (($_SESSION['role'] ?? '') !== 'admin') {
    die("Akses ditolak! Halaman ini hanya untuk admin.");
}

// ======================================
// PROSES SIMPAN USER
// ======================================
if (isset($_POST['simpan'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input
    if ($username === '' || $password === '') {
        echo "<script>
            alert('Username dan password wajib diisi!');
            window.history.back();
        </script>";
        exit;
    }

    // Enkripsi password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // ======================================
    // CEK USERNAME SUDAH ADA ATAU BELUM
    // ======================================
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id FROM users WHERE username = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);

    $hasil_cek = mysqli_stmt_get_result($cek);

    if (mysqli_num_rows($hasil_cek) > 0) {

        mysqli_stmt_close($cek);

        echo "<script>
            alert('Username sudah digunakan!');
            window.history.back();
        </script>";
        exit;
    }

    mysqli_stmt_close($cek);

    // ======================================
    // SIMPAN DATA USER
    // ======================================
    $stmt = mysqli_prepare(
        $koneksi,
        "INSERT INTO users (username, password)
         VALUES (?, ?)"
    );

    if (!$stmt) {
        die("Query gagal: " . htmlspecialchars(mysqli_error($koneksi)));
    }

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $username,
        $password_hash
    );

    if (mysqli_stmt_execute($stmt)) {

        mysqli_stmt_close($stmt);

        echo "<script>
            alert('Data user berhasil ditambahkan!');
            window.location.href = 'daftar_user.php';
        </script>";
        exit;

    } else {

        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);

        die("Gagal menyimpan data: " . htmlspecialchars($error));
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Tambah User - SIACAD SMK</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f7fb;
        }

        .form-container {
            max-width: 650px;
            margin: 45px auto;
            padding: 0 15px;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        }

        .form-title {
            font-weight: 700;
            color: #20314d;
        }

        .form-control {
            padding: 11px;
            border-radius: 8px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
        }
    </style>

</head>

<body>

    <!-- NAVBAR -->
    <?php require 'navbar.php'; ?>

    <div class="form-container">

        <div class="form-card">

            <div class="text-center mb-4">

                <h3 class="form-title">
                    Form Tambah User
                </h3>

                <p class="text-muted">
                    Tambahkan akun pengguna baru SIACAD SMK
                </p>

            </div>

            <form method="POST" action="">

                <!-- USERNAME -->
                <div class="mb-3">

                    <label class="form-label">
                        Username Akun
                    </label>

                    <input
                        type="text"
                        name="username"
                        class="form-control"
                        placeholder="Contoh: ujian123"
                        required
                    >

                </div>

                <!-- PASSWORD -->
                <div class="mb-4">

                    <label class="form-label">
                        Password Akun
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        required
                    >

                </div>

                <!-- TOMBOL -->
                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-primary"
                    >
                        Simpan Data
                    </button>

                    <a
                        href="daftar_user.php"
                        class="btn btn-secondary"
                    >
                        Kembali
                    </a>

                </div>

            </form>

        </div>

    </div>

    <!-- BOOTSTRAP JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
