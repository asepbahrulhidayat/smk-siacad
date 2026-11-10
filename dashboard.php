<?php
session_start();
require_once 'koneksi.php';

// Periksa login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Dashboard ini khusus admin
if (($_SESSION['role'] ?? '') !== 'admin') {
    if (($_SESSION['role'] ?? '') === 'guru') {
        header("Location: dashboard_guru.php");
    } elseif (($_SESSION['role'] ?? '') === 'siswa') {
        header("Location: dashboard_siswa.php");
    } else {
        header("Location: login.php");
    }
    exit;
}

$nama = $_SESSION['username'] ?? 'Admin';

// Menghitung jumlah data
function jumlahData($koneksi, $sql) {
    $result = mysqli_query($koneksi, $sql);

    if (!$result) {
        return 0;
    }

    $data = mysqli_fetch_assoc($result);
    return (int) ($data['total'] ?? 0);
}

$total_user = jumlahData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM users"
);

$total_siswa = jumlahData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM siswa"
);

$total_guru = jumlahData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM guru"
);

$total_mapel = jumlahData(
    $koneksi,
    "SELECT COUNT(*) AS total FROM mapel"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard SIAKAD</title>

    <link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <style>
        body {
            background: #f4f6fb;
        }

        .sidebar {
            min-height: 100vh;
            background: #173b70;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 12px;
            margin-bottom: 5px;
            border-radius: 8px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #315b96;
        }

        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,.05);
            height: 100%;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
        }

        .welcome {
            background: white;
            border-radius: 15px;
            padding: 24px;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 sidebar p-3">
            <h4 class="text-white mb-4">SIAKAD</h4>

            <a href="dashboard.php" class="active">
                🏠 Dashboard
            </a>

            <a href="daftar_user.php">
                👥 Data User
            </a>

            <a href="daftar_guru.php">
                👨‍🏫 Data Guru
            </a>

            <a href="daftar_siswa.php">
                🎓 Data Siswa
            </a>

            <a href="daftar_mapel.php">
                📚 Data Mapel
            </a>

            <a href="logout.php">
                🚪 Logout
            </a>
        </aside>

        <!-- KONTEN -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="welcome mb-4">
                <h3>Dashboard Admin</h3>
                <p class="text-secondary mb-0">
                    Selamat datang,
                    <strong><?= htmlspecialchars($nama) ?></strong>!
                    Selamat mengelola data akademik sekolah.
                </p>
            </div>

            <h5 class="mb-3">Ringkasan Data</h5>

            <div class="row g-3">

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <p class="text-secondary">Total User</p>
                            <div class="stat-number text-primary">
                                <?= $total_user ?>
                            </div>
                            <a href="daftar_user.php"
                               class="btn btn-outline-primary btn-sm mt-3">
                                Lihat User
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <p class="text-secondary">Total Siswa</p>
                            <div class="stat-number text-success">
                                <?= $total_siswa ?>
                            </div>
                            <a href="daftar_siswa.php"
                               class="btn btn-outline-success btn-sm mt-3">
                                Lihat Siswa
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <p class="text-secondary">Total Guru</p>
                            <div class="stat-number text-warning">
                                <?= $total_guru ?>
                            </div>
                            <a href="daftar_guru.php"
                               class="btn btn-outline-warning btn-sm mt-3">
                                Lihat Guru
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body">
                            <p class="text-secondary">Total Mapel</p>
                            <div class="stat-number text-danger">
                                <?= $total_mapel ?>
                            </div>
                            <a href="daftar_mapel.php"
                               class="btn btn-outline-danger btn-sm mt-3">
                                Lihat Mapel
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body">
                    <h5>Menu Pengelolaan</h5>
                    <p class="text-secondary">
                        Pilih menu di sebelah kiri untuk mengelola
                        data akademik sekolah.
                    </p>
                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>