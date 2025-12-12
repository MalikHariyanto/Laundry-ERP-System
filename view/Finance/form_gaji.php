<?php
require_once '../../controller/PayrollController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

try {
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    $controller = new PayrollController($conn);
    $karyawan = $controller->getAllKaryawan();
    
    if (!$karyawan) {
        throw new Exception("No employee data found");
    }
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error: ' . $e->getMessage() . '</div>';
    exit;
}
?>

<div class="container">
    <!-- Header with Icon and Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <div class="logo-box me-3">
                <i class="fas fa-money-check-alt text-dark fs-5"></i>
            </div>
            <h2 class="mb-0">Form Pembayaran Gaji</h2>
        </div>
        <a href="gaji.php" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($_GET['message'] ?? 'Terjadi kesalahan') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm" data-aos="fade-up">
        <div class="card-header bg-gradient">
            <h3 class="card-title mb-0">Data Pembayaran</h3>
        </div>
        <div class="card-body">
            <form action="proses_gaji.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Karyawan</label>
                    <select name="karyawan_id" class="form-select" required id="karyawan_id">
                        <option value="">Pilih Karyawan</option>
                        <?php while($row = mysqli_fetch_assoc($karyawan)): ?>
                            <option value="<?= $row['id'] ?>" data-gaji="<?= $row['gaji'] ?>">
                                <?= $row['nama'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Periode Gaji</label>
                    <div class="row">
                        <div class="col">
                            <select name="bulan" class="form-select" required>
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= $i ?>"><?= date('F', mktime(0, 0, 0, $i, 1)) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col">
                            <select name="tahun" class="form-select" required>
                                <?php for($i = date('Y'); $i <= date('Y')+1; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah Gaji</label>
                    <input type="number" name="gaji" id="gaji" class="form-control" readonly required>
                </div>
                <button type="submit" class="btn btn-primary">Proses Pembayaran</button>
                <a href="gaji.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<style>
.page-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
}

.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.card-header.bg-gradient {
    background: linear-gradient(135deg, #1976d2, #64b5f6);
    color: white;
    border-radius: 15px 15px 0 0;
    padding: 1.5rem;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 500;
}

.form-label {
    font-weight: 500;
    color: #1a1f36;
}

.input-group-text {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.form-control,
.form-select {
    border: 1px solid #dee2e6;
    padding: 0.6rem 1rem;
}

.form-actions {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #eee;
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.6rem 1.5rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #1976d2, #2196F3);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #1565c0, #1976d2);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(33,150,243,0.3);
}

.btn-secondary {
    background: linear-gradient(135deg, #78909c, #90a4ae);
    border: none;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #546e7a, #78909c);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(120,144,156,0.3);
}

.logo-box {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 8px;
}

.fs-5 {
    font-size: 1.15rem !important;
}
</style>

<script>
document.getElementById('karyawan_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const gaji = selectedOption.getAttribute('data-gaji');
    document.getElementById('gaji').value = gaji || '';
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

<?php include '../layout/footer.php'; ?>