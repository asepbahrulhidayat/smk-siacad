
<?php
require_once 'koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (($_SESSION['role'] ?? '') !== 'guru') {
    die("Akses ditolak. Halaman ini hanya untuk guru.");
}

// Ambil ID akun yang sedang login
$user_id = (int) ($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);

if ($user_id <= 0) {
    die("ID akun tidak ditemukan. Silakan login kembali.");
}

// Ambil profil guru berdasarkan akun login
$sql = "SELECT guru.*, users.username
        FROM guru
        INNER JOIN users ON guru.user_id = users.id
        WHERE guru.user_id = ?
        LIMIT 1";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$guru = mysqli_fetch_assoc($result);

if (!$guru) {
    die("Data profil guru tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Guru - SIACAD SMK</title>
    <link rel="stylesheet" href="style1.css">
    <style>
        .profil-container {
            max-width: 750px;
            margin: 35px auto;
            padding: 0 15px;
        }

        .profil-card {
            background: white;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,.08);
        }

        .profil-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .profil-avatar {
            width: 85px;
            height: 85px;
            margin: auto auto 15px;
            border-radius: 50%;
            background: #2563c7;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            font-weight: bold;
        }

        .profil-row {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 12px;
            padding: 13px 0;
            border-bottom: 1px solid #eee;
        }

        .profil-label {
            color: #666;
        }

        .profil-value {
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        @media (max-width: 500px) {
            .profil-row {
                grid-template-columns: 1fr;
                gap: 5px;
            }

            .profil-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="profil-container">
    <div class="profil-card">

        <div class="profil-header">
            <div class="profil-avatar">
                <?= strtoupper(substr($guru['nama'] ?? 'G', 0, 1)) ?>
            </div>

            <h2>Profil Guru</h2>
            <p style="color:#666;">
                Informasi akun dan data guru
            </p>
        </div>

        <div class="profil-row">
            <div class="profil-label">Nama Lengkap</div>
            <div class="profil-value">
                <?= htmlspecialchars($guru['nama'] ?? '-') ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">Username</div>
            <div class="profil-value">
                <?= htmlspecialchars($guru['username'] ?? '-') ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">NIP</div>
            <div class="profil-value">
                <?= htmlspecialchars($guru['nip'] ?? '-') ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">Jenis Kelamin</div>
            <div class="profil-value">
                <?= htmlspecialchars($guru['jenis_kelamin'] ?? '-') ?>
            </div>
        </div>

        <div style="margin-top:25px;">
            <a href="dashboard_guru.php"
               class="btn btn-secondary">
                Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>

<?php
mysqli_stmt_close($stmt);
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
