<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Warga</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <h2 class="mt-4">Data Warga</h2>

    <?php
    include 'koneksi.php';

    // Periksa jika ada pesan kesalahan duplikasi NIK
    if (isset($_GET['error']) && $_GET['error'] == 'duplicate') {
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
    $duplicate_nik = '';
    if (isset($_GET['nik'])) {
        $duplicate_nik = $_GET['nik'];
    }
    ?>

    <!-- Modal Input Data -->
    <div class="modal fade" id="inputModal" tabindex="-1" role="dialog" aria-labelledby="inputModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="proses.php" method="POST">
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
                            <input type="text" class="form-control" name="nik" required>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat:</label>
                            <textarea class="form-control" name="alamat" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Desa:</label>
                            <input type="text" class="form-control" name="desa" required>
                        </div>
                        <div class="form-group">
                            <label>Kecamatan:</label>
                            <input type="text" class="form-control" name="kecamatan" required>
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

    <!-- Modal Edit Data -->
    <?php if (isset($data)): ?>
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="proses.php" method="POST">
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
                            <input type="text" class="form-control" name="nik" value="<?php echo $data['nik']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" value="<?php echo $data['nama']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Alamat:</label>
                            <textarea class="form-control" name="alamat" required><?php echo $data['alamat']; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>Desa:</label>
                            <input type="text" class="form-control" name="desa" value="<?php echo $data['desa']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Kecamatan:</label>
                            <input type="text" class="form-control" name="kecamatan" value="<?php echo $data['kecamatan']; ?>" required>
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
    <script>
        $(document).ready(function() {
            $('#editModal').modal('show');
        });
    </script>
    <?php endif; ?>

    <!-- Modal untuk Menampilkan Detail NIK Duplikat -->
    <div class="modal fade" id="duplicateModal" tabindex="-1" role="dialog" aria-labelledby="duplicateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="duplicateModalLabel">Detail NIK Duplikat</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>NIK: <?php echo htmlspecialchars($duplicate_nik); ?></h6>
                    <p>Berikut adalah data yang menggunakan NIK ini:</p>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>NIK</th>
                                <th>Nama</th>
                                <th>Alamat</th>
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
                                $result = mysqli_stmt_get_result($stmt);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                        <td>{$row['nik']}</td>
                                        <td>{$row['nama']}</td>
                                        <td>{$row['alamat']}</td>
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

    <!-- Tombol untuk memunculkan Modal Input -->
    <button type="button" class="btn btn-primary mb-4" data-toggle="modal" data-target="#inputModal">
        Tambah Data
    </button>

    <!-- Tabel Data Warga -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Desa</th>
                <th>Kecamatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = "SELECT * FROM data_warga";
            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['nik']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['alamat']}</td>
                    <td>{$row['desa']}</td>
                    <td>{$row['kecamatan']}</td>
                    <td>
                        <a href='?edit_id={$row['id']}' class='btn btn-info btn-sm'>Edit</a>
                        <a href='proses.php?action=delete&id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
