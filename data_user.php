<?php
include("koneksi.php");

$sql = "SELECT users.*,
        COALESCE(siswa.nama_siswa, guru.nama) AS nama
        FROM users
        LEFT JOIN siswa ON users.id = siswa.user_id
        LEFT JOIN guru ON users.id = guru.user_id
        ORDER BY users.id DESC";


$usersql = mysqli_query($koneksi, $sql);


if (!$usersql) {
    die("Query Error : " . mysqli_error($koneksi));
}


$user = mysqli_fetch_all($usersql, MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php require 'navbar.php'; ?>
    <title>Data Users</title>
<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>
</head>


<body>


<div class="container my-4">


    <div class="row justify-content-center">


        <div class="col-md-10">


            <h2 class="text-center mb-4">
                Tabel Users
            </h2>



            <table class="table table-striped table-hover table-bordered">

<thead class="table-dark">
<tr>
    <th>No</th>
    <th>Username</th>
    <th>Role</th>
    <th>Dibuat</th>
</tr>
</thead>

<tbody>

<?php if(count($user) > 0): ?>

<?php $no = 1; ?>

<?php foreach($user as $data): ?>

<tr>

    <td>
        <?= $no++; ?>
    </td>


    <td>
        <?= $data['username']; ?>
    </td>

    <td>
        <?= $data['role']; ?>
    </td>

    <td>
        <?= $data['created_at']; ?>
    </td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>
    <td colspan="5" class="text-center">
        Data Belum Di Isi
    </td>
</tr>

<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</tbody>

</table>


        </div>


    </div>


</div>




  </body>

  </html>