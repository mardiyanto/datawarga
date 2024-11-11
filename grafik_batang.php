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

// Query untuk menghitung jumlah total data
$query_total = "SELECT COUNT(*) AS total FROM data_warga";
$result_total = mysqli_query($conn, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_data = $row_total['total']; // Total data

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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">FAUZI LARAS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">HOME</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="grafik_batang.php">TIM</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="grafik.php">KECAMATAN</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
        </li>
      </ul>
      
    </div>
  </div>
</nav>
<div class="container mt-5">
<h2 class="text-center"> TIM PEMENANG FAUZI LARAS</h2>
    <p class="text-center">Jumlah Total Data: <?php echo $total_data; ?> orang</p> <!-- Menampilkan jumlah total data -->
    <div class="row justify-content-center">
        <!-- Grafik Batang -->
        <div class="col-md-12">
            <canvas id="keteranganBarChart"></canvas>
        </div>
    </div>
    <div class="row justify-content-center mt-4">
        <!-- Grafik Pie -->
        <div class="col-md-12">
            <h3 class="text-center">TIM PEMENANG FAUZI LARAS</h3>
            <canvas id="keteranganPieChart"></canvas>
        </div>
    </div>
</div>

<script>
// Ambil data PHP ke dalam JavaScript
var keterangan = <?php echo json_encode($keterangan); ?>;
var jumlah = <?php echo json_encode($jumlah); ?>;

// Buat grafik batang menggunakan Chart.js
const ctxBar = document.getElementById('keteranganBarChart').getContext('2d');
const keteranganBarChart = new Chart(ctxBar, {
    type: 'bar', // Tipe grafik batang (bar chart)
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
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        var label = context.label || '';
                        var value = context.raw || 0;
                        return label + ': ' + value + ' orang';
                    }
                }
            }
        }
    }
});

// Buat grafik pie menggunakan Chart.js
const ctxPie = document.getElementById('keteranganPieChart').getContext('2d');
const keteranganPieChart = new Chart(ctxPie, {
    type: 'pie', // Tipe grafik pie
    data: {
        labels: keterangan,
        datasets: [{
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
                display: true,
                position: 'top'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        var label = context.label || '';
                        var value = context.raw || 0;
                        var percentage = (value / <?php echo $total_data; ?> * 100).toFixed(2);
                        return label + ': ' + value + ' orang (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
