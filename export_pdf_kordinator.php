<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use Dompdf\Dompdf;

if (isset($_GET['kordinator'])) {
    $kordinator = $_GET['kordinator'];
    $query = "SELECT * FROM data_warga WHERE kordinator = '$kordinator'";
    $result = mysqli_query($conn, $query);

    $html = '<h2>KORDINATOR TPS :' . htmlspecialchars($kordinator) . '</h2>';
    $html .= '<p>Jumlah data: ' . mysqli_num_rows($result) . '</p>';
    $html .= '<table border="1" width="100%" cellpadding="5" cellspacing="0">
                <tr>
                    <th>ID</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th>No HP</th>
                    <th>Kecamatan</th>
                    <th>Desa</th>
                    <th>Tim</th>
                </tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['nik'] . '</td>
                    <td>' . $row['nama'] . '</td>
                    <td>' . $row['keterangan'] . '</td>
                    <td>' . $row['no_hp'] . '</td>
                    <td>' . $row['kecamatan'] . '</td>
                    <td>' . $row['desa'] . '</td>
                    <td>' . $row['tim'] . '</td>
                  </tr>';
    }
    $html .= '</table>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('KORDINATORTPS_' . $kordinator . '.pdf');
} else {
    echo "Kecamatan tidak ditemukan!";
}
?>
