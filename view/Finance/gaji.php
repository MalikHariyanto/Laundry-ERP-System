<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../controller/PayrollController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new PayrollController($conn);
$karyawan = $controller->getRiwayatGaji();
?>

<div class="container">
    <!-- Header with Simple Icon -->
    <div class="d-flex align-items-center header-section mb-3" data-aos="fade-right">
        <div class="logo-box bg-light rounded-3 me-3">
            <i class="fas fa-dollar-sign text-dark"></i>
        </div>
        <h2 class="mb-0">Manajemen Gaji Karyawan</h2>
    </div>

    <!-- Action Button -->
    <div class="mb-4 mt-2" data-aos="fade-up" data-aos-delay="100">
        <a href="form_gaji.php" class="btn btn-custom-primary">
            <span class="plus-icon">+</span>
            Tambah Pembayaran
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="finance-summary" data-aos="fade-up" data-aos-delay="200">
        <!-- Total Gaji Card -->
        <div class="summary-card">
            <div class="icon-wrapper bg-success bg-opacity-10">
                <i class="fas fa-wallet text-success"></i>
            </div>
            <div class="info">
                <span class="label">Total Gaji Bulan Ini</span>
                <h3 class="amount" id="totalGaji">
                    Rp <?= number_format($controller->getTotalGajiBulanIni(), 0, ',', '.') ?>
                </h3>
                <div class="trend positive">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Periode <?= date('F Y') ?></span>
                </div>
            </div>
        </div>

        <!-- Average Gaji Card -->
        <div class="summary-card">
            <div class="icon-wrapper bg-danger bg-opacity-10">
                <i class="fas fa-users text-danger"></i>
            </div>
            <div class="info">
                <span class="label">Rata-rata Gaji</span>
                <h3 class="amount" id="avgGaji">
                    Rp <?= number_format($controller->getAverageGaji(), 0, ',', '.') ?>
                </h3>
                <div class="trend">
                    <i class="fas fa-calculator"></i>
                    <span>Per Karyawan</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="card custom-card" data-aos="fade-up" data-aos-delay="300">
        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>Nama Karyawan</th>
                        <th>Periode</th>
                        <th>Gaji</th>
                        <th>Status</th>
                        <th>Tanggal Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($karyawan)): ?>
                        <tr class="animate-row">
                            <td><?= $row['nama'] ?></td>
                            <td><?= date('F Y', mktime(0, 0, 0, $row['bulan'], 1, $row['tahun'])) ?></td>
                            <td>Rp <?= number_format($row['gaji'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= $row['status_pembayaran'] == 'Dibayar' ? 'success' : 'warning' ?> bg-opacity-10 text-<?= $row['status_pembayaran'] == 'Dibayar' ? 'success' : 'warning' ?>">
                                    <?= $row['status_pembayaran'] ?>
                            </span>
                            </td>
                            <td><?= $row['tanggal_pembayaran'] ? date('d/m/Y', strtotime($row['tanggal_pembayaran'])) : '-' ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-action btn-warning" onclick="window.location.href='edit_gaji.php?id=<?= $row['id'] ?>'">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-action btn-danger" onclick="deleteGaji(<?= $row['id'] ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.header-section {
    margin-top: -0.5rem;
}

.btn-primary {
    background: #0d6efd;
    border: none;
    padding: 0.4rem 1rem;
    font-size: 0.875rem;
    border-radius: 6px;
}

.btn-primary:hover {
    background: #0b5ed7;
    transform: translateY(-1px);
}

.logo-box {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.08);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.btn-custom-primary {
    background-color: #0d6efd;
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 14px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-custom-primary:hover {
    background-color: #0b5ed7;
    color: white;
}

.plus-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    background: rgba(255, 255, 255, 1);
    border-radius: 50%;
    font-size: 16px;
    font-weight: 500;
    line-height: 1;
    color: #0d6efd;
}

.me-2 {
    margin-right: 8px;
}

.finance-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

.icon-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: all 0.3s ease;
}

.summary-card:hover .icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.info {
    flex: 1;
}

.label {
    display: block;
    color: #666;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.amount {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
    color: #1a1f36;
    transition: all 0.3s ease;
}

.summary-card:hover .amount {
    transform: scale(1.05);
}

.trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    width: fit-content;
}

.btn-action {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.custom-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    overflow: hidden;
}

.custom-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #1a1f36;
}

.animate-row {
    animation: slideIn 0.5s ease forwards;
    opacity: 0;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });

    // Add animation delay to table rows
    document.querySelectorAll('.animate-row').forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
    });
});

// Function to update salary stats
function updateSalaryStats() {
    fetch('get_salary_stats.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('totalGaji').innerHTML = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.total);
            document.getElementById('avgGaji').innerHTML = 
                'Rp ' + new Intl.NumberFormat('id-ID').format(data.average);
        })
        .catch(error => console.error('Error:', error));
}

// Update stats after delete
function deleteGaji(id) {
    if(confirm('Apakah Anda yakin ingin menghapus data gaji ini?')) {
        fetch('delete_gaji.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                updateSalaryStats(); // Update stats after successful deletion
                location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan saat menghapus data');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus data');
        });
    }
}
</script>

<?php include '../layout/footer.php'; ?>