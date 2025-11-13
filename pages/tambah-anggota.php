<?php
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

include __DIR__ . '/../config/database.php';

$pesan = '';
$tipe_pesan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // hash md5
    $alamat = $_POST['alamat'];
    $no_telepon = $_POST['no_telepon'];

    // Upload foto
    $foto_profil = '';
    if (!empty($_FILES['foto_profil']['name'])) {
        $target_dir = __DIR__ . '/../uploads/users/';
        $file_name = time() . '_' . basename($_FILES['foto_profil']['name']);
        $target_file = $target_dir . $file_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
            $foto_profil = $file_name;
        }
    }

    // Simpan ke database
    $sql = "INSERT INTO anggota (nama_lengkap, email, password, alamat, no_telepon, foto_profil)
            VALUES ('$nama_lengkap', '$email', '$password', '$alamat', '$no_telepon', '$foto_profil')";

    if ($mysqli->query($sql)) {
        $pesan = "Anggota <b>$nama_lengkap</b> berhasil ditambahkan.";
        $tipe_pesan = "success";
    } else {
        $pesan = "Gagal menambahkan anggota: " . $mysqli->error;
        $tipe_pesan = "danger";
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Anggota</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tambah Anggota</li>
    </ol>

    <!-- Notifikasi -->
    <?php if (!empty($pesan)): ?>
        <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show" role="alert">
            <?= $pesan ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="2"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telepon" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Profil</label>
                    <input type="file" name="foto_profil" class="form-control" accept="image/*">
                </div>
                <button type="submit" class="btn btn-success">Simpan</button>
                <a href="index.php?hal=daftar-anggota" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>