<?php
include 'koneksi.php';

// Perbaikan untuk kompatibilitas PHP versi di bawah 7.0
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Proses Input Data
if ($action == 'insert') {
    // Mengambil input dan mengubah menjadi huruf kapital
    $nik = strtoupper(mysqli_real_escape_string($conn, $_POST['nik']));
    $nama = strtoupper(mysqli_real_escape_string($conn, $_POST['nama']));
    $no_hp = strtoupper(mysqli_real_escape_string($conn, $_POST['no_hp'])); // Menambahkan no_hp
    $keterangan = strtoupper(mysqli_real_escape_string($conn, $_POST['keterangan'])); // Mengganti alamat dengan keterangan
    $desa = strtoupper(mysqli_real_escape_string($conn, $_POST['desa']));
    $kecamatan = strtoupper(mysqli_real_escape_string($conn, $_POST['kecamatan']));
    $tim = strtoupper(mysqli_real_escape_string($conn, $_POST['tim']));

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
    $query = "INSERT INTO data_warga (nik, nama, no_hp, keterangan, desa, kecamatan, tim) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssss", $nik, $nama, $no_hp, $keterangan, $desa, $kecamatan, $tim);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: index.php?success=inserted");
    exit();
}

// Proses Update Data
elseif ($action == 'update') {
    // Mengambil input dan mengubah menjadi huruf kapital
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nik = strtoupper(mysqli_real_escape_string($conn, $_POST['nik']));
    $nama = strtoupper(mysqli_real_escape_string($conn, $_POST['nama']));
    $no_hp = strtoupper(mysqli_real_escape_string($conn, $_POST['no_hp'])); // Menambahkan no_hp
    $keterangan = strtoupper(mysqli_real_escape_string($conn, $_POST['keterangan'])); // Mengganti alamat dengan keterangan
    $desa = strtoupper(mysqli_real_escape_string($conn, $_POST['desa']));
    $kecamatan = strtoupper(mysqli_real_escape_string($conn, $_POST['kecamatan']));
    $tim = strtoupper(mysqli_real_escape_string($conn, $_POST['tim']));
    
    $query = "UPDATE data_warga SET nik = ?, nama = ?, no_hp = ?, keterangan = ?, desa = ?, kecamatan = ?, tim = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssssi", $nik, $nama, $no_hp, $keterangan, $desa, $kecamatan, $tim, $id);
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
