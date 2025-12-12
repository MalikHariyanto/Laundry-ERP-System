<?php
require_once '../../config/koneksi.php';

if (!isset($_GET['id'])) {
    echo "ID barang tidak ditemukan.";
    exit;
}

$id_barang = intval($_GET['id']);
$query = "SELECT * FROM stok_barang WHERE id_barang = $id_barang";
$result = mysqli_query($conn, $query);

if ($row = mysqli_fetch_assoc($result)) {
    ?>
    <h2>Detail Stok Barang</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID Barang</th>
            <td><?= htmlspecialchars($row['id_barang']) ?></td>
        </tr>
        <tr>
            <th>Nama Barang</th>
            <td><?= htmlspecialchars($row['nama_barang']) ?></td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td><?= htmlspecialchars($row['jumlah']) ?></td>
        </tr>
        <tr>
            <th>Satuan</th>
            <td><?= htmlspecialchars($row['satuan']) ?></td>
        </tr>
        <tr>
            <th>Tanggal Update</th>
            <td><?= htmlspecialchars($row['tanggal_update']) ?></td>
        </tr>
    </table>
    <br>
    <a href="list.php">Kembali ke Daftar Stok</a>
    <?php
} else {
    echo "Data barang tidak ditemukan.";
}
?>