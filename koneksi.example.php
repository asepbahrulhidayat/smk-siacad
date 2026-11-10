<?php

// Contoh konfigurasi database SIACAD
$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "db_siacad_smk2"
);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

?>