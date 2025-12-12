<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

if (!isset($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = intval($_GET['id']);
$controller = new FinanceController($conn);
$transaksi = $controller->getTransactionById($id);

if (!$transaksi) {
    header('Location: list.php');
    exit;
}

// Set default status if not set
$status = isset($transaksi['status']) ? $transaksi['status'] : 'Pending';
?>

<div class="container">
    <div class="page-header mb-4">
        <h2><i class="fas fa-edit"></i> Edit Transaksi</h2>
        <div class="header-actions">
            <a href="list.php" class="btn btn-custom-back btn-with-icon">
                <div class="btn-icon">
                    <i class="fas fa-arrow-left"></i>
                </div>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="card" data-aos="fade-up">
        <div class="card-header bg-gradient">
            <h3 class="card-title mb-0">Form Edit Transaksi</h3>
        </div>
        <div class="card-body">
            <form action="update_transaksi.php" method="POST" class="edit-form">
                <input type="hidden" name="id" value="<?= $transaksi['id'] ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Kode Transaksi</label>
                            <input type="text" class="form-control" value="<?= $transaksi['kode_transaksi'] ?>" readonly>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Jenis Transaksi</label>
                            <select name="jenis" class="form-select" required>
                                <option value="Pemasukan" <?= $transaksi['jenis'] == 'Pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                                <option value="Pengeluaran" <?= $transaksi['jenis'] == 'Pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="<?= date('Y-m-d', strtotime($transaksi['tanggal'])) ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <optgroup label="Pemasukan">
                                    <option value="Jasa Laundry" <?= $transaksi['kategori'] == 'Jasa Laundry' ? 'selected' : '' ?>>Jasa Laundry</option>
                                    <option value="Lainnya" <?= $transaksi['kategori'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </optgroup>
                                <optgroup label="Pengeluaran">
                                    <option value="Bahan Baku" <?= $transaksi['kategori'] == 'Bahan Baku' ? 'selected' : '' ?>>Bahan Baku</option>
                                    <option value="Gaji Karyawan" <?= $transaksi['kategori'] == 'Gaji Karyawan' ? 'selected' : '' ?>>Gaji Karyawan</option>
                                    <option value="Operasional" <?= $transaksi['kategori'] == 'Operasional' ? 'selected' : '' ?>>Operasional</option>
                                    <option value="Utilitas" <?= $transaksi['kategori'] == 'Utilitas' ? 'selected' : '' ?>>Utilitas</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Jumlah</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah" class="form-control" 
                                       value="<?= $transaksi['jumlah'] ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="Pending" <?= ($status == 'Pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="Selesai" <?= ($status == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                <option value="Batal" <?= ($status == 'Batal') ? 'selected' : '' ?>>Batal</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="form-group mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= $transaksi['keterangan'] ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-custom-primary btn-with-icon">
                        <div class="btn-icon">
                            <i class="fas fa-save"></i>
                        </div>
                        <span>Update Transaksi</span>
                    </button>
                    <a href="list.php" class="btn btn-custom-secondary btn-with-icon ms-2">
                        <div class="btn-icon">
                            <i class="fas fa-times"></i>
                        </div>
                        <span>Batal</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Header Styling */
.transaction-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.transaction-status {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.transaction-date {
    font-size: 0.9rem;
    opacity: 0.9;
}

.transaction-code h3 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 500;
}

/* Detail Section Styling */
.detail-section {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.detail-section h4 {
    color: #1a1f36;
    margin-bottom: 1rem;
    font-size: 1.1rem;
    font-weight: 600;
}

.detail-table {
    width: 100%;
}

.detail-table tr {
    border-bottom: 1px solid #eee;
}

.detail-table tr:last-child {
    border-bottom: none;
}

.detail-table td {
    padding: 0.75rem 0;
}

.detail-table .label {
    color: #666;
    width: 40%;
}

.detail-table .value {
    font-weight: 500;
    color: #1a1f36;
}

/* Description Box */
.description-box {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #eee;
    min-height: 100px;
}

/* Document Preview */
.document-preview {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #eee;
}

.document-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #1976d2;
    text-decoration: none;
    transition: color 0.3s;
}

.document-link:hover {
    color: #1565c0;
}

/* Timeline Styling */
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #eee;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
    margin-bottom: 1.5rem;
}

.timeline-point {
    position: absolute;
    left: 0.35rem;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.timeline-content {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
}

.timeline-content h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 500;
}

/* Button Styling */
.btn-custom-back {
    background: linear-gradient(45deg, #78909c, #90a4ae);
    color: white;
}

.btn-custom-back:hover {
    background: linear-gradient(45deg, #546e7a, #78909c);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(120,144,156,0.3);
    color: white;
    text-decoration: none;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #eee;
    display: flex;
    gap: 1rem;
}

.btn-custom-primary {
    background: linear-gradient(135deg, #1976d2, #2196F3);
    color: white;
}

.btn-custom-secondary {
    background: linear-gradient(135deg, #78909c, #90a4ae);
    color: white;
}
</style>

<script>
function printDetail() {
    window.print();
}

// Initialize AOS
AOS.init({
    duration: 800,
    once: true
});

// Script to handle kategori options based on jenis
document.querySelector('select[name="jenis"]').addEventListener('change', function() {
    const kategoriSelect = document.querySelector('select[name="kategori"]');
    const optgroups = kategoriSelect.querySelectorAll('optgroup');
    
    optgroups.forEach(group => {
        if (group.label === this.value) {
            group.style.display = '';
            group.querySelectorAll('option')[0].selected = true;
        } else {
            group.style.display = 'none';
        }
    });
});
</script>

<?php include '../layout/footer.php'; ?>