<?php
session_start();
include 'koneksi.php';

// Pengecekan apakah pengguna sudah login dan merupakan admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Cek apakah username sudah ada
    $check = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        $error_message = "Username sudah digunakan!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
        if (mysqli_query($conn, $query)) {
            $success_message = "Pengguna berhasil ditambahkan.";
        } else {
            $error_message = "Terjadi kesalahan: " . mysqli_error($conn);
        }
    }
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $user_id = intval($_POST['user_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Password akan di-hash jika diubah
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (!empty($password)) {
        // Jika password diubah, hash password baru
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET username='$username', password='$hashed_password', role='$role' WHERE id='$user_id'";
    } else {
        // Jika password tidak diubah
        $update_query = "UPDATE users SET username='$username', role='$role' WHERE id='$user_id'";
    }

    if (mysqli_query($conn, $update_query)) {
        $success_message = "Data pengguna berhasil diperbarui.";
    } else {
        $error_message = "Gagal memperbarui data pengguna: " . mysqli_error($conn);
    }
}

// Handle Delete User
if (isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);

    // Jangan izinkan admin untuk menghapus dirinya sendiri
    if ($user_id === $_SESSION['user_id']) {
        $error_message = "Anda tidak dapat menghapus akun Anda sendiri.";
    } else {
        $delete_query = "DELETE FROM users WHERE id='$user_id'";
        if (mysqli_query($conn, $delete_query)) {
            $success_message = "Pengguna berhasil dihapus.";
        } else {
            $error_message = "Gagal menghapus pengguna: " . mysqli_error($conn);
        }
    }
}

// Fetch All Users (Kecuali Admin jika user bukan admin)
$users_query = "SELECT * FROM users";
$users_result = mysqli_query($conn, $users_query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengguna</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Data Pengguna</h1>
        
        <!-- Pesan Sukses atau Error -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <!-- Tombol Tambah Pengguna -->
        <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
            Tambah Pengguna
        </button>

        <!-- Tabel Data Pengguna -->
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']); ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id']; ?>">
                                Edit
                            </button>

                            <!-- Tombol Delete -->
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteUserModal<?= $user['id']; ?>">
                                Hapus
                            </button>

                            <!-- Modal Edit User -->
                            <div class="modal fade" id="editUserModal<?= $user['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $user['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="datauser.php" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editUserModalLabel<?= $user['id']; ?>">Edit Pengguna</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
                                                <div class="mb-3">
                                                    <label for="username<?= $user['id']; ?>" class="form-label">Username</label>
                                                    <input type="text" class="form-control" id="username<?= $user['id']; ?>" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="password<?= $user['id']; ?>" class="form-label">Password</label>
                                                    <input type="password" class="form-control" id="password<?= $user['id']; ?>" name="password" placeholder="Biarkan kosong jika tidak diubah">
                                                </div>
                                                <div class="mb-3">
                                                    <label for="role<?= $user['id']; ?>" class="form-label">Role</label>
                                                    <select class="form-select" id="role<?= $user['id']; ?>" name="role" required>
                                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User Biasa</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="update_user" class="btn btn-primary">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal Edit -->

                            <!-- Modal Delete User -->
                            <div class="modal fade" id="deleteUserModal<?= $user['id']; ?>" tabindex="-1" aria-labelledby="deleteUserModalLabel<?= $user['id']; ?>" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="datauser.php" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteUserModalLabel<?= $user['id']; ?>">Hapus Pengguna</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']); ?>">
                                                <p>Apakah Anda yakin ingin menghapus pengguna <strong><?= htmlspecialchars($user['username']); ?></strong>?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="delete_user" class="btn btn-danger">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal Delete -->
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="data.php" class="btn btn-secondary mt-3">Kembali ke Halaman Utama</a>
        <a href="logout.php" class="btn btn-danger mt-3">Logout</a>
    </div>

    <!-- Modal Tambah Pengguna -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="datauser.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Tambah Pengguna Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="new_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="new_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="new_password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_role" class="form-label">Role</label>
                            <select class="form-select" id="new_role" name="role" required>
                                <option value="user">User Biasa</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="add_user" class="btn btn-success">Tambah Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Modal Tambah Pengguna -->

    <!-- Bootstrap JS (Dependencies: Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
