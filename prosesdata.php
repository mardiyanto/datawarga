<?php
include 'koneksi.php';

// Perbaikan untuk kompatibilitas PHP versi di bawah 7.0
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

// Proses Input Data
if ($action == 'insert') {
    $nik = strtoupper(mysqli_real_escape_string($conn, $_POST['nik']));
    $nama = strtoupper(mysqli_real_escape_string($conn, $_POST['nama']));
    $no_hp = strtoupper(mysqli_real_escape_string($conn, $_POST['no_hp'])); // Menambahkan no_hp
    $keterangan = strtoupper(mysqli_real_escape_string($conn, $_POST['keterangan'])); // Mengganti alamat dengan keterangan
    $desa = strtoupper(mysqli_real_escape_string($conn, $_POST['desa']));
    $kecamatan = strtoupper(mysqli_real_escape_string($conn, $_POST['kecamatan']));
    $tim = strtoupper(mysqli_real_escape_string($conn, $_POST['tim']));
    $kordinator = strtoupper(mysqli_real_escape_string($conn, $_POST['kordinator']));
    $wa_kor = strtoupper(mysqli_real_escape_string($conn, $_POST['wa_kor']));
    // Periksa apakah NIK sudah ada di database
    $query_check = "SELECT * FROM data_warga WHERE nik = ?";
    $stmt_check = mysqli_prepare($conn, $query_check);
    mysqli_stmt_bind_param($stmt_check, "s", $nik);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    $count = mysqli_stmt_num_rows($stmt_check);
    mysqli_stmt_close($stmt_check);

    // Jika NIK sudah ada, kirimkan pesan kesalahan dan NIK kembali ke data.php
    if ($count > 0) {
        header("Location: data.php?error=duplicate&nik=" . urlencode($nik));
        $query = "INSERT INTO warga (nik, nama, no_hp, keterangan, desa, kecamatan, tim,kordinator,wa_kor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssss", $nik, $nama, $no_hp, $keterangan, $desa, $kecamatan, $tim, $kordinator, $wa_kor);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        exit();
    }

    // Jika tidak ada duplikasi, lakukan proses penyimpanan data
    $query = "INSERT INTO warga (nik, nama, no_hp, keterangan, desa, kecamatan, tim,kordinator,wa_kor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssssss", $nik, $nama, $no_hp, $keterangan, $desa, $kecamatan, $tim, $kordinator, $wa_kor);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: data.php?success=inserted");
    exit();
}

// Proses Update Data
elseif ($action == 'update') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nik = strtoupper(mysqli_real_escape_string($conn, $_POST['nik']));
    $nama = strtoupper(mysqli_real_escape_string($conn, $_POST['nama']));
    $no_hp = strtoupper(mysqli_real_escape_string($conn, $_POST['no_hp'])); // Menambahkan no_hp
    $keterangan = strtoupper(mysqli_real_escape_string($conn, $_POST['keterangan'])); // Mengganti alamat dengan keterangan
    $desa = strtoupper(mysqli_real_escape_string($conn, $_POST['desa']));
    $kecamatan = strtoupper(mysqli_real_escape_string($conn, $_POST['kecamatan']));
    $tim = strtoupper(mysqli_real_escape_string($conn, $_POST['tim']));
    $kordinator = strtoupper(mysqli_real_escape_string($conn, $_POST['kordinator']));
    $wa_kor = strtoupper(mysqli_real_escape_string($conn, $_POST['wa_kor']));
    
    $query = "UPDATE data_warga SET nik = ?, nama = ?, no_hp = ?, keterangan = ?, desa = ?, kecamatan = ?, tim = ?, kordinator = ?, wa_kor = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssssssssi", $nik, $nama, $no_hp, $keterangan, $desa, $kecamatan, $tim, $kordinator, $wa_kor, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: data.php?success=updated");
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
    
    header("Location: data.php?success=deleted");
    exit();
}

// Jika aksi tidak dikenali, kembali ke halaman utama
else {
    header("Location: data.php");
    exit();
}
?>
