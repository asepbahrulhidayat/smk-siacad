<?php
// ========================================
// SESSION DAN KONEKSI DATABASE
// ========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'koneksi.php';

// ========================================
// CEK ROLE ADMIN
// ========================================
if (($_SESSION['role'] ?? '') !== 'admin') {
    die("Akses ditolak. Halaman ini hanya untuk admin.");
}

// Ambil ID user yang login
$user_id = (int) ($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);

if ($user_id <= 0) {
    die("ID akun tidak ditemukan. Silakan login kembali.");
}

// ========================================
// AMBIL DATA ADMIN
// ========================================
$sql = "SELECT id, username, password, role, created_at
        FROM users
        WHERE id = ? AND role = 'admin'
        LIMIT 1";

$stmt = mysqli_prepare($koneksi, $sql);

if (!$stmt) {
    die("Gagal menyiapkan query: " . htmlspecialchars(mysqli_error($koneksi)));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$admin = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$admin) {
    die("Data profil admin tidak ditemukan.");
}

// ========================================
// PESAN NOTIFIKASI
// ========================================
$pesan = '';
$jenis_pesan = '';

// ========================================
// PROSES UPDATE AKUN
// ========================================
if (isset($_POST['update_profil'])) {

    $username_baru = trim($_POST['username'] ?? '');
    $password_lama = $_POST['password_lama'] ?? '';
    $password_baru = $_POST['password_baru'] ?? '';
    $konfirmasi_password = $_POST['konfirmasi_password'] ?? '';

    // Validasi username
    if ($username_baru === '') {

        $pesan = "Username tidak boleh kosong!";
        $jenis_pesan = "danger";

    } elseif ($password_lama === '') {

        $pesan = "Masukkan password lama untuk verifikasi!";
        $jenis_pesan = "danger";

    } else {

        // ========================================
        // VERIFIKASI PASSWORD LAMA
        // Mendukung password hash dan password lama
        // yang sebelumnya masih berupa teks biasa.
        // ========================================
        $password_valid =
            password_verify($password_lama, $admin['password']) ||
            hash_equals((string)$admin['password'], $password_lama);

        if (!$password_valid) {

            $pesan = "Password lama yang kamu masukkan salah!";
            $jenis_pesan = "danger";

        } elseif (
            $password_baru !== '' &&
            strlen($password_baru) < 6
        ) {

            $pesan = "Password baru minimal 6 karakter!";
            $jenis_pesan = "danger";

        } elseif (
            $password_baru !== '' &&
            $password_baru !== $konfirmasi_password
        ) {

            $pesan = "Konfirmasi password baru tidak cocok!";
            $jenis_pesan = "danger";

        } else {

            // ========================================
            // CEK USERNAME SUDAH DIGUNAKAN ATAU BELUM
            // ========================================
            $cek = mysqli_prepare(
                $koneksi,
                "SELECT id FROM users
                 WHERE username = ? AND id != ?
                 LIMIT 1"
            );

            mysqli_stmt_bind_param(
                $cek,
                "si",
                $username_baru,
                $user_id
            );

            mysqli_stmt_execute($cek);

            $hasil_cek = mysqli_stmt_get_result($cek);
            $username_ada = mysqli_num_rows($hasil_cek) > 0;

            mysqli_stmt_close($cek);

            if ($username_ada) {

                $pesan = "Username sudah digunakan oleh akun lain!";
                $jenis_pesan = "danger";

            } else {

                // ========================================
                // PASSWORD BARU
                // Jika kosong, password lama tetap dipakai.
                // Password akan disimpan dalam bentuk hash.
                // ========================================
                $password_untuk_disimpan = ($password_baru !== '')
                    ? $password_baru
                    : $password_lama;

                $password_hash = password_hash(
                    $password_untuk_disimpan,
                    PASSWORD_DEFAULT
                );

                // ========================================
                // UPDATE USERNAME DAN PASSWORD
                // ========================================
                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE users
                     SET username = ?, password = ?
                     WHERE id = ? AND role = 'admin'"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "ssi",
                    $username_baru,
                    $password_hash,
                    $user_id
                );

                if (mysqli_stmt_execute($update)) {

                    mysqli_stmt_close($update);

                    // Perbarui username dalam session
                    $_SESSION['username'] = $username_baru;

                    // Redirect agar form tidak terkirim ulang
                    header("Location: profil_admin.php?berhasil=1");
                    exit;

                } else {

                    $pesan = "Gagal memperbarui akun: " .
                             mysqli_stmt_error($update);

                    $jenis_pesan = "danger";

                    mysqli_stmt_close($update);
                }
            }
        }
    }
}

