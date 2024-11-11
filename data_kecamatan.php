<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
} ?>
<?php
include 'koneksi.php';

if (isset($_GET['kecamatan'])) {
    $kecamatan = $_GET['kecamatan'];
    $query = "SELECT * FROM data_warga WHERE kecamatan = '$kecamatan'";
    $result = mysqli_query($conn, $query);

    // Tambahkan query untuk menghitung jumlah data
    $countQuery = "SELECT COUNT(*) as jumlah_data FROM data_warga WHERE kecamatan = '$kecamatan'";
    $countResult = mysqli_query($conn, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);
    $jumlahData = $countRow['jumlah_data'];
} else {
    $query = "SELECT DISTINCT kecamatan FROM data_warga";
    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Warga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">FAUZI LARAS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="data.php">HOME</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="data_kecamatan.php">KECAMATAN</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      
    </div>
  </div>
</nav>
    <div class="container mt-5">
        <h2 class="text-center">Data Warga Berdasarkan Kecamatan dan Desa</h2>
        <a href="data_kecamatan.php" class="btn btn-secondary">Kembali</a>
        <?php if (isset($kecamatan)) : ?>
            <p>Kecamatan <?= urlencode($kecamatan) ?> Jumlah data: <?= $jumlahData ?> </p>
    <a href="export_excel.php?kecamatan=<?= urlencode($kecamatan) ?>" class="btn btn-success mb-3">Export ke Excel</a>
    <a href="export_pdf.php?kecamatan=<?= urlencode($kecamatan) ?>" class="btn btn-danger mb-3">Export ke PDF</a>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Keterangan</th>
                        <th>No HP</th>
                        <th>Desa</th>
                        <th>Tim</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nik'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['keterangan'] ?></td>
                            <td><?= $row['no_hp'] ?></td>
                            <td><?= $row['desa'] ?></td>
                            <td><?= $row['tim'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
   
        <?php else : ?>
            <ul class="list-group">
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <li class="list-group-item">
                        <a href="data_kecamatan.php?kecamatan=<?= urlencode($row['kecamatan']) ?>">
                            <?= htmlspecialchars($row['kecamatan']) ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
