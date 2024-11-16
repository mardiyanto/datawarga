<?php
include 'koneksi.php';

// Query untuk menghitung jumlah setiap kecamatan
$queryKecamatan = "SELECT kecamatan, COUNT(*) AS jumlah FROM data_warga GROUP BY kecamatan";
$resultKecamatan = mysqli_query($conn, $queryKecamatan);

$kecamatan = [];
$jumlah = [];

// Mengambil data dari hasil query kecamatan
while ($row = mysqli_fetch_assoc($resultKecamatan)) {
    $kecamatan[] = $row['kecamatan'];
    $jumlah[] = $row['jumlah'];
}

// Query untuk menghitung total jumlah data warga
$queryTotal = "SELECT COUNT(*) AS total_data FROM data_warga";
$resultTotal = mysqli_query($conn, $queryTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);
$totalData = $rowTotal['total_data'];

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
<?php
include 'menuuser.php'; ?>
<div class="container mt-5">
    <h2 class="text-center">Grafik Jumlah Warga per Kecamatan</h2>
    
    <!-- Menampilkan jumlah total data -->
    <div class="mb-3 text-center">
        <h4>Total Data Warga: <?php echo $totalData; ?></h4>
    </div>
    
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
    
    <!-- Menampilkan jumlah warga berdasarkan kecamatan yang dipilih -->
    <div id="jumlahWargaKecamatan" class="mb-3 text-center"></div>
    
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

// Fungsi untuk menampilkan jumlah warga per kecamatan
function displayJumlahWargaKecamatan(kecamatan) {
    $.ajax({
        url: 'get_jumlah_warga_kecamatan.php',
        type: 'POST',
        data: { kecamatan: kecamatan },
        dataType: 'json',
        success: function(response) {
            // Menampilkan jumlah warga per kecamatan
            $('#jumlahWargaKecamatan').html('<h4>Jumlah Warga di Kecamatan ' + kecamatan + ': ' + response.jumlah + '</h4>');
        }
    });
}

// Mengambil data desa berdasarkan kecamatan yang dipilih
$('#kecamatanDropdown').change(function() {
    const selectedKecamatan = $(this).val();
    if (selectedKecamatan) {
        // Tampilkan jumlah warga untuk kecamatan yang dipilih
        displayJumlahWargaKecamatan(selectedKecamatan);
        
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
        $('#jumlahWargaKecamatan').html('');
    }
});
</script>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
