<?php
session_start();
require_once 'koneksi.php';

// Periksa status login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Periksa role
if (($_SESSION['role'] ?? '') !== 'guru') {
    header("Location: login.php");
    exit;
}

$nama = $_SESSION['username'] ?? 'Guru';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Guru - SIAKAD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f7fb; }
        .sidebar {
            min-height: 100vh;
            background: #173b70;
        }
        .sidebar a {
            color: white;
            display: block;
            padding: 12px;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
        }
        .sidebar a:hover { background: #315b96; }
        .dashboard-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 3px 12px #0000000d;
        }
    </style>
</head>
<body>

<div class="container-fluid">
<div class="row">

    <aside class="col-md-3 col-lg-2 sidebar p-3">
        <h4 class="text-white mb-4">SIAKAD</h4>
        <a href="dashboard_guru.php">🏠 Dashboard</a>
        <a href="daftar_siswa.php">👨‍🎓 Data Siswa</a>
        <a href="data_mapel.php">📚 Mata Pelajaran</a>
        <a href="profil_guru.php">👤 Profil Guru</a>
        <a href="nilai_guru.php" >📝Masukkan Nilai</a>
        <a href="logout.php">🚪 Logout</a>
    </aside>

    <main class="col-md-9 col-lg-10 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3>Dashboard Guru</h3>
                <p class="text-secondary mb-0">
                    Selamat datang, <?= htmlspecialchars($nama) ?>!
                </p>
            </div>
            <span class="badge text-bg-primary p-2">Guru</span>
        </div>

        <div class="row g-3">
            <div class="col-sm-6 col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5>Data Siswa</h5>
                        <p class="text-secondary">Lihat informasi siswa.</p>
                        <a href="daftar_siswa.php" class="btn btn-primary">
                            Buka Data Siswa
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5>Mata Pelajaran</h5>
                        <p class="text-secondary">Lihat data mata pelajaran.</p>
                        <a href="data_mapel.php" class="btn btn-primary">
                            Buka Mata Pelajaran
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5>Jadwal Mengajarr</h5>
                        <p class="text-secondary">Lihat Jadwal Mengajar.</p>
                        <a href="daftar_mengajar.php" class="btn btn-primary">
                            Buka Mata Pelajaran
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5>Data Semua Guru</h5>
                        <p class="text-secondary">Lihat Semua Data Guru.</p>
                        <a href="guru.php" class="btn btn-primary">
                            Buka Mata Pelajaran
                        </a>
                    </div>
                </div>
            </div>

             <div class="col-sm-6 col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5>Masukan Nilai</h5>
                        <p class="text-secondary">Masukan Nilai Siswa</p>
                        <a href="nilai_guru.php" class="btn btn-primary">
                            Lihat Nilai Siswa
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-4">
                <div class="card dashboard-card">
                    <div class="card-body">
                        <h5>Profil Guru</h5>
                        <p class="text-secondary">Informasi akun guru.</p>
                        <a href="profil_guru.php" class="btn btn-primary">
                            Buka Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

</div>
</div>
</body>
</html>