<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
} ?>

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
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap5.min.css">
    <style>
.pagination-container {
    max-height: 400px; /* Sesuaikan tinggi sesuai kebutuhan */
    overflow-y: auto;
    margin-right: 20px;
}

.pagination .page-item {
    margin: 2px 0;
}

.pagination .page-item.active .page-link {
    background-color: magenta;
    color: white;
}

.pagination .page-link {
    display: block;
    padding: 8px 12px;
    text-align: center;
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 4px;
    color: black;
    text-decoration: none;
}

.pagination .page-link:hover {
    background-color: #ddd;
}

.table-container {
    flex-grow: 1;
}
</style>
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
          <a class="nav-link active" aria-current="page" href="data.php">HOME</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="data_kecamatan.php">KECAMATAN</a>
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
<div class="container">
    
    <h2 class="mt-4">Data Warga</h2>

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

    // Tampilkan modal edit jika ada data yang dipilih untuk diedit
    if (isset($_GET['edit_id'])) {
        $edit_id = $_GET['edit_id'];
        $query = "SELECT * FROM data_warga WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
    }

    // Cek apakah ada NIK duplikat yang diambil dari URL
    if (isset($_GET['nik'])) {
        $duplicate_nik = $_GET['nik'];
    }

    // Pagination
    $limit = 1000; // Batas jumlah data per halaman
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Halaman saat ini
    $offset = ($page - 1) * $limit;

    // Hitung total data
    $result_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM data_warga");
    $total_data = mysqli_fetch_assoc($result_count)['total'];
    $total_pages = ceil($total_data / $limit);
    
    // Ambil data warga dengan pagination
    $query = "SELECT * FROM data_warga LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $limit, $offset);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    ?>

    <!-- Tombol untuk memunculkan Modal Input -->
    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#inputModal">
        Tambah Data
    </button> <a href="data_kecamatan.php" class="btn btn-primary mb-4" >EXPOR EXEL</a>
 <a href="logout.php" class="btn btn-danger mb-4" >Logout</a>

    <!-- Tabel Data Warga -->
<div class="d-flex">
    <!-- Pagination di sebelah kiri -->
    <nav class="pagination-container">
        <ul class="pagination flex-column">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <!-- Tabel di sebelah kanan -->
    <div class="container p-12 border rounded">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Keterangan</th>
                    <th>Desa</th>
                    <th>Kecamatan</th>
                    <th>Tim</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                    <td>{$no}</td> 
                        <td>{$row['nik']}</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['no_hp']}</td>
                        <td>{$row['keterangan']}</td>
                        <td>{$row['desa']}</td>
                        <td>{$row['kecamatan']}</td>
                        <td>{$row['tim']}</td>
                        <td>";
                        ?>
                        <?php if ($_SESSION['role'] === 'admin'): 
                            echo " <a href='?edit_id={$row['id']}' class='btn btn-info btn-sm'>Edit</a>
                            <a href='prosesdata.php?action=delete&id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>
                            "; ?>
                        <?php else: 
                            echo " <a href='?edit_id={$row['id']}' class='btn btn-info btn-sm'>Edit</a> "; ?>  
                      <?php endif; ?>    
                        </td>
                    </tr>
                <?php
                     $no++;
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Modal Input Data -->
    <div class="modal fade" id="inputModal" tabindex="-1" role="dialog" aria-labelledby="inputModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="prosesdata.php" method="POST">
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
                    <div class="form-group">
                        <label>TIM :</label>
                        <input type="text" class="form-control" name="tim" style="text-transform: uppercase;" required>
                    </div>
                    <!-- Menambahkan pilihan FAUZI atau LARAS dengan radio button -->
                    <!-- <div class="form-group">
                        <label>TIM :</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tim" value="FAUZI" id="radioFauzi" required>
                            <label class="form-check-label" for="radioFauzi">FAUZI</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tim" value="LARAS" id="radioLaras" required>
                            <label class="form-check-label" for="radioLaras">LARAS</label>
                        </div>
                    </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Data -->
    <?php if (isset($data)): ?>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="prosesdata.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data Warga</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                        <div class="form-group">
                            <label>NIK:</label>
                            <input type="text" class="form-control" name="nik" style="text-transform: uppercase;" value="<?php echo $data['nik']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" style="text-transform: uppercase;" value="<?php echo $data['nama']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>No HP:</label>
                            <input type="text" class="form-control" name="no_hp" style="text-transform: uppercase;" value="<?php echo $data['no_hp']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan:</label>
                            <textarea class="form-control" name="keterangan" style="text-transform: uppercase;" required><?php echo $data['keterangan']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Desa:</label>
                            <input type="text" class="form-control" name="desa" style="text-transform: uppercase;" value="<?php echo $data['desa']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kecamatan:</label>
                            <input type="text" class="form-control" name="kecamatan" style="text-transform: uppercase;" value="<?php echo $data['kecamatan']; ?>" required>
                        </div>
                        <!-- Menambahkan pilihan FAUZI atau LARAS dengan radio button -->
                        <div class="form-group">
                        <label>TIM :</label>
                        <input type="text" class="form-control" name="tim" value="<?php echo $data['tim']; ?>" style="text-transform: uppercase;" required>
                    </div>
                        <!-- <div class="form-group">
        <label>TIM :</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tim" value="FAUZI" id="radioFauzi" 
                <?php echo ($data['tim'] === 'FAUZI') ? 'checked' : ''; ?> required>
            <label class="form-check-label" for="radioFauzi">FAUZI</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="tim" value="LARAS" id="radioLaras" 
                <?php echo ($data['tim'] === 'LARAS') ? 'checked' : ''; ?> required>
            <label class="form-check-label" for="radioLaras">LARAS</label>
        </div>
    </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal untuk data duplikat -->
<div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog" aria-labelledby="duplicateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="duplicateModalLabel">NIK Duplikat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                NIK <?php echo $duplicate_nik; ?> sudah ada di database. Silakan masukkan NIK lain atau hubungi administrator.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {
    $('.table').DataTable();
});
</script>
<script>
    $(document).ready(function () {
        <?php if (isset($data)): ?>
        $('#editModal').modal('show');
        <?php endif; ?>
    });
</script>
</body>
</html>
