<?php
session_start();
require_once "koneksi.php";

// ======================================
// CEK LOGIN
// ======================================
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// ======================================
// CEK ROLE GURU
// ======================================
if (($_SESSION['role'] ?? '') !== 'guru') {
    echo "<script>
        alert('Halaman ini khusus untuk guru!');
        window.location.href = 'index.php';
    </script>";
    exit;
}

// ======================================
// AMBIL ID USER YANG SEDANG LOGIN
// ======================================
$user_id = (int) ($_SESSION['id'] ?? 0);

// Cari data guru berdasarkan user_id
$stmt_guru = mysqli_prepare(
    $koneksi,
    "SELECT id, nama FROM guru WHERE user_id = ? LIMIT 1"
);

mysqli_stmt_bind_param($stmt_guru, "i", $user_id);
mysqli_stmt_execute($stmt_guru);

$hasil_guru = mysqli_stmt_get_result($stmt_guru);
$data_guru = mysqli_fetch_assoc($hasil_guru);

mysqli_stmt_close($stmt_guru);

if (!$data_guru) {
    die("
        <div style='font-family:Arial;padding:30px'>
            <h3>Data guru tidak ditemukan!</h3>
            <p>Pastikan kolom guru.user_id sesuai dengan ID akun yang sedang login.</p>
        </div>
    ");
}

$guru_id = (int) $data_guru['id'];
$nama_guru = $data_guru['nama'];

// ======================================
// AMBIL JADWAL KHUSUS GURU INI
// ======================================
$sql = "
    SELECT
        jp.id,
        jp.hari,
        jp.jam_mulai,
        jp.jam_selesai,
        jp.ruang,
        gm.kelas,
        m.nama_mapel,
        m.kode_mapel
    FROM jadwal_pelajaran jp
    INNER JOIN guru_mengajar gm
        ON gm.id = jp.guru_mengajar_id
    INNER JOIN mapel m
        ON m.id = gm.mapel_id
    WHERE gm.guru_id = ?
    ORDER BY
        FIELD(
            jp.hari,
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ),
        jp.jam_mulai ASC,
        gm.kelas ASC
";

$stmt = mysqli_prepare($koneksi, $sql);
mysqli_stmt_bind_param($stmt, "i", $guru_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$total_jadwal = mysqli_num_rows($result);

// Simpan hasil agar bisa digunakan untuk tabel dan kartu
$jadwal = [];

while ($row = mysqli_fetch_assoc($result)) {
    $jadwal[] = $row;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Jadwal Mengajar Guru</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #f4f6f9;
        }

        .judul-halaman {
            font-weight: 700;
            color: #253858;
        }

        .kartu-info {
            border: none;
            border-radius: 12px;
        }

        .kartu-jadwal {
            border: none;
            border-radius: 12px;
            transition: transform 0.2s;
        }

        .kartu-jadwal:hover {
            transform: translateY(-3px);
        }

        .label-hari {
            display: inline-block;
            background: #e7f1ff;
            color: #0d6efd;
            border-radius: 20px;
            padding: 5px 12px;
            font-weight: 600;
        }

        .jam {
            font-weight: 600;
            color: #198754;
        }
    </style>
</head>

<body>

<?php require_once "navbar.php"; ?>

<div class="container py-4">

    <!-- JUDUL -->
    <div class="mb-4">
        <h2 class="judul-halaman">Jadwal Mengajar Saya</h2>

        <p class="text-muted mb-0">
            Halo, <?= htmlspecialchars($nama_guru) ?>.
            Berikut jadwal mengajar yang ditugaskan kepada Anda.
        </p>
    </div>

    <!-- RINGKASAN -->
    <div class="card kartu-info shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center">

                <div class="col-md-8">
                    <h5 class="mb-1">
                        <?= htmlspecialchars($nama_guru) ?>
                    </h5>

                    <p class="text-muted mb-0">
                        Guru · Jadwal Mengajar
                    </p>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-primary fs-6">
                        <?= $total_jadwal ?> Jadwal
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- DAFTAR JADWAL -->
    <div class="card kartu-info shadow-sm">
        <div class="card-body p-4">

            <h5 class="mb-3">Daftar Jadwal Pelajaran</h5>

            <?php if ($total_jadwal === 0): ?>

                <div class="alert alert-info mb-0">
                    <h6 class="alert-heading">Belum Ada Jadwal</h6>

                    Jadwal mengajar lu belum tersedia, bro.
                    Minta admin memastikan bahwa:
                    <ul class="mb-0 mt-2">
                        <li>Penugasan guru sudah dibuat.</li>
                        <li>Jadwal pelajaran sudah ditambahkan.</li>
                        <li>Data guru terhubung dengan akun login yang benar.</li>
                    </ul>
                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-bordered table-hover align-middle">

                        <thead class="table-primary">
                            <tr>
                                <th>No.</th>
                                <th>Hari</th>
                                <th>Jam</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Ruang</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($jadwal as $index => $row): ?>

                                <tr>
                                    <td><?= $index + 1 ?></td>

                                    <td>
                                        <span class="label-hari">
                                            <?= htmlspecialchars($row['hari']) ?>
                                        </span>
                                    </td>

                                    <td class="jam">
                                        <?= htmlspecialchars(substr($row['jam_mulai'], 0, 5)) ?>
                                        -
                                        <?= htmlspecialchars(substr($row['jam_selesai'], 0, 5)) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['kelas']) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($row['nama_mapel']) ?>
                                        </strong>

                                        <br>

                                        <small class="text-muted">
                                            <?= htmlspecialchars($row['kode_mapel']) ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($row['ruang'] ?: '-') ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>