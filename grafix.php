<?php
include 'koneksi.php';

// Query untuk menghitung jumlah setiap keterangan
$query = "SELECT keterangan, COUNT(*) AS jumlah FROM data_warga GROUP BY keterangan";
$result = mysqli_query($conn, $query);

$keterangan = [];
$jumlah = [];

// Mengambil data dari hasil query
while ($row = mysqli_fetch_assoc($result)) {
    $keterangan[] = $row['keterangan'];
    $jumlah[] = $row['jumlah'];
}

// Tutup koneksi
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grafik Data Warga</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Grafik TIM PEMENANG FAUZI LARAS</h2>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <canvas id="keteranganChart"></canvas>
        </div>
    </div>
</div>

<script>
// Ambil data PHP ke dalam JavaScript
var keterangan = <?php echo json_encode($keterangan); ?>;
var jumlah = <?php echo json_encode($jumlah); ?>;

// Buat grafik menggunakan Chart.js
const ctx = document.getElementById('keteranganChart').getContext('2d');
const keteranganChart = new Chart(ctx, {
    type: 'pie', // Tipe grafik pie
    data: {
        labels: keterangan,
        datasets: [{
            label: 'Jumlah',
            data: jumlah,
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            }
        }
    }
});
</script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
