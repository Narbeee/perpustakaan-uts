<?php
// proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

include __DIR__ . '/../config/database.php';

// query ke database
$sql = "SELECT * FROM anggota ORDER BY id_anggota DESC";
$result = $mysqli->query($sql);
if (!$result) {
    die("Query Error : " . $mysqli->error);
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Anggota</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Data Anggota</li>
    </ol>

    <!-- Tombol Tambah Anggota -->
    <div class="mb-3">
        <a href="index.php?hal=tambah-anggota" class="btn-tambah">+ Tambah Anggota</a>
    </div>

    <!-- Tabel Anggota -->
    <table class="table table-bordered table-striped table-hover align-middle">
        <thead class="table-dark text-center">
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>No Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = $result->fetch_assoc()) {
            ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <?php if (!empty($row['foto_profil']) && file_exists(__DIR__ . '/../uploads/users/' . $row['foto_profil'])): ?>
                                <img src="uploads/users/<?= htmlspecialchars($row['foto_profil']) ?>"
                                    alt="Foto Profil"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px; border: 1px solid #ccc;">
                            <?php else: ?>
                                <img src="assets/no-profile.png"
                                    alt="Default Foto"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px; border: 1px solid #ccc;">
                            <?php endif; ?>

                            <span style="font-weight: 500;"><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                    <td><?= htmlspecialchars($row['no_telepon']) ?></td>
                    <td>
                        <a href="index.php?hal=ubah-password&id_anggota=<?= $row['id_anggota'] ?>"
                            class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                            <i class="fa fa-key"></i> Ubah
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<!-- CSS styling biar sama kayak daftar-buku -->
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