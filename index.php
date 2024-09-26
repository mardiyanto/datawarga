<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Warga</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/jquery-3.5.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h2 class="mt-4">DATA PEMENANG FAUZI LARAS</h2>

    <?php
    include 'koneksi.php';
    
    // Menampilkan notifikasi sukses jika ada
    if (isset($_GET['success']) && $_GET['success'] === 'inserted') {
        echo "<div class='alert alert-success' role='alert'>Data berhasil ditambahkan!</div>";
    }
    
    // Periksa jika ada pesan kesalahan duplikasi NIK
    $duplicate_nik = '';
    if (isset($_GET['error']) && $_GET['error'] == 'duplicate' && isset($_GET['nik'])) {
        $duplicate_nik = htmlspecialchars($_GET['nik']);
        echo "<div class='alert alert-danger'>NIK yang Anda masukkan sudah ada di database. <button class='btn btn-link' data-toggle='modal' data-target='#duplicateModal'>lihat detail nik</button></div>";
    }

    // Hitung total data
    $result_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM data_warga");
    $total_data = mysqli_fetch_assoc($result_count)['total'];
    ?>

    <!-- Tombol untuk memunculkan Modal Input -->
    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#inputModal">
        Tambah Data
    </button>
    <a class="btn btn-primary mb-4" href='ktp.php'>
        IMPORT DARI EXEL
   </a>
    <!-- Menampilkan jumlah total data -->
    <div class="alert alert-info" role="alert">
        Jumlah total data yang terinput: <?php echo $total_data; ?>
    </div>

    <!-- Modal Input Data -->
<div class="modal fade" id="inputModal" tabindex="-1" role="dialog" aria-labelledby="inputModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="proses.php" method="POST" onsubmit="return validateForm()">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputModalLabel">Tambah Data Warga</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="insert">
                    <div class="form-group">
                        <label>NIK:</label>
                        <input type="text" class="form-control" name="nik" style="text-transform: uppercase;" required>
                    </div>
                    <div class="form-group">
                        <label>Nama:</label>
                        <input type="text" class="form-control" name="nama" style="text-transform: uppercase;" required>
                    </div>
                    <div class="form-group">
                        <label>No HP:</label>
                        <input type="text" class="form-control" name="no_hp" style="text-transform: uppercase;">
                    </div>
                    <div class="form-group">
                        <label>Keterangan:</label>
                        <textarea class="form-control" name="keterangan" style="text-transform: uppercase;" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Desa:</label>
                        <input type="text" class="form-control" name="desa" style="text-transform: uppercase;" required>
                    </div>
                    <div class="form-group">
                        <label>Kecamatan:</label>
                        <input type="text" class="form-control" name="kecamatan" style="text-transform: uppercase;" required>
                    </div>
                    
                    <!-- Menambahkan pilihan FAUZI atau LARAS dengan radio button -->
                    <div class="form-group">
                        <label>TIM :</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tim" value="FAUZI" id="radioFauzi" required>
                            <label class="form-check-label" for="radioFauzi">FAUZI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tim" value="LARAS" id="radioLaras" required>
                            <label class="form-check-label" for="radioLaras">LARAS</label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>


    <!-- Modal untuk Menampilkan Detail NIK Duplikat -->
    <div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog" aria-labelledby="duplicateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateModalLabel">Detail NIK Duplikat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>NIK: <?php echo $duplicate_nik; ?></h6>
                    <p>Berikut adalah data yang menggunakan NIK ini:</p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>No HP</th> <!-- Menambahkan kolom No HP -->
                                <th>Keterangan</th> <!-- Mengganti Alamat menjadi Keterangan -->
                                <th>Desa</th>
                                <th>Kecamatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($duplicate_nik) {
                                $query = "SELECT * FROM data_warga WHERE nik = ?";
                                $stmt = mysqli_prepare($conn, $query);
                                mysqli_stmt_bind_param($stmt, "s", $duplicate_nik);
                                mysqli_stmt_execute($stmt);
                                $result_dup = mysqli_stmt_get_result($stmt);
                                while ($row = mysqli_fetch_assoc($result_dup)) {
                                    echo "<tr>
                                        <td>{$row['nik']}</td>
                                        <td>{$row['nama']}</td>
                                        <td>{$row['no_hp']}</td>
                                        <td>{$row['keterangan']}</td>
                                        <td>{$row['desa']}</td>
                                        <td>{$row['kecamatan']}</td>
                                    </tr>";
                                }
                                mysqli_stmt_close($stmt);
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<footer class="main-footer">
        <div class="container">
          <div class="pull-right hidden-xs">
            <b>Version</b> 2.3.0
          </div>
          <strong>Copyright &copy; 2024 <a href="http://sukait.com">mardybest</a>.</strong> All rights reserved.
        </div><!-- /.container -->
      </footer>
</div>

<!-- Script untuk validasi checkbox -->
<script>
    function validateForm() {
        var checkFauzi = document.getElementById('checkFauzi').checked;
        var checkLaras = document.getElementById('checkLaras').checked;

        if (!checkFauzi && !checkLaras) {
            alert('Anda harus memilih FAUZI atau LARAS.');
            return false;
        }

        return true;
    }
</script>
</body>
</html>