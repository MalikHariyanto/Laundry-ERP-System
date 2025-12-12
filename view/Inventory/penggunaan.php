<?php
require_once '../../controller/InventoryController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new InventoryController($conn);

// Tambahkan query untuk riwayat penggunaan
$riwayat_query = "SELECT ps.*, sb.nama_barang, sb.satuan 
                  FROM penggunaan_stok ps
                  JOIN stok_barang sb ON ps.id_barang = sb.id_barang
                  ORDER BY ps.tanggal_pakai DESC";
$riwayat = mysqli_query($conn, $riwayat_query);
?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-clipboard-list"></i> Pencatatan Penggunaan Stok</h2>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="proses_penggunaan.php" method="POST">
                <div class="form-group">
                    <label>Pilih Barang</label>
                    <select name="id_barang" class="form-control" required>
                        <option value="">Pilih Barang</option>
                        <?php 
                        $data = $controller->index();
                        while($row = mysqli_fetch_assoc($data)): 
                        ?>
                        <option value="<?= $row['id_barang'] ?>" 
                                data-stok="<?= $row['jumlah'] ?>">
                            <?= $row['nama_barang'] ?> (Stok: <?= $row['jumlah'] ?> <?= $row['satuan'] ?>)
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah Penggunaan</label>
                    <input type="number" name="jumlah_pakai" class="form-control" 
                           min="1" required>
                    <small class="text-muted">Stok tersedia: <span id="stok-tersedia">0</span></small>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Catat Penggunaan
                </button>
            </form>
        </div>
    </div>

    <!-- Riwayat Penggunaan -->
    <div class="card mt-4">
        <div class="card-header">
            <h3>Riwayat Penggunaan</h3>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kode</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($riwayat)): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($row['tanggal_pakai'])) ?></td>
                        <td><?= $row['kode_penggunaan'] ?></td>
                        <td><?= $row['nama_barang'] ?></td>
                        <td><?= $row['jumlah_pakai'] . ' ' . $row['satuan'] ?></td>
                        <td><?= $row['keterangan'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="id_barang"]').addEventListener('change', function() {
    let stokTersedia = this.options[this.selectedIndex].dataset.stok;
    document.getElementById('stok-tersedia').textContent = stokTersedia;
    document.querySelector('input[name="jumlah_pakai"]').max = stokTersedia;
});
</script>

<?php include '../layout/footer.php'; ?>