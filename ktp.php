<?php  
include 'koneksi.php'; // Pastikan untuk mengubah dengan path koneksi database Anda

// Inisialisasi variabel
$imported_count = isset($_GET['imported_count']) ? (int)$_GET['imported_count'] : 0;
$duplicate_count = isset($_GET['duplicate_count']) ? (int)$_GET['duplicate_count'] : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Data Warga</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Input DATA PEMENANG FAUZI LARAS dari Excel</h2>
    <a href='index.php' class='btn btn-info'>home</a>
    <a href='tema.xlsx' class='btn btn-info '>Download Format</a>
    <?php if ($imported_count > 0): ?>
        <div class='alert alert-success' role='alert'>
            <?php echo $imported_count; ?> data berhasil diimpor.
        </div>
    <?php endif; ?>

    <?php if ($duplicate_count > 0): ?>
        <div class='alert alert-danger' role='alert'>
            <?php echo $duplicate_count; ?> data duplikat ditemukan.
        </div>
    <?php endif; ?>

    <?php if ($imported_count === 0 && $duplicate_count === 0): ?>
        <div class='alert alert-warning' role='alert'>
            Tidak ada data yang berhasil diimpor atau ditemukan.
        </div>
    <?php endif; ?>

    <form action="postimpor.php" method="POST">
        <div class="form-group">
            <label>Tempelkan Data:</label>
            <textarea class="form-control" name="data" rows="10" placeholder="Tempelkan data dari Excel di sini..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Data</button>
    </form>
</div>

<script src="js/jquery-3.5.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
