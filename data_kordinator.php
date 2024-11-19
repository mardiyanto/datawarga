<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
} ?>
<?php
include 'koneksi.php';

if (isset($_GET['kordinator'])) {
    $kordinator = $_GET['kordinator'];
    $wa_kor= $_GET['wa_kor'];
    $query = "SELECT * FROM data_warga WHERE kordinator = '$kordinator' ";
    $result = mysqli_query($conn, $query);

    // Tambahkan query untuk menghitung jumlah data
    $countQuery = "SELECT COUNT(*) as jumlah_data FROM data_warga WHERE kordinator = '$kordinator'";
    $countResult = mysqli_query($conn, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);
    $jumlahData = $countRow['jumlah_data'];
} else {
    $query = "SELECT DISTINCT kordinator,wa_kor FROM data_warga";
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
<?php
include 'menu.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Data Warga Berdasarkan Kecamatan dan Desa</h2>
        <a href="data_kordinator.php" class="btn btn-secondary">Kembali</a>
        <?php if (isset($kordinator)) : ?>
            <p>Jumlah data: <?= $jumlahData ?> </p>
    <a href="export_excel_kordinator.php?kordinator=<?= urlencode($kordinator) ?>&wa_kor=<?= urlencode($wa_kor) ?>" class="btn btn-success mb-3">Export ke Excel</a>
    <a href="export_pdf_kordinator.php?kordinator=<?= urlencode($kordinator) ?>&wa_kor=<?= urlencode($wa_kor) ?>" class="btn btn-danger mb-3">Export ke PDF</a>
<table class="table table-bordered">
  <tbody>
    <tr>
      <td>Kordinator Tps</td>
      <td><?= urlencode($kordinator) ?></td>
    </tr>
    <tr>
      <td>No Wa Kordinator</td>
      <td><?= urlencode($wa_kor) ?></td>
    </tr>
  </tbody>
</table>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Keterangan</th>
                        <th>No HP</th>
                        <th>Desa</th>
                        <th>Kecamatan</th>
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
                            <td><?= $row['kecamatan'] ?></td>
                            <td><?= $row['tim'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
   
        <?php else : ?>
            <ul class="list-group">
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <li class="list-group-item">
                        <a href="data_kordinator.php?kordinator=<?= urlencode($row['kordinator']) ?>&wa_kor=<?= urlencode($row['wa_kor']) ?>">
                            <?= htmlspecialchars($row['kordinator']) ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
