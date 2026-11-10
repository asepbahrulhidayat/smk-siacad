<?php
// ========================================
// MEMULAI SESSION
// ========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ========================================
// MENGAMBIL ROLE DAN USERNAME
// ========================================
$role = strtolower($_SESSION['role'] ?? '');
$username = $_SESSION['username'] ?? 'Pengguna';

// ========================================
// MENU BERDASARKAN ROLE
// ========================================
$menu = [

    // ADMIN
    'admin' => [
        'Dashboard' => [
            'dashboard.php' => 'Dashboard',
        ],

        'Kelola Users' => [
            'data_user.php' => 'Data User',
            'form.php' => 'Tambah User 1',
            'formadmin.php' => 'Tambah User 2',
            'formpilihan.php' => 'Tambah User 3',
            'daftar_user.php' => 'Edit User',
            'daftar2_user.php' => 'Edit User 2',
        ],

        'Kelola Siswa' => [
            'form.php' => 'Tambah Siswa',
            'crud_edit_siswa.php' => 'Edit Siswa',
            'daftar_siswa.php' => 'Daftar Siswa',
        ],

        'Kelola Guru' => [
            'daftar_guru.php' => 'Data Guru',
            'tambah_guru.php' => 'Tambah Guru',
        ],

        'Kelola Mapel' => [
            'daftar_mapel.php' => 'Data Mapel',
            'tambah_mapel.php' => 'Tambah Mapel',
        ],

        'Daftar Mengajar' => [
            'daftar_mengajar.php' => 'Daftar Mengajar',
        ],
    ],

    // GURU
    'guru' => [
        'Dashboard' => [
            'dashboard_guru.php' => 'Dashboard',
        ],

        'Akademik' => [
            'daftar_siswa.php' => 'Data Siswa',
            'data_mapel.php' => 'Data Mapel',
            'nilai_guru.php' => 'Masukkan Nilai',
        ],
    ],

    // SISWA
    'siswa' => [
        'Dashboard' => [
            'dashboard_siswa.php' => 'Dashboard',
        ],

        'Akademik' => [
            'data_mapel.php' => 'Data Mapel',
            'nilai_siswa.php' => 'Nilai Siswa',
        ],
    ],
];

// Ambil menu sesuai role
$menuAktif = $menu[$role] ?? [];

// ========================================
// LINK PROFIL SESUAI ROLE
// ========================================
$profil_link = '';

if ($role === 'admin') {
    $profil_link = 'profil_admin.php';
} elseif ($role === 'guru') {
    $profil_link = 'profil_guru.php';
} elseif ($role === 'siswa') {
    $profil_link = 'profil_siswa.php';
}

// ========================================
// INISIAL USER
// ========================================
$inisial = strtoupper(substr($username, 0, 1));

// ID NAVBAR
$navbar_id = 'navbarMenu_' . substr(md5(uniqid('', true)), 0, 8);
?>

