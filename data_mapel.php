<?php
include("koneksi.php");

$sql = "SELECT *
        FROM mapel
        ORDER BY id DESC";

$mapelsql = mysqli_query($koneksi, $sql);

if (!$mapelsql) {
    die("Query Error : " . mysqli_error($koneksi));
}

$mapel = mysqli_fetch_all($mapelsql, MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require 'navbar.php'; ?>
    <title>Data Mapel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>


<body>


<div class="container my-4">


    <div class="row justify-content-center">


        <div class="col-md-10">


            <h2 class="text-center mb-4">
                Tabel mapel
            </h2>



            <table class="table table-striped table-hover table-bordered">

<thead class="table-dark">
<tr>
    <th>No</th>
    <th>Kode Mapel</th>
    <th>Nama Mapel</th>
    <th>Guru ID</th>
</tr>
</thead>

<tbody>

<?php if(count($mapel) > 0): ?>

<?php $no = 1; ?>

<?php foreach($mapel as $data): ?>

<tr>

    <td>
        <?= $no++; ?>
    </td>

    <td>
        <?= htmlspecialchars($data['kode_mapel']); ?>
    </td>

    <td>
        <?= htmlspecialchars($data['nama_mapel']); ?>
    </td>

    <td>
        <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
    </td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>
    <td colspan="4" class="text-center">
        Data Belum Di Isi
    </td>
</tr>

<?php endif; ?>

</tbody>

</table>


        </div>


    </div>


</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>

  </html>