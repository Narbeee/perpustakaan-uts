<?php
// proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

include __DIR__ . '/../config/database.php';

$pesan = '';
$pesan_error = '';

$id_anggota = $_GET['id_anggota'] ?? '';

if ($id_anggota === '') {
    die('ID anggota tidak ditemukan');
}

// ambil data anggota
$stmt = $mysqli->prepare("SELECT nama_lengkap, email FROM anggota WHERE id_anggota = ?");
$stmt->bind_param("i", $id_anggota);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Data anggota tidak ditemukan');
}

$anggota = $result->fetch_assoc();
$stmt->close();

// proses ubah password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_baru = $_POST['password_baru'] ?? '';

    if (empty($password_baru)) {
        $pesan_error = "Password baru tidak boleh kosong.";
    } else {
        $password_md5 = md5($password_baru);

        $stmt_update = $mysqli->prepare("UPDATE anggota SET password = ? WHERE id_anggota = ?");
        $stmt_update->bind_param("si", $password_md5, $id_anggota);

        if ($stmt_update->execute()) {
            $pesan = "Password anggota dengan nama <strong>" . htmlspecialchars($anggota['nama_lengkap']) . "</strong> berhasil diubah.";
        } else {
            $pesan_error = "Gagal mengubah password: " . $stmt_update->error;
        }

        $stmt_update->close();
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Password</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ubah Password</li>
    </ol>

    <?php if (!empty($pesan)): ?>
        <div class="alert alert-success"><?= $pesan; ?></div>
    <?php endif; ?>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger"><?= $pesan_error; ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap_display" class="form-control"
                        value="<?= htmlspecialchars($anggota['nama_lengkap']); ?>" disabled style="background-color: #f0f0f0;">
                    <input type="hidden" name="nama_lengkap" value="<?= htmlspecialchars($anggota['nama_lengkap']); ?>">
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" id="email_display" class="form-control"
                        value="<?= htmlspecialchars($anggota['email']); ?>" disabled style="background-color: #f0f0f0;">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($anggota['email']); ?>">
                </div>

                <div class="mb-3">
                    <label for="password_baru" class="form-label">Password Baru</label>
                    <input type="password" id="password_baru" name="password_baru" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?hal=daftar-anggota" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
</div>