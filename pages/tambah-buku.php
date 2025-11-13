<?php
// proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

include __DIR__ . '/../config/database.php';

$pesan = '';
$pesan_error = '';

// ambil data kategori
$sql_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = $mysqli->query($sql_kategori);
if (!$result_kategori) {
    die("Query kategori error: " . $mysqli->error);
}

// proses simpan buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul        = $_POST['judul'] ?? '';
    $penulis      = $_POST['penulis'] ?? '';
    $penerbit     = $_POST['penerbit'] ?? '';
    $tahun_terbit = $_POST['tahun_terbit'] ?? '';
    $stok         = $_POST['stok'] ?? '';
    $kategori     = $_POST['kategori'] ?? [];

    if (
        $judul === '' ||
        $penulis === '' ||
        $penerbit === '' ||
        $tahun_terbit === '' ||
        $stok === '' ||
        empty($kategori)
    ) {
        $pesan_error = "Semua field wajib diisi dan minimal satu kategori dipilih.";
    } else {
        // Simpan data buku dengan kolom cover_buku (awal masih kosong)
        $sql = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, stok, cover_buku) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            die("Prepare statement error: " . $mysqli->error);
        }

        // default cover kosong
        $cover_buku = '';

        $stmt->bind_param("sssiss", $judul, $penulis, $penerbit, $tahun_terbit, $stok, $cover_buku);

        if ($stmt->execute()) {
            $id_buku = $stmt->insert_id;

            // Simpan kategori buku
            $stmt_kat = $mysqli->prepare("INSERT INTO buku_kategori (id_buku, id_kategori) VALUES (?, ?)");
            foreach ($kategori as $id_kat) {
                $stmt_kat->bind_param("ii", $id_buku, $id_kat);
                $stmt_kat->execute();
            }
            $stmt_kat->close();

            // === Upload cover dengan nama file berdasarkan ID buku ===
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
                $folder_upload = __DIR__ . '/../uploads/buku/';
                if (!is_dir($folder_upload)) {
                    mkdir($folder_upload, 0777, true);
                }

                $nama_file = "cover_" . $id_buku . "." . $ext;
                $tujuan = $folder_upload . $nama_file;

                if (move_uploaded_file($_FILES['cover']['tmp_name'], $tujuan)) {
                    // update nama file ke database
                    $sql_update = "UPDATE buku SET cover_buku = ? WHERE id_buku = ?";
                    $stmt_update = $mysqli->prepare($sql_update);
                    $stmt_update->bind_param("si", $nama_file, $id_buku);
                    $stmt_update->execute();
                    $stmt_update->close();
                }
            }

            $pesan = "Buku berhasil disimpan!";
        } else {
            $pesan_error = "Terjadi kesalahan saat menyimpan data buku: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Buku</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tambah Buku</li>
    </ol>

    <?php if (!empty($pesan)): ?>
        <div class="alert alert-success" role="alert">
            <?= $pesan; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger" role="alert">
            <?= $pesan_error; ?>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Buku</label>
                    <input type="text" class="form-control" id="judul" name="judul" required>
                </div>

                <div class="mb-3">
                    <label for="kategori" class="form-label">Pilih Kategori</label><br>
                    <?php while ($kat = $result_kategori->fetch_assoc()): ?>
                        <label class="me-3">
                            <input type="checkbox" name="kategori[]" value="<?= $kat['id_kategori']; ?>">
                            <?= htmlspecialchars($kat['nama_kategori']); ?>
                        </label>
                    <?php endwhile; ?>
                </div>

                <div class="mb-3">
                    <label for="penulis" class="form-label">Penulis</label>
                    <input type="text" class="form-control" id="penulis" name="penulis" required>
                </div>
                <div class="mb-3">
                    <label for="penerbit" class="form-label">Penerbit</label>
                    <input type="text" class="form-control" id="penerbit" name="penerbit" required>
                </div>
                <div class="mb-3">
                    <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                    <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" required>
                </div>
                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="stok" name="stok" required>
                </div>

                <div class="mb-4">
                    <label for="cover" class="form-label">Upload Cover Buku</label>
                    <input type="file" class="form-control" id="cover" name="cover" accept="image/*">
                    <small class="text-muted">File akan disimpan di folder <code>uploads/buku/</code> dengan nama <code>cover_IDBUKU.jpg</code>.</small>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?hal=daftar-buku" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
</div>