<?php
session_start();

// Hanya admin yang boleh membuka dasbor ini
if (($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$koneksi = mysqli_connect("localhost", "root", "", "db_siacad_smk");
mysqli_set_charset($koneksi, "utf8mb4");

function esc($v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

// Hitung jumlah baris tabel. Mengembalikan null jika tabel belum ada.
function hitung(mysqli $koneksi, string $tabel): ?int
{
    $diizinkan = ['users', 'guru', 'mapel'];
    if (!in_array($tabel, $diizinkan, true)) {
        return null;
    }
    try {
        $hasil = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM `$tabel`");
        return (int) mysqli_fetch_assoc($hasil)['total'];
    } catch (mysqli_sql_exception $e) {
        return null;
    }
}

// Ambil nama pengguna yang sedang login dari database
$nama = $_SESSION['username'] ?? 'Admin';

$stmt = mysqli_prepare($koneksi, "SELECT id, username, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, "s", $nama);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if ($user) {
    $nama = $user['username'];

    // Jika akun ini terhubung ke data guru, pakai nama lengkap guru
    try {
        $stmt = mysqli_prepare($koneksi, "SELECT nama FROM guru WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user['id']);
        mysqli_stmt_execute($stmt);
        $guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        if (!empty($guru['nama'])) {
            $nama = $guru['nama'];
        }
    } catch (mysqli_sql_exception $e) {
        // tabel guru belum siap: abaikan, tetap pakai username
    }
}

$kartu = [
    [
        'judul' => 'Kelola Users',
        'ket'   => 'Tambah, ubah, dan hapus akun pengguna.',
        'link'  => 'form.php',
        'total' => hitung($koneksi, 'users'),
        'warna' => '#3b6fd8',
    ],
    [
        'judul' => 'Kelola Guru',
        'ket'   => 'Kelola data guru dan NIP.',
        'link'  => 'guru.php',
        'total' => hitung($koneksi, 'guru'),
        'warna' => '#16a085',
    ],
    [
        'judul' => 'Kelola Mapel',
        'ket'   => 'Kelola daftar mata pelajaran.',
        'link'  => 'mapel.php',
        'total' => hitung($koneksi, 'mapel'),
        'warna' => '#e67e22',
    ],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SIACAD SMK</title>
    <style>
        body { margin: 0; background: #f4f6fb; color: #1f2a44;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .wrap { max-width: 1100px; margin: 0 auto; padding: 28px 20px 48px; }
        .hero { background: linear-gradient(135deg, #3b6fd8, #5b8def); color: #fff;
            border-radius: 16px; padding: 32px 28px; box-shadow: 0 6px 18px rgba(59,111,216,.3); }
        .hero small { opacity: .85; letter-spacing: 1px; text-transform: uppercase; font-size: 13px; }
        .hero h1 { margin: 6px 0 4px; font-size: 30px; line-height: 1.25; }
        .hero p { margin: 0; opacity: .9; }
        .judul { margin: 32px 0 14px; font-size: 18px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .kartu { display: block; background: #fff; border-radius: 14px; padding: 22px;
            text-decoration: none; color: inherit; border-top: 5px solid var(--w);
            box-shadow: 0 2px 10px rgba(0,0,0,.07); transition: transform .15s, box-shadow .15s; }
        .kartu:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,.12); }
        .kartu h3 { margin: 0 0 6px; font-size: 18px; }
        .kartu p { margin: 0 0 16px; color: #667; font-size: 14px; }
        .angka { font-size: 34px; font-weight: 700; color: var(--w); }
        .angka span { font-size: 13px; font-weight: 400; color: #889; margin-left: 4px; }
        .buka { display: inline-block; margin-top: 12px; font-size: 14px; font-weight: 600; color: var(--w); }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="wrap">

    <div class="hero">
        <small>Dashboard Admin</small>
        <h1>Selamat Datang, <?= esc($nama) ?>!</h1>
        <p>Kelola data pengguna, guru, dan mata pelajaran SIACAD SMK dari sini.</p>
    </div>

    <h2 class="judul">Menu Pengelolaan</h2>

    <div class="grid">
        <?php foreach ($kartu as $k): ?>
            <a class="kartu" href="<?= esc($k['link']) ?>" style="--w: <?= esc($k['warna']) ?>">
                <h3><?= esc($k['judul']) ?></h3>
                <p><?= esc($k['ket']) ?></p>
                <div class="angka">
                    <?= $k['total'] === null ? '-' : (int) $k['total'] ?>
                    <span>data</span>
                </div>
                <span class="buka">Buka &rarr;</span>
            </a>
        <?php endforeach; ?>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> 
</body>
</html>