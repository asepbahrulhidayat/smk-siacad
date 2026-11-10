
<?php
// Memulai session jika belum aktif.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form Tambah Siswa</title>

    <!-- CSS desain form milik lu -->
    <link rel="stylesheet" href="style1.css">

    <!-- Bootstrap CSS: cukup dimuat satu kali pada halaman ini -->
   <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>
</head>

<body>

<?php
// Memanggil navbar.
// Bootstrap JavaScript akan dimuat di bagian bawah halaman ini.
require 'navbar.php';
?>

<!-- =========================
     FORM TAMBAH DATA SISWA
========================= -->

<div class="form2" style="margin: 0 auto; width: 30%;">
    <div class="container text-center" style="margin: 0 auto;">
        <div class="form">
            <div class="card" style="margin-top: 20px;">

                <div class="card-form">
                    <h2>Form Tambah Siswa</h2>
                    <br>
                </div>

                <div class="card-body">

                    <!-- Data akan diproses oleh aksi_tambah_user.php -->
                    <form action="aksi_tambah_user.php" method="POST">

                        <h1>Data Profil Siswa</h1>
                        <br>

                        <!-- Nomor induk siswa -->
                        <div class="mb-3">
                            <input
                                type="number"
                                name="nis"
                                class="form-control"
                                placeholder="Nomor Induk Siswa | Contoh:12345678"
                                style="width: 90%; margin: 0 auto;"
                                required
                            >
                        </div>

                        <br>

                        <!-- Nama lengkap siswa -->
                        <div class="mb-3">
                            <input
                                type="text"
                                name="nama_siswa"
                                class="form-control"
                                placeholder="Nama Lengkap Siswa | Contoh:Ujang Asep"
                                style="width: 90%; margin: 0 auto;"
                                required
                            >
                        </div>

                        <br>

                        <!-- Kelas siswa -->
                        <div class="mb-3">
                            <input
                                type="text"
                                name="kelas"
                                class="form-control"
                                placeholder="Kelas Siswa | Contoh:X-RPL-1"
                                style="width: 90%; margin: 0 auto;"
                                required
                            >
                        </div>

                        <br>

                        <!-- Jenis kelamin -->
                        <div class="radio">
                            Jenis Kelamin:<br>

                            <input
                                type="radio"
                                name="jenis_kelamin"
                                id="L"
                                value="L"
                                class="form-check-input"
                                required
                            >

                            <label for="L">L</label>
                            <br>

                            <input
                                type="radio"
                                name="jenis_kelamin"
                                id="P"
                                value="P"
                                class="form-check-input"
                                required
                            >

                            <label for="P">P</label>
                        </div>

                        <br>

                        <!-- =========================
                             DATA AKUN USERS
                        ========================== -->

                        <div
                            class="form2"
                            style="border: 1px solid #d3d3d3; padding: 10px;"
                        >

                            <h3>Data Kredensial Akun (Tabel Users)</h3>
                            <br>

                            <!-- Username akun -->
                            <div class="mb-3">
                                <input
                                    type="text"
                                    name="username"
                                    class="form-control"
                                    placeholder="Username Akun | Contoh:ujang123"
                                    style="width: 90%; margin: 0 auto;"
                                    required
                                >
                            </div>

                            <br>

                            <!-- Password akun -->
                            <div class="mb-3">
                                <input
                                    type="password"
                                    name="password"
                                    class="form-control"
                                    placeholder="Password Akun"
                                    style="width: 90%; margin: 0 auto;"
                                    required
                                >
                            </div>

                            <!-- Role otomatis menjadi siswa -->
                            <input
                                type="hidden"
                                name="role"
                                value="siswa"
                            >

                            <br>

                            <!-- Tombol simpan dan kembali -->
                            <div class="button1">

                                <button
                                    type="submit"
                                    name="kirim"
                                    class="btn btn-primary"
                                    style="margin-bottom: 20px;"
                                >
                                    Simpan Data
                                </button>

                                <a
                                    href="crud_edit_siswa.php"
                                    class="btn btn-danger"
                                    style="margin-bottom: 20px; display: inline-block; text-decoration: none;"
                                >
                                    Kembali
                                </a>

                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<br>
<br>

<!-- =========================
     OUTPUT FORM
     Ditampilkan jika halaman menerima POST.
     Password tidak ditampilkan demi keamanan.
========================= -->

<div class="form2" style="margin: 0 auto; width: 30%;">
    <div class="container text-center">

        <div class="card" style="margin-top: 20px;">

            <div class="card-form">
                <h2>Output dari Form</h2>
                <br>

                <div class="card-get">

                    <?php
                    // Memeriksa apakah halaman menerima data POST.
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                        $nis           = $_POST['nis'] ?? '-';
                        $nama_siswa    = $_POST['nama_siswa'] ?? '-';
                        $kelas         = $_POST['kelas'] ?? '-';
                        $jenis_kelamin = $_POST['jenis_kelamin'] ?? '-';
                        $username      = $_POST['username'] ?? '-';

                        // Menampilkan data dengan htmlspecialchars
                        // untuk mencegah keluaran HTML yang tidak aman.
                        echo "NIS: " . htmlspecialchars((string) $nis) . "<br>";

                        echo "Nama: " .
                            htmlspecialchars((string) $nama_siswa) . "<br>";

                        echo "Kelas: " .
                            htmlspecialchars((string) $kelas) . "<br>";

                        echo "Jenis Kelamin: " .
                            htmlspecialchars((string) $jenis_kelamin) . "<br>";

                        echo "Username: " .
                            htmlspecialchars((string) $username) . "<br>";

                        // Password sengaja tidak ditampilkan.
                    } else {
                        echo "<em>Belum ada data yang dikirim. Isi form di atas.</em>";
                    }
                    ?>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- =========================
     BOOTSTRAP JAVASCRIPT
     Dimuat satu kali di halaman ini.
========================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>