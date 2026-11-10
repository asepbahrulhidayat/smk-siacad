<?php
// =============================================
// 1. MEMULAI SESSION
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =============================================
// 2. KONEKSI DATABASE
// =============================================
include 'koneksi.php';

// =============================================
// 3. CEK HAK AKSES ADMIN
// =============================================
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

// =============================================
// 4. MENGAMBIL DATA USER SISWA
// =============================================
$query = mysqli_query($koneksi, "
    SELECT
        users.id,
        users.username,
        users.role,
        siswa.nama_siswa,
        siswa.kelas
    FROM users
    INNER JOIN siswa ON users.id = siswa.user_id
    WHERE users.role = 'siswa'
    ORDER BY users.id DESC
");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

$total = mysqli_num_rows($query);

// =============================================
// 5. FUNGSI KEAMANAN OUTPUT
// =============================================
function esc($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Users - SIACAD SMK</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f3f6fc;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #243047;
        }

        /* AREA KONTEN */
        .user-container {
            max-width: 1250px;
            margin: 35px auto;
            padding: 0 25px 40px;
        }

        /* HEADER HALAMAN */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-header h2 {
            font-size: 27px;
            font-weight: 700;
            margin: 0 0 7px;
            color: #1e293b;
        }

        .page-header p {
            color: #718096;
            margin: 0;
            font-size: 14px;
        }

        /* KARTU TOTAL DATA */
        .total-card {
            background: white;
            padding: 15px 22px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 13px;
            min-width: 180px;
        }

        .total-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            background: #e8efff;
            color: #3b63c8;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 21px;
        }

        .total-card small {
            color: #8490a3;
            font-size: 12px;
        }

        .total-card h3 {
            margin: 2px 0 0;
            font-size: 23px;
            color: #243b65;
        }

        /* KARTU TABEL */
        .table-card {
            background: white;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        }

        .table-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-title h3 {
            margin: 0;
            font-size: 18px;
            color: #263752;
        }

        .table-title span {
            font-size: 12px;
            color: #718096;
        }

        /* TABEL */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .table-users {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        .table-users thead {
            background: #263b61;
            color: white;
        }

        .table-users th {
            padding: 16px 15px;
            text-align: left;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .table-users td {
            padding: 15px;
            border-bottom: 1px solid #edf0f5;
            font-size: 14px;
            color: #475569;
        }

        .table-users tbody tr {
            transition: 0.2s;
        }

        .table-users tbody tr:hover {
            background: #f5f8ff;
        }

        .table-users tbody tr:last-child td {
            border-bottom: none;
        }

        /* ID */
        .id-user {
            font-weight: 600;
            color: #64748b;
        }

        /* ROLE */
        .role-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            background: #e8efff;
            color: #315bc0;
            font-size: 12px;
            font-weight: 600;
        }

        /* NAMA SISWA */
        .nama-siswa {
            font-weight: 600;
            color: #273b59;
        }

        /* KELAS */
        .kelas-badge {
            display: inline-block;
            background: #e7f7ef;
            color: #16804b;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
        }

        /* TOMBOL AKSI */
        .aksi {
            display: flex;
            gap: 7px;
            white-space: nowrap;
        }

        .action-btn {
            display: inline-block;
            padding: 8px 13px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-edit {
            background: #fff3d6;
            color: #b77905;
        }

        .btn-edit:hover {
            background: #f5b82e;
            color: white;
        }

        .btn-hapus {
            background: #ffe6e6;
            color: #dc3545;
        }

        .btn-hapus:hover {
            background: #dc3545;
            color: white;
        }

        /* DATA KOSONG */
        .empty-data {
            text-align: center;
            padding: 40px !important;
            color: #94a3b8 !important;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {

            .user-container {
                margin-top: 22px;
                padding: 0 14px 30px;
            }

            .page-header h2 {
                font-size: 23px;
            }

            .total-card {
                width: 100%;
            }

            .table-card {
                padding: 15px;
            }

        }

    </style>
</head>

<body>

<?php
// =============================================
// 6. MEMANGGIL NAVBAR
// =============================================
include 'navbar.php';
?>

<div class="user-container">

    <!-- HEADER -->
    <div class="page-header">

        <div>
            <h2>Data Users</h2>
            <p>Kelola akun dan informasi pengguna siswa SIACAD SMK.</p>
        </div>

        <div class="total-card">

            <div class="total-icon">
                👥
            </div>

            <div>
                <small>Total Data Siswa</small>
                <h3><?= $total; ?></h3>
            </div>

        </div>

    </div>

    <!-- TABEL DATA -->
    <div class="table-card">

        <div class="table-title">
            <h3>Daftar Pengguna Siswa</h3>
            <span>Data akun yang terdaftar</span>
        </div>

        <div class="table-responsive">

            <table class="table-users">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if ($total > 0): ?>

                    <?php while ($row = mysqli_fetch_assoc($query)): ?>

                        <tr>

                            <td class="id-user">
                                #<?= esc($row['id']); ?>
                            </td>

                            <td>
                                <?= esc($row['username']); ?>
                            </td>

                            <td>
                                <span class="role-badge">
                                    <?= esc($row['role']); ?>
                                </span>
                            </td>

                            <td class="nama-siswa">
                                <?= esc($row['nama_siswa']); ?>
                            </td>

                            <td>
                                <span class="kelas-badge">
                                    <?= esc($row['kelas']); ?>
                                </span>
                            </td>

                            <td>
                                <div class="aksi">

                                    <a
                                        href="edit_siswa.php?id=<?= (int)$row['id']; ?>"
                                        class="action-btn btn-edit">
                                        ✏ Edit
                                    </a>

                                    <a
                                        href="hapus_siswa.php?id=<?= (int)$row['id']; ?>"
                                        class="action-btn btn-hapus"
                                        onclick="return confirm('Yakin ingin menghapus data siswa ini?');">
                                        🗑 Hapus
                                    </a>

                                </div>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="empty-data">
                            Belum ada data pengguna siswa.
                        </td>
                    </tr>

                <?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
                </tbody>

            </table>

        </div>

    </div>

</div>

<?php
// =============================================
// 7. BEBASKAN HASIL QUERY
// =============================================
mysqli_free_result($query);
?>

<!-- =============================================
     8. BOOTSTRAP JAVASCRIPT
     Cukup satu kali pada setiap halaman.
============================================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php include 'footer.php'; ?>

</body>
</html>
