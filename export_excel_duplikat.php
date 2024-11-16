<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['kecamatan'])) {
    $kecamatan = $_GET['kecamatan'];
    $query = "SELECT * FROM warga WHERE kecamatan = '$kecamatan'";
    $result = mysqli_query($conn, $query);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set header
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'NIK');
    $sheet->setCellValue('C1', 'Nama');
    $sheet->setCellValue('D1', 'Keterangan');
    $sheet->setCellValue('E1', 'No HP');
    $sheet->setCellValue('F1', 'Desa');
    $sheet->setCellValue('G1', 'Tim');

    // Populate data
    $rowIndex = 2;
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowIndex, $row['id']);
        $sheet->setCellValue('B' . $rowIndex, $row['nik']);
        $sheet->setCellValue('C' . $rowIndex, $row['nama']);
        $sheet->setCellValue('D' . $rowIndex, $row['keterangan']);
        $sheet->setCellValue('E' . $rowIndex, $row['no_hp']);
        $sheet->setCellValue('F' . $rowIndex, $row['desa']);
        $sheet->setCellValue('G' . $rowIndex, $row['tim']);
        $rowIndex++;
    }

    // Export to Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'warga_Kecamatan_' . $kecamatan . '.xlsx';

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
} else {
    echo "Kecamatan tidak ditemukan!";
}
?>
