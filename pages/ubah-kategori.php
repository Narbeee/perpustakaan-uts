<?php
// proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_kategori = $_GET['id'];

    include _DIR_ . '/../config/database.php';

    $sql = "SELECT nama_kategori FROM kategori WHERE id_kategori = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $id_kategori);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $kategori = $result->fetch_assoc();
            } else {
                echo "Data kategori tidak ditemukan";
                exit();
            }
        } else {
            header('Location: index.php?hal=daftar-kategori');
            exit();
        }
        $stmt->close();
    }
    $mysqli->close();
} else {
    echo "ID Kategori tidak valid";
    exit();
}

// proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include _DIR_ . '/../config/database.php';

    $nama_kategori = $_POST['nama_kategori'];
    $sql = "UPDATE kategori SET nama_kategori = ? WHERE id_kategori = ?";

    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("si", $nama_kategori, $id_kategori);

        if ($stmt->execute()) {
            session_start();
            $_SESSION['pesan'] = "Data kategori berhasil diubah.";
            header('Location: index.php?hal=daftar-kategori');
            exit();
        } else {
            $pesan_error = "Terjadi kesalahan saat mengubah data kategori: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $pesan_error = "Query tidak dapat dipersiapkan.";
    }

    $mysqli->close();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kategori</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ubah Kategori</li>
    </ol>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $pesan_error; ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="nama_kategori" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="nama_kategori" name="nama_kategori"
                        value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?hal=daftar-kategori" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
</div>