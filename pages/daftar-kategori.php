<?php
// proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

// query ke database
$sql = "SELECT * FROM kategori ORDER BY id_kategori DESC";
$result = $mysqli->query($sql);
if (!$result) {
    die("Query Error : " . $mysqli->error);
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Kategori</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Daftar Kategori</li>
    </ol>

    <!-- NOTIFIKASI -->
    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Data kategori berhasil ditambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif($_GET['msg'] == 'update'): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Data kategori berhasil diubah!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif($_GET['msg'] == 'delete'): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Data kategori berhasil dihapus!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif($_GET['msg'] == 'error'): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Terjadi kesalahan! Silakan coba lagi.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-body">
            <a href="index.php?hal=tambah-kategori" class="btn btn-success mb-3">Tambah Kategori</a>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>NO</th>
                            <th>Nama Kategori</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) : ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['nama_kategori']); ?></td>
                                    <td>
                                        <a href="index.php?hal=ubah-kategori&id=<?php echo $row['id_kategori']; ?>" class="btn btn-warning btn-sm">Ubah</a>
                                        <a href="index.php?hal=hapus-kategori&id=<?php echo $row['id_kategori']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile;
                        } else { ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data kategori</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<!-- Bootstrap JS (untuk alert dismiss) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>