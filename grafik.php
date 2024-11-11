<?php
include 'koneksi.php';

// Query untuk menghitung jumlah setiap kecamatan
$query = "SELECT kecamatan, COUNT(*) AS jumlah FROM data_warga GROUP BY kecamatan";
$result = mysqli_query($conn, $query);

$kecamatan = [];
$jumlah = [];

// Mengambil data dari hasil query
while ($row = mysqli_fetch_assoc($result)) {
    $kecamatan[] = $row['kecamatan'];
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
    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
<div class="container mt-12">
    <h2 class="text-center">TIM PEMENANG FAUZI LARAS</h2>
    
    <!-- Dropdown untuk memilih kecamatan -->
    <div class="mb-3 text-center">
        <label for="kecamatanDropdown" class="form-label">Pilih Kecamatan:</label>
        <select id="kecamatanDropdown" class="form-select w-50 mx-auto">
            <option value="">-- Pilih Kecamatan --</option>
            <?php foreach ($kecamatan as $kec) : ?>
                <option value="<?= $kec; ?>"><?= $kec; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-12">
            <canvas id="dataChart"></canvas>
        </div>
    </div>
</div>

<script>
// Data awal grafik berdasarkan kecamatan
var kecamatan = <?php echo json_encode($kecamatan); ?>;
var jumlah = <?php echo json_encode($jumlah); ?>;

// Fungsi untuk memperbarui grafik
function updateChart(labels, data, title = 'Jumlah Warga per Kecamatan') {
    const ctx = document.getElementById('dataChart').getContext('2d');
    if (window.kecamatanChart) {
        window.kecamatanChart.destroy();
    }
    window.kecamatanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.7)',
                borderColor: 'rgba(75, 192, 192, 1)',
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
                    position: 'top',
                },
                title: {
                    display: true,
                    text: title
                }
            }
        }
    });
}

// Menampilkan grafik awal
updateChart(kecamatan, jumlah);

// Mengambil data desa berdasarkan kecamatan yang dipilih
$('#kecamatanDropdown').change(function() {
    const selectedKecamatan = $(this).val();
    if (selectedKecamatan) {
        $.ajax({
            url: 'get_data_desa.php',
            type: 'POST',
            data: { kecamatan: selectedKecamatan },
            dataType: 'json',
            success: function(response) {
                const labels = response.desa;
                const data = response.jumlah;
                updateChart(labels, data, `Jumlah Warga per Desa di Kecamatan ${selectedKecamatan}`);
            }
        });
    } else {
        // Jika tidak ada kecamatan yang dipilih, tampilkan grafik awal
        updateChart(kecamatan, jumlah, 'Jumlah Warga per Kecamatan');
    }
});
</script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
