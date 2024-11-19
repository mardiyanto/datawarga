<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use Dompdf\Dompdf;

if (isset($_GET['kordinator'])) {
    $kordinator = mysqli_real_escape_string($conn, $_GET['kordinator']);
    $wa_kor = mysqli_real_escape_string($conn, $_GET['wa_kor']);
    $query = "SELECT * FROM data_warga WHERE kordinator = '$kordinator'";
    $result = mysqli_query($conn, $query);

    // Mulai HTML
    $html = '<h1 style="text-align:center;">Laporan Data Warga</h1>';
    $html .= '<p>Jumlah data: ' . mysqli_num_rows($result) . '</p>';
    $html .= '<table border="1" width="100%" cellpadding="5" cellspacing="0">
                <tbody>
                  <tr>
                    <td><strong>Kordinator TPS</strong></td>
                    <td>' . htmlspecialchars($kordinator) . '</td>
                  </tr>
                  <tr>
                    <td><strong>No Wa Kordinator</strong></td>
                    <td>' . htmlspecialchars($wa_kor) . '</td>
                  </tr>
                </tbody>
              </table><br>';

    // Tabel data warga
    $html .= '<table border="1" width="100%" cellpadding="5" cellspacing="0">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Pembawa</th>
                    <th>Kecamatan</th>
                    <th>Desa</th>
                    <th>Tim</th>
                  </tr>
                </thead>
                <tbody>';

    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($row['id']) . '</td>
                    <td>' . htmlspecialchars($row['nik']) . '</td>
                    <td>' . htmlspecialchars($row['nama']) . '</td>
                    <td>' . htmlspecialchars($row['keterangan']) . '</td>
                    <td>' . htmlspecialchars($row['kecamatan']) . '</td>
                    <td>' . htmlspecialchars($row['desa']) . '</td>
                    <td>' . htmlspecialchars($row['tim']) . '</td>
                  </tr>';
    }
    $html .= '</tbody></table>';

    // Generate PDF dengan Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream('Laporan_KordinatorTPS_' . $kordinator . '.pdf');
} else {
    echo "Kecamatan tidak ditemukan!";
}
?>
