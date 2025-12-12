<?php
require_once '../../controller/PayrollController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new PayrollController($conn);

if (!isset($_GET['id'])) {
    header('Location: gaji.php');
    exit;
}

$id = intval($_GET['id']);
$gaji = $controller->getGajiById($id);

if (!$gaji) {
    header('Location: gaji.php');
    exit;
}
?>

<div class="container">
    <div class="page-header mb-4">
        <h2><i class="fas fa-edit"></i> Edit Data Gaji</h2>
        <div class="header-actions">
            <a href="gaji.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow-sm" data-aos="fade-up">
        <div class="card-header bg-gradient">
            <h3 class="card-title mb-0">Form Edit Gaji</h3>
        </div>
        <div class="card-body">
            <form action="proses_gaji.php" method="POST" class="edit-form">
                <input type="hidden" name="id" value="<?= $gaji['id'] ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input type="text" class="form-control" value="<?= $gaji['nama'] ?>" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Gaji Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="gaji" class="form-control" 
                                       value="<?= $gaji['gaji'] ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Status Pembayaran</label>
                            <select name="status_pembayaran" class="form-select" required>
                                <option value="Belum Dibayar" <?= $gaji['status_pembayaran'] == 'Belum Dibayar' ? 'selected' : '' ?>>
                                    Belum Dibayar
                                </option>
                                <option value="Dibayar" <?= $gaji['status_pembayaran'] == 'Dibayar' ? 'selected' : '' ?>>
                                    Dibayar
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="gaji.php" class="btn btn-secondary ms-2">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.edit-form .form-group {
    margin-bottom: 1.5rem;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
}

.btn {
    padding: 0.6rem 1.5rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header.bg-gradient {
    background: linear-gradient(135deg, #1976d2, #64b5f6);
    color: white;
    padding: 1.5rem;
}
</style>

<?php include '../layout/footer.php'; ?>