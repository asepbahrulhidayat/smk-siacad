<?php
// ========================================
// SESSION DAN KONEKSI DATABASE
// ========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';

// ========================================
// CEK ROLE SISWA
// ========================================
if (($_SESSION['role'] ?? '') !== 'siswa') {
    die("Akses ditolak. Halaman ini hanya untuk siswa.");
}

// Ambil ID user yang sedang login
$user_id = (int) ($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);

if ($user_id <= 0) {
    die("ID akun tidak ditemukan. Silakan login kembali.");
}

// ========================================
// AMBIL DATA SISWA DAN USER
// ========================================
$sql = "SELECT
            siswa.id,
            siswa.nis,
            siswa.nama_siswa,
            siswa.kelas,
            siswa.jenis_kelamin,
            siswa.user_id,
            users.username
        FROM siswa
        INNER JOIN users ON siswa.user_id = users.id
        WHERE siswa.user_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan query: " . htmlspecialchars(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$siswa = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

// Cek apakah profil siswa tersedia
if (!$siswa) {
    die("Data profil siswa tidak ditemukan. Pastikan akun lu sudah terhubung dengan data siswa.");
}

// ========================================
// INISIAL NAMA SISWA
// ========================================
$nama_siswa = $siswa['nama_siswa'] ?: 'Siswa';
$inisial = strtoupper(substr($nama_siswa, 0, 1));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Profil Siswa - SIACAD SMK</title>

    <link rel="stylesheet" href="style1.css">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f7fb;
        }

        .profil-container {
            max-width: 750px;
            margin: 35px auto;
            padding: 0 15px;
        }

        .profil-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .profil-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .profil-avatar {
            width: 85px;
            height: 85px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #0d9488;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            font-weight: bold;
        }

        .profil-header h2 {
            font-weight: bold;
            color: #222;
        }

        .profil-row {
            display: grid;
            grid-template-columns: 160px minmax(0, 1fr);
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid #eee;
        }

        .profil-label {
            color: #666;
            font-weight: 500;
        }

        .profil-value {
            color: #222;
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        .profil-actions {
            margin-top: 25px;
        }

        @media (max-width: 500px) {
            .profil-container {
                margin: 20px auto;
            }

            .profil-card {
                padding: 20px;
            }

            .profil-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }
        }
    </style>
</head>

<body>

<?php require 'navbar.php'; ?>

<div class="profil-container">

    <div class="profil-card">

        <!-- HEADER PROFIL -->
        <div class="profil-header">

            <div class="profil-avatar">
                <?= htmlspecialchars($inisial) ?>
            </div>

            <h2>Profil Siswa</h2>

            <p class="text-muted">
                Informasi akun dan data siswa
            </p>

        </div>

        <!-- DATA PROFIL -->
        <div class="profil-row">
            <div class="profil-label">Username</div>

            <div class="profil-value">
                <?= htmlspecialchars($siswa['username'] ?? '-') ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">NIS</div>

            <div class="profil-value">
                <?= htmlspecialchars($siswa['nis'] ?? '-') ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">Nama Siswa</div>

            <div class="profil-value">
                <?= htmlspecialchars($siswa['nama_siswa'] ?? '-') ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">Kelas</div>

            <div class="profil-value">
                <?= htmlspecialchars($siswa['kelas'] ?? '-') ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">Jenis Kelamin</div>

            <div class="profil-value">
                <?= htmlspecialchars($siswa['jenis_kelamin'] ?? '-') ?>
            </div>
        </div>

        <!-- TOMBOL KEMBALI -->
        <div class="profil-actions">

            <a href="dashboard_siswa.php" class="btn btn-secondary">
                ← Kembali ke Dashboard
            </a>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
