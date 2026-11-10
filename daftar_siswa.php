<?php
require_once 'koneksi.php';
include 'navbar.php';

// Ambil hanya data dari tabel siswa
$query = mysqli_query($koneksi, "SELECT * FROM siswa ORDER BY user_id DESC");

if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<style>
    .content {
        padding: 30px;
    }

    .table-siswa {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .table-siswa th,
    .table-siswa td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: center;
    }

    .table-siswa th {
        background-color: #212529;
        color: white;
    }

    .table-siswa tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .table-siswa tr:hover {
        background-color: #e9ecef;
    }
</style>

<div class="content">
    <h2>Data Siswa</h2>
    <p>Berikut adalah daftar seluruh siswa yang terdaftar.</p>

    <div style="overflow-x: auto;">
        <table class="table-siswa">
            <thead>
                <b
                
                 <?php
                    // Judul kolom dibuat sesuai kolom tabel siswa
                    $fields = mysqli_fetch_fields($query);

                    foreach ($fields as $field) {
                        if ($field->name !== 'user_id') {
                            echo '<th>' .
                                htmlspecialchars(ucwords(str_replace('_', ' ', $field->name))) .
                                '</th>';
                        }
                    }
                    ?>
                </tr>
            </thead>

            <tbody>
                <?php if (mysqli_num_rows($query) > 0): ?>
                    <?php while ($siswa = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <?php foreach ($siswa as $kolom => $nilai): ?>
                                <?php if ($kolom !== 'user_id'): ?>
                                    <td>
                                        <?= htmlspecialchars((string)($nilai ?? '-')) ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= max(1, count($fields) - 1) ?>">
                            Belum ada data siswa.
                        </td>
                    </tr>
                <?php endif; ?>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
            </tbody>
           
        </table>
    </div>
</div>