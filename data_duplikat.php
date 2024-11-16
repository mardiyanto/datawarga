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
    $query = "SELECT * FROM warga WHERE kecamatan = '$kecamatan'";
    $result = mysqli_query($conn, $query);

    // Tambahkan query untuk menghitung jumlah data
    $countQuery = "SELECT COUNT(*) as jumlah_data FROM warga WHERE kecamatan = '$kecamatan'";
    $countResult = mysqli_query($conn, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);
    $jumlahData = $countRow['jumlah_data'];
} else {
    $query = "SELECT DISTINCT kecamatan FROM warga";
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
        <a href="data_kecamatan.php" class="btn btn-secondary">Kembali</a>
        <?php if (isset($kecamatan)) : ?>
            <p>Kecamatan <?= urlencode($kecamatan) ?> Jumlah data: <?= $jumlahData ?> </p>
    <a href="export_excel_duplikat.php?kecamatan=<?= urlencode($kecamatan) ?>" class="btn btn-success mb-3">Export ke Excel</a>
    <a href="export_pdf_duplikat.php?kecamatan=<?= urlencode($kecamatan) ?>" class="btn btn-danger mb-3">Export ke PDF</a>
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
                        <a href="data_duplikat.php?kecamatan=<?= urlencode($row['kecamatan']) ?>">
                            <?= htmlspecialchars($row['kecamatan']) ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
