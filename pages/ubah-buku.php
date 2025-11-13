<?php
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

include __DIR__ . '/../config/database.php';

// Ambil data buku berdasarkan ID
$id_buku = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql_buku = "SELECT * FROM buku WHERE id_buku = $id_buku";
$result_buku = $mysqli->query($sql_buku);
if (!$result_buku || $result_buku->num_rows == 0) {
    die("Buku tidak ditemukan!");
}
$buku = $result_buku->fetch_assoc();

// Ambil semua kategori untuk ditampilkan
$sql_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = $mysqli->query($sql_kategori);

// Ambil kategori yang sudah dimiliki buku ini
$sql_buku_kategori = "SELECT id_kategori FROM buku_kategori WHERE id_buku = $id_buku";
$result_buku_kategori = $mysqli->query($sql_buku_kategori);
$kategori_terpilih = [];
while ($row = $result_buku_kategori->fetch_assoc()) {
    $kategori_terpilih[] = $row['id_kategori'];
}

// Proses update data buku
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $stok = $_POST['stok'];
    $kategori_dipilih = isset($_POST['kategori']) ? $_POST['kategori'] : [];

    // Cek jika user upload cover baru
    $cover_baru = $_FILES['cover_buku']['name'];
    if (!empty($cover_baru)) {
        $target_dir = __DIR__ . '/../uploads/buku/';
        $target_file = $target_dir . basename($cover_baru);
        move_uploaded_file($_FILES['cover_buku']['tmp_name'], $target_file);

        // Update data dengan cover baru
        $sql_update = "UPDATE buku 
                       SET judul='$judul', penulis='$penulis', penerbit='$penerbit', 
                           tahun_terbit='$tahun_terbit', stok='$stok', cover_buku='$cover_baru' 
                       WHERE id_buku=$id_buku";
    } else {
        // Update tanpa ubah cover
        $sql_update = "UPDATE buku 
                       SET judul='$judul', penulis='$penulis', penerbit='$penerbit', 
                           tahun_terbit='$tahun_terbit', stok='$stok' 
                       WHERE id_buku=$id_buku";
    }

    if ($mysqli->query($sql_update)) {
        // Hapus kategori lama dulu
        $mysqli->query("DELETE FROM buku_kategori WHERE id_buku=$id_buku");

        // Simpan kategori baru
        foreach ($kategori_dipilih as $id_kat) {
            $mysqli->query("INSERT INTO buku_kategori (id_buku, id_kategori) VALUES ($id_buku, $id_kat)");
        }

        echo "<script>
            alert('Data buku berhasil diubah!');
            window.location='index.php?hal=daftar-buku';
        </script>";
        exit;
    } else {
        echo "<script>alert('Gagal mengubah data buku: " . $mysqli->error . "');</script>";
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Ubah Buku</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="index.php?hal=daftar-buku">Daftar Buku</a></li>
        <li class="breadcrumb-item active">Ubah Buku</li>
    </ol>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Judul Buku</label>
            <input type="text" name="judul" class="form-control" required value="<?= htmlspecialchars($buku['judul']); ?>">
        </div>

        <div class="mb-3">
            <label>Penulis</label>
            <input type="text" name="penulis" class="form-control" required value="<?= htmlspecialchars($buku['penulis']); ?>">
        </div>

        <div class="mb-3">
            <label>Penerbit</label>
            <input type="text" name="penerbit" class="form-control" required value="<?= htmlspecialchars($buku['penerbit']); ?>">
        </div>

        <div class="mb-3">
            <label>Tahun Terbit</label>
            <input type="number" name="tahun_terbit" class="form-control" required value="<?= htmlspecialchars($buku['tahun_terbit']); ?>">
        </div>

        <div class="mb-3">
            <label>Stok</label>
            <input type="number" name="stok" class="form-control" required value="<?= htmlspecialchars($buku['stok']); ?>">
        </div>

        <!-- ✅ Tambahan: Pilih kategori -->
        <div class="mb-3">
            <label>Kategori Buku</label><br>
            <?php while ($kat = $result_kategori->fetch_assoc()): ?>
                <label style="margin-right: 15px;">
                    <input type="checkbox" name="kategori[]" value="<?= $kat['id_kategori']; ?>"
                        <?= in_array($kat['id_kategori'], $kategori_terpilih) ? 'checked' : ''; ?>>
                    <?= htmlspecialchars($kat['nama_kategori']); ?>
                </label>
            <?php endwhile; ?>
        </div>

        <!-- ✅ Cover Buku -->
        <div class="mb-3">
            <label>Cover Buku</label><br>
            <img src="uploads/<?php echo htmlspecialchars($buku['cover'] ?? "default_png"); ?>" width="100" alt="cover"><br>
            <br><small>Upload jika ingin mengganti cover:</small><br>
            <input type="file" name="cover_buku" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="index.php?hal=daftar-buku" class="btn btn-secondary">Kembali</a>
    </form>
</div>