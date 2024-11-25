<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$query = "SELECT * FROM data_warga";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query error: " . mysqli_error($conn));
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'NIK');
$sheet->setCellValue('C1', 'Nama');
$sheet->setCellValue('D1', 'No HP');
$sheet->setCellValue('E1', 'Keterangan');
$sheet->setCellValue('F1', 'Desa');
$sheet->setCellValue('G1', 'Kecamatan');
$sheet->setCellValue('H1', 'Tim');

// Populate data
$rowIndex = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowIndex, $row['id']);
    $sheet->setCellValue('B' . $rowIndex, $row['nik']);
    $sheet->setCellValue('C' . $rowIndex, $row['nama']);
    $sheet->setCellValue('D' . $rowIndex, $row['no_hp']);
    $sheet->setCellValue('E' . $rowIndex, $row['keterangan']);
    $sheet->setCellValue('F' . $rowIndex, $row['desa']);
    $sheet->setCellValue('G' . $rowIndex, $row['kecataman']);
    $sheet->setCellValue('H' . $rowIndex, $row['tim']);
    $rowIndex++;
}

// Export to Excel
$writer = new Xlsx($spreadsheet);
$fileName = 'Data_Warga.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

$writer->save('php://output');
exit;
?>
