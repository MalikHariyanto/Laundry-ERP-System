<?php
require_once '../../controller/InventoryController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$edit = false;
if (isset($_GET['id'])) {
    $edit = true;
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM stok_barang WHERE id_barang=$id");
    $row = mysqli_fetch_assoc($result);
}
?>

<div class="container custom-container">
    <div class="page-header mb-4">
        <h2><i class="fas fa-<?= $edit ? 'edit' : 'plus-circle' ?>"></i> 
            <?= $edit ? 'Edit Barang' : 'Tambah Barang Baru' ?>
        </h2>
        <div class="header-actions">
            <a href="list.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <style>
    .custom-container {
        max-width: 650px !important;
        margin: 0 auto;
        padding: 15px;
    }

    .card {
        border: none;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .input-group {
        width: 100%;
    }

    .form-control,
    .form-select {
        height: 38px;
        font-size: 14px;
    }

    .input-group-text {
        width: 40px;
        justify-content: center;
    }

    .form-actions {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .btn {
        height: 38px;
        padding: 0 15px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    @media (max-width: 768px) {
        .custom-container {
            padding: 10px;
        }
        
        .card-body {
            padding: 15px;
        }
    }
    </style>

    <div class="card">
        <div class="card-header bg-gradient">
            <h3 class="card-title mb-0">Form Data Barang</h3>
        </div>
        <div class="card-body">
            <form action="proses_form.php" method="POST" class="form-inventory">
                <?php if($edit): ?>
                    <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nama Barang</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                <input type="text" name="nama_barang" class="form-control" 
                                       value="<?= $edit ? $row['nama_barang'] : '' ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Deterjen" <?= $edit && $row['kategori'] == 'Deterjen' ? 'selected' : '' ?>>Deterjen</option>
                                <option value="Pewangi" <?= $edit && $row['kategori'] == 'Pewangi' ? 'selected' : '' ?>>Pewangi</option>
                                <option value="Pemutih" <?= $edit && $row['kategori'] == 'Pemutih' ? 'selected' : '' ?>>Pemutih</option>
                                <option value="Peralatan" <?= $edit && $row['kategori'] == 'Peralatan' ? 'selected' : '' ?>>Peralatan</option>
                                <option value="Supplies" <?= $edit && $row['kategori'] == 'Supplies' ? 'selected' : '' ?>>Supplies</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="satuan" class="form-control" 
                                   value="<?= $edit ? $row['satuan'] : '' ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Jumlah</label>
                            <input type="number" name="jumlah" class="form-control" 
                                   value="<?= $edit ? $row['jumlah'] : '' ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Harga Satuan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="harga_satuan" class="form-control" 
                                       value="<?= $edit ? $row['harga_satuan'] : '' ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Min Stok</label>
                                    <input type="number" name="min_stok" class="form-control" 
                                           value="<?= $edit ? $row['min_stok'] : '' ?>" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="form-label">Max Stok</label>
                                    <input type="number" name="max_stok" class="form-control" 
                                           value="<?= $edit ? $row['max_stok'] : '' ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <button type="reset" class="btn btn-secondary ms-2">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>