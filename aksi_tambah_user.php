<?php

require_once 'koneksi.php';

// Hanya menerima data dari form POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: form.php");
    exit;
}

// =========================
// AMBIL DATA USER
// =========================

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = trim($_POST['role'] ?? '');

if ($username === '' || $password === '') {
    die("Username dan password wajib diisi.");
}

// Validasi role
$roles_valid = ['admin', 'guru', 'siswa'];

if (!in_array($role, $roles_valid, true)) {
    die("Role tidak valid.");
}

// Tentukan halaman tujuan
if ($role === 'siswa') {
    $halaman_tujuan = 'crud_edit_siswa.php';
} elseif ($role === 'guru') {
    $halaman_tujuan = 'daftar_guru.php';
} else {
    $halaman_tujuan = 'daftar_user.php';
}

// =========================
// MULAI TRANSAKSI
// =========================

mysqli_begin_transaction($koneksi);

try {

    // =========================
    // SIMPAN KE TABEL USERS
    // =========================

    $pass = password_hash($password, PASSWORD_DEFAULT);

    $stmt_users = mysqli_prepare(
        $koneksi,
        "INSERT INTO users
        (username, password, role, created_at)
        VALUES (?, ?, ?, NOW())"
    );

    if (!$stmt_users) {
        throw new Exception("Gagal menyiapkan query users.");
    }

    mysqli_stmt_bind_param(
        $stmt_users,
        "sss",
        $username,
        $pass,
        $role
    );

    if (!mysqli_stmt_execute($stmt_users)) {
        throw new Exception(
            "Gagal menyimpan users: " .
            mysqli_stmt_error($stmt_users)
        );
    }

    // Ambil ID user yang baru dibuat
    $user_id = mysqli_insert_id($koneksi);

    mysqli_stmt_close($stmt_users);

    // =========================
    // ROLE SISWA
    // =========================

    if ($role === 'siswa') {

        $nis = trim($_POST['nis'] ?? '');
        $nama_siswa = trim($_POST['nama_siswa'] ?? '');
        $kelas = trim($_POST['kelas'] ?? '');
        $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');

        if (
            $nis === '' ||
            $nama_siswa === '' ||
            $kelas === '' ||
            $jenis_kelamin === ''
        ) {
            throw new Exception(
                "NIS, nama siswa, kelas, dan jenis kelamin wajib diisi."
            );
        }

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO siswa
            (nis, nama_siswa, kelas, jenis_kelamin, user_id)
            VALUES (?, ?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception("Gagal menyiapkan query siswa.");
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssi",
            $nis,
            $nama_siswa,
            $kelas,
            $jenis_kelamin,
            $user_id
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(
                "Gagal menyimpan data siswa: " .
                mysqli_stmt_error($stmt)
            );
        }

        mysqli_stmt_close($stmt);
    }

    // =========================
    // ROLE GURU
    // =========================

    elseif ($role === 'guru') {

        $nip = trim($_POST['nip'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');

        if (
            $nip === '' ||
            $nama === '' ||
            $jenis_kelamin === ''
        ) {
            throw new Exception(
                "NIP, nama guru, dan jenis kelamin wajib diisi."
            );
        }

        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO guru
            (nip, nama, jenis_kelamin, user_id)
            VALUES (?, ?, ?, ?)"
        );

        if (!$stmt) {
            throw new Exception("Gagal menyiapkan query guru.");
        }

        mysqli_stmt_bind_param(
            $stmt,
            "sssi",
            $nip,
            $nama,
            $jenis_kelamin,
            $user_id
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception(
                "Gagal menyimpan data guru: " .
                mysqli_stmt_error($stmt)
            );
        }

        mysqli_stmt_close($stmt);
    }

    // Admin hanya disimpan di tabel users

    // =========================
    // SIMPAN TRANSAKSI
    // =========================

    mysqli_commit($koneksi);

    // Redirect sesuai role
    echo "<script>
        alert('Data berhasil disimpan!');
        window.location.href = " .
        json_encode($halaman_tujuan) .
        ";
    </script>";

    exit;

} catch (Exception $e) {

    mysqli_rollback($koneksi);

    die(
        "Proses gagal: " .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}

?>