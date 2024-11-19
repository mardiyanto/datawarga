<?php
require 'vendor/autoload.php';
include 'koneksi.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_GET['kordinator'])) {
    $kordinator = mysqli_real_escape_string($conn, $_GET['kordinator']);

    // Ambil data utama
    $query = "SELECT * FROM data_warga WHERE kordinator = '$kordinator'";
    $result = mysqli_query($conn, $query);

    // Ambil nomor WA dan jumlah data
    $waQuery = "SELECT DISTINCT wa_kor FROM data_warga WHERE kordinator = '$kordinator' LIMIT 1";
    $waResult = mysqli_query($conn, $waQuery);
    $waRow = mysqli_fetch_assoc($waResult);
    $waKor = isset($waRow['wa_kor']) ? $waRow['wa_kor'] : 'Tidak Tersedia';

    $jumlahData = mysqli_num_rows($result);

    // Buat Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set judul laporan
    $sheet->setCellValue('A1', 'Laporan Data Warga');
    $sheet->setCellValue('A2', 'Kordinator TPS:');
    $sheet->setCellValue('B2', $kordinator);
    $sheet->setCellValue('A3', 'No WA Kordinator:');
    $sheet->setCellValue('B3', $waKor);
    $sheet->setCellValue('A4', 'Jumlah Data:');
    $sheet->setCellValue('B4', $jumlahData);

    // Header tabel
    $sheet->setCellValue('A6', 'ID');
    $sheet->setCellValue('B6', 'NIK');
    $sheet->setCellValue('C6', 'Nama');
    $sheet->setCellValue('D6', 'Keterangan');
    $sheet->setCellValue('E6', 'No HP');
    $sheet->setCellValue('F6', 'Kecamatan');
    $sheet->setCellValue('G6', 'Desa');
    $sheet->setCellValue('H6', 'Tim');

    // Isi data warga
    $rowIndex = 7; // Mulai dari baris ke-7
    while ($row = mysqli_fetch_assoc($result)) {
        $sheet->setCellValue('A' . $rowIndex, $row['id']);
        $sheet->setCellValue('B' . $rowIndex, $row['nik']);
        $sheet->setCellValue('C' . $rowIndex, $row['nama']);
        $sheet->setCellValue('D' . $rowIndex, $row['keterangan']);
        $sheet->setCellValue('E' . $rowIndex, $row['no_hp']);
        $sheet->setCellValue('F' . $rowIndex, $row['kecamatan']);
        $sheet->setCellValue('G' . $rowIndex, $row['desa']);
        $sheet->setCellValue('H' . $rowIndex, $row['tim']);
        $rowIndex++;
    }

    // Format judul
    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
    $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

    // Export ke Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'Laporan_KordinatorTPS_' . $kordinator . '.xlsx';

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
} else {
    echo "Kecamatan tidak ditemukan!";
}
?>
