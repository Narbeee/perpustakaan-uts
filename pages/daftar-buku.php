<?php
// proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

include __DIR__ . '/../config/database.php';

// ✅ Perbaikan query agar bisa ambil kategori banyak
$sql = "
    SELECT 
        b.*, 
        GROUP_CONCAT(k.nama_kategori SEPARATOR ', ') AS kategori_list
    FROM buku b
    LEFT JOIN buku_kategori bk ON b.id_buku = bk.id_buku
    LEFT JOIN kategori k ON bk.id_kategori = k.id_kategori
    GROUP BY b.id_buku
    ORDER BY b.id_buku DESC
";

$result = $mysqli->query($sql);
if (!$result) {
    die('Query Error : ' . $mysqli->error);
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Daftar Buku</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Data Buku</li>
    </ol>

    <!-- Tombol Tambah Buku -->
    <div class="mb-3">
        <a href="index.php?hal=tambah-buku" class="btn-tambah">+ Tambah Buku</a>
    </div>

    <!-- Tabel Buku -->
    <table class="simple-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul Buku</th>
                <th>Kategori</th> <!-- ✅ Tambah kolom kategori -->
                <th>Penulis</th>
                <th>Penerbit</th>
                <th>Tahun Terbit</th>
                <th>Stok</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()):
                $id_buku = $row['id_buku'];

                $folder_path = __DIR__ . '/../uploads/buku/';
                $folder_url  = 'uploads/buku/';
                $default_cover = 'assets/no-cover.png';

                $cover_file = !empty($row['cover_buku']) ? $row['cover_buku'] : '';
                if ($cover_file && file_exists($folder_path . $cover_file)) {
                    $url_cover = $folder_url . $cover_file;
                } else {
                    $url_cover = $default_cover;
                }
            ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <img src="<?= htmlspecialchars($url_cover); ?>" alt="Cover Buku"
                                style="width: 55px; height: 70px; object-fit: cover; border: 1px solid #ccc; border-radius: 4px;">
                            <span><?= htmlspecialchars($row['judul']); ?></span>
                        </div>
                    </td>

                    <!-- ✅ Tambah tampilan kategori -->
                    <td><i><?= !empty($row['kategori_list']) ? htmlspecialchars($row['kategori_list']) : 'Tidak ada kategori'; ?></i></td>

                    <td><?= htmlspecialchars($row['penulis']); ?></td>
                    <td><?= htmlspecialchars($row['penerbit']); ?></td>
                    <td><?= htmlspecialchars($row['tahun_terbit']); ?></td>
                    <td><?= htmlspecialchars($row['stok']); ?></td>
                    <td>
                        <a href="index.php?hal=ubah-buku&id=<?= $row['id_buku']; ?>" class="btn-edit">Ubah</a>
                        <a href="index.php?hal=hapus-buku&id=<?= $row['id_buku']; ?>"
                            class="btn-hapus"
                            onclick="return confirm('Yakin ingin menghapus buku ini?');">
                            Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- CSS styling tabel biar simple seperti kategori -->
<style>
    .simple-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #dee2e6;
        font-size: 16px;
    }

    .simple-table th {
        background-color: #212529;
        color: white;
        text-align: left;
        padding: 10px;
    }

    .simple-table td {
        border-top: 1px solid #dee2e6;
        padding: 10px;
        vertical-align: middle;
    }

    .simple-table tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .btn-tambah {
        background-color: #198754;
        color: white;
        padding: 8px 14px;
        text-decoration: none;
        border-radius: 4px;
        font-weight: 500;
    }

    .btn-edit {
        background-color: #ffc107;
        color: black;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 500;
        margin-right: 5px;
    }

    .btn-hapus {
        background-color: #dc3545;
        color: white;
        text-decoration: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 500;
    }

    .btn-edit:hover,
    .btn-hapus:hover,
    .btn-tambah:hover {
        opacity: 0.85;
    }
</style>