<?php
include 'koneksi.php'; // Pastikan untuk mengubah dengan path koneksi database Anda

// Inisialisasi variabel
$imported_count = 0;
$duplicate_count = 0;

// Memeriksa apakah form telah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data yang dipaste
    $data = $_POST['data'];

    // Memecah data menjadi baris
    $rows = explode("\n", $data);

    foreach ($rows as $row) {
        // Memecah baris menjadi kolom
        $columns = explode("\t", trim($row));

        // Memastikan ada cukup kolom
        if (count($columns) >= 7) { // Pastikan ada 7 kolom (nik, nama, no_hp, keterangan, desa, kecamatan, tim)
            $nik = strtoupper(mysqli_real_escape_string($conn, $columns[0]));
            $nama = strtoupper(mysqli_real_escape_string($conn, $columns[1]));
            $no_hp = strtoupper(mysqli_real_escape_string($conn, $columns[2]));
            $keterangan = strtoupper(mysqli_real_escape_string($conn, $columns[3]));
            $desa = strtoupper(mysqli_real_escape_string($conn, $columns[4]));
            $kecamatan = strtoupper(mysqli_real_escape_string($conn, $columns[5]));
            $tim = strtoupper(mysqli_real_escape_string($conn, $columns[6]));
            $kordinator = strtoupper(mysqli_real_escape_string($conn, $columns[7]));
            $wa_kor = strtoupper(mysqli_real_escape_string($conn, $columns[8]));

            // Periksa apakah NIK sudah ada di database
            $query_check = "SELECT * FROM data_warga WHERE nik = ?";
            $stmt_check = mysqli_prepare($conn, $query_check);
            mysqli_stmt_bind_param($stmt_check, "s", $nik);
            mysqli_stmt_execute($stmt_check);
            mysqli_stmt_store_result($stmt_check);
            $count = mysqli_stmt_num_rows($stmt_check);
            mysqli_stmt_close($stmt_check);

            // Jika NIK sudah ada, increment duplicate counter
            if ($count > 0) {
                $duplicate_count++;
                $query = "INSERT INTO warga (nik, nama, no_hp, keterangan, desa, kecamatan, tim,kordinator,wa_kor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssssssss", $nik, $nama, $no_hp, $keterangan, $desa, $kecamatan, $tim, $kordinator, $wa_kor);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                continue; // NIK sudah ada, skip ke baris berikutnya
            }

            // Jika tidak ada duplikasi, simpan data ke database
            $query = "INSERT INTO data_warga (nik, nama, no_hp, keterangan, desa, kecamatan, tim,kordinator,wa_kor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sssssssss", $nik, $nama, $no_hp, $keterangan, $desa, $kecamatan, $tim,$kordinator, $wa_kor);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Increment imported counter
            $imported_count++;
        }
    }

    // Redirect ke input_data_warga.php dengan jumlah data yang diimpor dan duplikat
    header("Location: ktp.php?imported_count=$imported_count&duplicate_count=$duplicate_count");
    exit();
}
?>
