<?php
include 'koneksi.php';

if (isset($_POST['kecamatan'])) {
    $kecamatan = $_POST['kecamatan'];

    // Query untuk mendapatkan data desa berdasarkan kecamatan
    $query = "SELECT desa, COUNT(*) AS jumlah FROM warga WHERE kecamatan = ? GROUP BY desa";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $kecamatan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $desa = [];
    $jumlah = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $desa[] = $row['desa'];
        $jumlah[] = $row['jumlah'];
    }

    // Tutup koneksi
    mysqli_close($conn);

    // Kembalikan data dalam format JSON
    echo json_encode(['desa' => $desa, 'jumlah' => $jumlah]);
}
?>
