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
    $limit = 50; // Batas jumlah data per halaman
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
    </button>

    <!-- Tabel Data Warga -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>No HP</th>
                <th>Keterangan</th>
                <th>Desa</th>
                <th>Kecamatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['nik']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['no_hp']}</td>
                    <td>{$row['keterangan']}</td>
                    <td>{$row['desa']}</td>
                    <td>{$row['kecamatan']}</td>
                    <td>
                        <a href='?edit_id={$row['id']}' class='btn btn-info btn-sm'>Edit</a>
                        <a href='prosesdata.php?action=delete&id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Hapus</a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i === $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

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
                            <input type="text" class="form-control" name="nik" required>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label>No HP:</label>
                            <input type="text" class="form-control" name="no_hp" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan:</label>
                            <textarea class="form-control" name="keterangan" required></textarea>
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
                            <input type="text" class="form-control" name="nik" value="<?php echo $data['nik']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nama:</label>
                            <input type="text" class="form-control" name="nama" value="<?php echo $data['nama']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>No HP:</label>
                            <input type="text" class="form-control" name="no_hp" value="<?php echo $data['no_hp']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Keterangan:</label>
                            <textarea class="form-control" name="keterangan" required><?php echo $data['keterangan']; ?></textarea>
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

<script>
    $(document).ready(function () {
        <?php if (isset($data)): ?>
        $('#editModal').modal('show');
        <?php endif; ?>
    });
</script>
</body>
</html>