<!-- BOOTSTRAP CSS -->
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<style>
    .siacad-navbar {
        background: linear-gradient(110deg, #172943, #203b60);
        padding: 12px 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.13);
        position: relative;
        z-index: 1050;
    }

    .siacad-navbar .navbar-brand {
        color: #fff;
        font-size: 21px;
        font-weight: 800;
        letter-spacing: 1px;
        margin-right: 35px;
    }

    .siacad-navbar .navbar-brand span {
        color: #60a5fa;
    }

    .siacad-navbar .navbar-nav {
        gap: 5px;
    }

    .siacad-navbar .nav-link {
        color: #e8eef7 !important;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 13px !important;
        border-radius: 8px;
        transition: 0.25s;
    }

    .siacad-navbar .nav-link:hover,
    .siacad-navbar .nav-link:focus,
    .siacad-navbar .nav-link.show {
        color: #fff !important;
        background: rgba(255, 255, 255, 0.13);
    }

    .siacad-navbar .dropdown-menu {
        border: none;
        border-radius: 12px;
        padding: 8px;
        min-width: 205px;
        margin-top: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.16);
        animation: dropdownShow 0.2s ease;
    }

    .siacad-navbar .dropdown-item {
        padding: 10px 13px;
        font-size: 14px;
        border-radius: 7px;
        color: #334155;
        transition: 0.2s;
    }

    .siacad-navbar .dropdown-item:hover {
        background: #eaf2ff;
        color: #2563eb;
        padding-left: 18px;
    }

    @keyframes dropdownShow {
        from {
            opacity: 0;
            transform: translateY(7px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* BAGIAN AKUN */
    .akun-menu {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-left: 15px;
    }

    .akun-toggle {
        display: flex;
        align-items: center;
        gap: 9px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        background: rgba(255, 255, 255, 0.10);
        color: white;
        padding: 6px 13px 6px 7px;
        border-radius: 30px;
        font-size: 14px;
        transition: 0.2s;
    }

    .akun-toggle:hover,
    .akun-toggle:focus {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .akun-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #60a5fa;
        color: #10233e;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 800;
    }

    .akun-role {
        color: #bfdbfe;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    /* LOGOUT */
    .btn-logout {
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.45);
        border-radius: 8px;
        padding: 9px 15px;
        font-size: 13px;
        font-weight: 600;
        transition: 0.2s;
        text-decoration: none;
    }

    .btn-logout:hover {
        background: #ef4444;
        border-color: #ef4444;
        color: white;
    }

    /* RESPONSIVE */
    @media (max-width: 991.98px) {
        .siacad-navbar {
            padding: 12px 15px;
        }

        .siacad-navbar .navbar-nav {
            gap: 2px;
            margin-top: 12px;
        }

        .siacad-navbar .dropdown-menu {
            margin-top: 3px;
            box-shadow: none;
        }

        .akun-menu {
            margin: 12px 0 5px;
            align-items: flex-start;
            flex-direction: column;
        }

        .akun-toggle {
            width: 100%;
        }

        .btn-logout {
            display: inline-block;
            margin-top: 8px;
        }
    }
</style>

<!-- NAVBAR UTAMA -->
<nav class="navbar navbar-expand-lg navbar-dark siacad-navbar">

    <div class="container-fluid">

        <!-- LOGO -->
        <a class="navbar-brand" href="#">
            SIACAD <span>SMK</span>
        </a>

        <!-- TOGGLER HP -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#<?= htmlspecialchars($navbar_id, ENT_QUOTES, 'UTF-8') ?>"
            aria-controls="<?= htmlspecialchars($navbar_id, ENT_QUOTES, 'UTF-8') ?>"
            aria-expanded="false"
            aria-label="Buka navigasi"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- ISI NAVBAR -->
        <div
            class="collapse navbar-collapse"
            id="<?= htmlspecialchars($navbar_id, ENT_QUOTES, 'UTF-8') ?>"
        >

            <!-- MENU KIRI -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                <?php foreach ($menuAktif as $judul => $submenu): ?>

                    <?php if ($judul === 'Dashboard'): ?>

                        <?php foreach ($submenu as $link => $nama): ?>
                            <li class="nav-item">
                                <a
                                    class="nav-link"
                                    href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </li>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <?php
                        $dropdown_id = 'dropdown_' .
                            substr(md5($navbar_id . $judul), 0, 8);
                        ?>

                        <li class="nav-item dropdown">

                            <button
                                class="nav-link dropdown-toggle"
                                id="<?= htmlspecialchars($dropdown_id, ENT_QUOTES, 'UTF-8') ?>"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                            >
                                <?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') ?>
                            </button>

                            <ul
                                class="dropdown-menu"
                                aria-labelledby="<?= htmlspecialchars($dropdown_id, ENT_QUOTES, 'UTF-8') ?>"
                            >

                                <?php foreach ($submenu as $link => $nama): ?>
                                    <li>
                                        <a
                                            class="dropdown-item"
                                            href="<?= htmlspecialchars($link, ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                            <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>

                            </ul>

                        </li>

                    <?php endif; ?>

                <?php endforeach; ?>

            </ul>

            <!-- MENU AKUN KANAN -->
            <div class="akun-menu">

                <!-- DROPDOWN PROFIL -->
                <div class="dropdown">

                    <button
                        class="akun-toggle dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >

                        <span class="akun-avatar">
                            <?= htmlspecialchars($inisial, ENT_QUOTES, 'UTF-8') ?>
                        </span>

                        <span class="text-start">
                            <strong>
                                <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
                            </strong>

                            <br>

                            <span class="akun-role">
                                <?= htmlspecialchars($role, ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </span>

                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">

                        <?php if ($profil_link !== ''): ?>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="<?= htmlspecialchars($profil_link, ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <span class="me-2">👤</span>
                                    Profil Saya
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="<?= htmlspecialchars($profil_link, ENT_QUOTES, 'UTF-8') ?>"
                                >
                                    <span class="me-2">⚙️</span>
                                    Pengaturan Akun
                                </a>
                            </li>

                        <?php endif; ?>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <li>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <span class="me-2">🚪</span>
                                Keluar Akun
                            </a>
                        </li>

                    </ul>

                </div>

                <!-- TOMBOL LOGOUT -->
                <a href="logout.php" class="btn-logout">
                    Logout
                </a>

            </div>

        </div>
    </div>
</nav>

<!--
PENTING:
Bootstrap JavaScript Bundle harus dimuat satu kali saja
pada setiap halaman, tepat sebelum tag penutup </body>.
-->
