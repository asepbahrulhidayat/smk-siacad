
<?php
require_once 'koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$role = $_SESSION['role'] ?? '';

if (!in_array($role, ['admin', 'guru'], true)) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Ambil semua data guru dan mata pelajaran yang diajar.
// Sesuaikan relasi jika struktur tabel mapel kamu berbeda.
$sql = "
    SELECT
        u.username,
        u.role,
        g.nama,
        GROUP_CONCAT(
            DISTINCT m.nama_mapel
            ORDER BY m.nama_mapel
            SEPARATOR ', '
        ) AS mapel
    FROM guru g
    INNER JOIN users u ON u.id = g.user_id
    LEFT JOIN mapel m ON m.nama_guru = g.id
    GROUP BY u.id, u.username, u.role, g.id, g.nama
    ORDER BY g.nama ASC
";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die('Query gagal: ' . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Guru - SIACAD SMK</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css"
          rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .content {
            padding: 30px;
        }

        .judul {
            text-align: center;
            margin-bottom: 28px;
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,.06);
        }

        #tabelGuru thead th {
            background: #eaf0f7;
            color: #222;
            white-space: nowrap;
        }

        #tabelGuru tbody td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="content">
    <h2 class="judul">👨‍🏫 Daftar Guru</h2>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelGuru"
                       class="table table-striped table-bordered align-middle w-100">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Nama</th>
                            <th>Mata Pelajaran</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php $no = 1; ?>
                    <?php while ($guru = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($guru['username'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($guru['role'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($guru['nama'] ?? '-') ?></td>
                            <td>
                                <?= htmlspecialchars($guru['mapel'] ?: '-') ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>

<script>
$(document).ready(function () {
    $('#tabelGuru').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50, 100],
        language: {
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ guru",
            infoEmpty: "Belum ada data guru",
            zeroRecords: "Data guru tidak ditemukan",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Berikutnya",
                previous: "Sebelumnya"
            }
        }
    });
});
</script>

</body>
</html>
