<!DOCTYPE html>
<html lang="id">
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Tambah Mapel</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
<?php require 'navbar.php'; ?>

<div style="margin: 0 auto; width: 30%;">
    <div class="container text-center">
        <div class="card" style="margin-top: 20px;">
            <form action="aksi_tambah_mapel.php" method="POST">
                <div style="border: 1px solid #d3d3d3; padding: 10px;">
                    <h3>Tabel Mapel Guru</h3><br>

                    <div class="mb-3">
                        <input type="text" name="kode_mapel" class="form-control"
                               placeholder="Masukan Kode Mapel"
                               style="width: 90%; margin: 0 auto;" required />
                    </div>

                    <div class="mb-3">
                        <input type="text" name="nama_mapel" class="form-control"
                               placeholder="Masukan Nama Mapel"
                               style="width: 90%; margin: 0 auto;" required />
                    </div>

                    <div class="mb-3">
                        <input type="text" name="nama_guru" class="form-control"
                               placeholder="Masukan Nama Guru"
                               style="width: 90%; margin: 0 auto;" required />
                    </div>

                    <div class="button1">
                        <button type="submit" name="kirim" class="btn btn-primary" style="margin-bottom: 20px;">Simpan Data</button>
                        <a href="index.php" class="btn btn-danger" style="margin-bottom: 20px;">Kembali</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>