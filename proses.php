<?php
include 'koneksi.php';

// Perbaikan untuk kompatibilitas PHP versi di bawah 7.0
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Proses Input Data
if ($action == 'insert') {
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $desa = mysqli_real_escape_string($conn, $_POST['desa']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);

    // Periksa apakah NIK sudah ada di database
    $query_check = "SELECT * FROM data_warga WHERE nik = ?";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, "s", $nik);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    $count = mysqli_stmt_num_rows($stmt_check);
    mysqli_stmt_close($stmt_check);

    // Jika NIK sudah ada, kirimkan pesan kesalahan dan NIK kembali ke index.php
    if ($count > 0) {
        header("Location: index.php?error=duplicate&nik=" . urlencode($nik));
        exit();
    }

    // Jika tidak ada duplikasi, lakukan proses penyimpanan data
    $query = "INSERT INTO data_warga (nik, nama, alamat, desa, kecamatan) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssss", $nik, $nama, $alamat, $desa, $kecamatan);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: index.php?success=inserted");
    exit();
}

// Proses Update Data
elseif ($action == 'update') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nik = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $desa = mysqli_real_escape_string($conn, $_POST['desa']);
    $kecamatan = mysqli_real_escape_string($conn, $_POST['kecamatan']);
    
    $query = "UPDATE data_warga SET nik = ?, nama = ?, alamat = ?, desa = ?, kecamatan = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssi", $nik, $nama, $alamat, $desa, $kecamatan, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: index.php?success=updated");
    exit();
}

// Proses Hapus Data
elseif ($action == 'delete') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "DELETE FROM data_warga WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: index.php?success=deleted");
    exit();
}

// Jika aksi tidak dikenali, kembali ke halaman utama
else {
    header("Location: index.php");
    exit();
}
?>