// ========================================
// REFRESH DATA ADMIN
// ========================================
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, username, role, created_at
     FROM users
     WHERE id = ? AND role = 'admin'
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$admin_tampil = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$admin_tampil) {
    die("Data admin tidak ditemukan.");
}

// Notifikasi berhasil
if (isset($_GET['berhasil'])) {
    $pesan = "Username dan pengaturan akun berhasil diperbarui!";
    $jenis_pesan = "success";
}

// Inisial admin
$inisial = strtoupper(substr($admin_tampil['username'], 0, 1));
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Profil Admin - SIACAD SMK</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f4f7fb;
        }

        .profil-container {
            max-width: 850px;
            margin: 35px auto;
            padding: 0 15px;
        }

        .profil-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
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
            background: #7c3aed;
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
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .pengaturan-title {
            font-weight: bold;
            color: #20314d;
            margin-bottom: 5px;
        }

        .form-label {
            font-weight: 600;
            color: #444;
        }

        .form-control {
            padding: 11px;
            border-radius: 8px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
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

    <!-- ==================================
         PROFIL ADMIN
    =================================== -->
    <div class="profil-card">

        <div class="profil-header">

            <div class="profil-avatar">
                <?= htmlspecialchars($inisial) ?>
            </div>

            <h2>Profil Admin</h2>

            <p class="text-muted">
                Informasi akun administrator SIACAD SMK
            </p>

        </div>

        <div class="profil-row">
            <div class="profil-label">ID Admin</div>

            <div class="profil-value">
                <?= htmlspecialchars((string)$admin_tampil['id']) ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">Username</div>

            <div class="profil-value">
                <?= htmlspecialchars($admin_tampil['username']) ?>
            </div>
        </div>

        <div class="profil-row">
            <div class="profil-label">Role</div>

            <div class="profil-value">
                <span class="badge bg-primary">
                    <?= htmlspecialchars(ucfirst($admin_tampil['role'])) ?>
                </span>
            </div>
        </div>

        <?php if (!empty($admin_tampil['created_at'])): ?>

        <div class="profil-row">
            <div class="profil-label">Tanggal Dibuat</div>

            <div class="profil-value">
                <?= htmlspecialchars($admin_tampil['created_at']) ?>
            </div>
        </div>

        <?php endif; ?>

        <div class="mt-4">

            <a href="dashboard.php" class="btn btn-secondary">
                ← Kembali ke Dashboard
            </a>

        </div>

    </div>

    <!-- ==================================
         PENGATURAN AKUN
    =================================== -->
    <div class="profil-card">

        <h4 class="pengaturan-title">
            ⚙️ Pengaturan Akun
        </h4>

        <p class="text-muted mb-4">
            Ubah username dan password akun admin kamu.
        </p>

        <!-- NOTIFIKASI -->
        <?php if ($pesan !== ''): ?>

            <div class="alert alert-<?= htmlspecialchars($jenis_pesan) ?>">
                <?= htmlspecialchars($pesan) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <!-- USERNAME -->
            <div class="mb-3">

                <label class="form-label">
                    Username Baru
                </label>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    value="<?= htmlspecialchars($admin_tampil['username']) ?>"
                    required
                >

            </div>

            <hr>

            <h6 class="fw-bold mb-3">
                Ganti Password
            </h6>

            <!-- PASSWORD LAMA -->
            <div class="mb-3">

                <label class="form-label">
                    Password Lama
                </label>

                <input
                    type="password"
                    name="password_lama"
                    class="form-control"
                    placeholder="Masukkan password lama"
                    required
                >

            </div>

            <!-- PASSWORD BARU -->
            <div class="mb-3">

                <label class="form-label">
                    Password Baru
                </label>

                <input
                    type="password"
                    name="password_baru"
                    class="form-control"
                    placeholder="Kosongkan jika tidak ingin mengganti password"
                >

                <small class="text-muted">
                    Minimal 6 karakter. Kosongkan jika hanya ingin mengganti username.
                </small>

            </div>

            <!-- KONFIRMASI PASSWORD -->
            <div class="mb-4">

                <label class="form-label">
                    Konfirmasi Password Baru
                </label>

                <input
                    type="password"
                    name="konfirmasi_password"
                    class="form-control"
                    placeholder="Ulangi password baru"
                >

            </div>

            <!-- TOMBOL SIMPAN -->
            <button
                type="submit"
                name="update_profil"
                class="btn btn-primary"
            >
                💾 Simpan Perubahan
            </button>

        </form>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
