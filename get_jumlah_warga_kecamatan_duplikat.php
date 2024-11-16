<?php
include 'koneksi.php';

if (isset($_POST['kecamatan'])) {
    $kecamatan = $_POST['kecamatan'];

    // Query untuk menghitung jumlah warga di kecamatan yang dipilih
    $query = "SELECT COUNT(*) AS jumlah FROM warga WHERE kecamatan = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $kecamatan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $jumlahWarga = $row['jumlah'];

    // Tutup koneksi
    mysqli_close($conn);

    // Kembalikan data dalam format JSON
    echo json_encode(['jumlah' => $jumlahWarga]);
}
?>
