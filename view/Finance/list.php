<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new FinanceController($conn);
$transactions = $controller->getAllTransactions(); 
$summary = $controller->summary(); // Menggunakan method summary() yang sudah ada
$data = mysqli_fetch_assoc($summary);
?>

<div class="container">
    <div class="page-header mb-4">
        <h2><i class="fas fa-money-bill-wave"></i> Manajemen Keuangan</h2>
        <div class="header-actions">
            <a href="form_transaksi.php" class="btn btn-custom-primary btn-with-icon">
                <div class="btn-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <span>Tambah Transaksi</span>
            </a>
        </div>
    </div>

    <!-- Finance Summary -->
    <div class="finance-summary">
        <div class="summary-card" data-aos="fade-up" data-aos-delay="100">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-circle-up"></i>
            </div>
            <div class="info">
                <span class="label">Total Pemasukan</span>
                <h3 class="amount">Rp <?= number_format($data['total_pemasukan'] ?? 0, 0, ',', '.') ?></h3>
                <div class="trend positive">
                    <i class="fas fa-chart-line"></i>
                    <span>Pendapatan</span>
                </div>
            </div>
        </div>

        <div class="summary-card" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-circle-down"></i>
            </div>
            <div class="info">
                <span class="label">Total Pengeluaran</span>
                <h3 class="amount">Rp <?= number_format($data['total_pengeluaran'] ?? 0, 0, ',', '.') ?></h3>
                <div class="trend negative">
                    <i class="fas fa-chart-line"></i>
                    <span>Pengeluaran</span>
                </div>
            </div>
        </div>

        <div class="summary-card" data-aos="fade-up" data-aos-delay="300">
            <div class="icon-wrapper">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="info">
                <span class="label">Saldo</span>
                <h3 class="amount">Rp <?= number_format(($data['total_pemasukan'] ?? 0) - ($data['total_pengeluaran'] ?? 0), 0, ',', '.') ?></h3>
                <div class="trend">
                    <i class="fas fa-balance-scale"></i>
                    <span>Total Saldo</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card card-animate" data-aos="fade-up">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $transactions = $controller->getAllTransactions();
                        while($row = mysqli_fetch_assoc($transactions)):
                            // Set default status jika tidak ada
                            $status = $row['status'] ?? 'Pending';
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td><?= $row['kode_transaksi'] ?></td>
                            <td>
                                <span class="badge bg-<?= $row['jenis'] === 'Pemasukan' ? 'success' : 'danger' ?>">
                                    <?= $row['jenis'] ?>
                                </span>
                            </td>
                            <td><?= $row['kategori'] ?></td>
                            <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= $status === 'Selesai' ? 'success' : 'warning' ?>">
                                    <?= $status ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail_transaksi.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-custom-warning" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-custom-danger" 
                                            onclick="deleteTransaction(<?= $row['id'] ?>)"
                                            data-bs-toggle="tooltip" 
                                            title="Hapus">
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
</div>

<?php include '../layout/footer.php'; ?>

<style>
.header-actions {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.action-group {
    display: flex;
    gap: 0.5rem;
}

.btn-custom-primary,
.btn-custom-info,
.btn-custom-success {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-decoration: none;
    color: white;
    position: relative;
    overflow: hidden;
}

.btn-custom-primary {
    background: linear-gradient(135deg, #1976d2, #2196F3);
}

.btn-custom-info {
    background: linear-gradient(135deg, #0288d1, #29b6f6);
}

.btn-custom-success {
    background: linear-gradient(135deg, #2e7d32, #43a047);
}

.btn-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

/* Hover effects */
.btn-custom-primary:hover,
.btn-custom-info:hover,
.btn-custom-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    color: white;
}

.btn-custom-primary:hover {
    background: linear-gradient(135deg, #1565c0, #1976d2);
}

.btn-custom-info:hover {
    background: linear-gradient(135deg, #0277bd, #0288d1);
}

.btn-custom-success:hover {
    background: linear-gradient(135deg, #2e7d32, #388e3c);
}

/* Button animation */
.btn-custom-primary:hover .btn-icon-wrapper,
.btn-custom-info:hover .btn-icon-wrapper,
.btn-custom-success:hover .btn-icon-wrapper {
    animation: spin 0.5s ease;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    25% { transform: rotate(15deg); }
    75% { transform: rotate(-15deg); }
    100% { transform: rotate(0deg); }
}

/* Ripple effect */
.btn-custom-primary::after,
.btn-custom-info::after,
.btn-custom-success::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 120%;
    height: 120%;
    background: rgba(255,255,255,0.2);
    transform: translate(-50%, -50%) scale(0);
    border-radius: 50%;
    transition: transform 0.5s;
}

.btn-custom-primary:active::after,
.btn-custom-info:active::after,
.btn-custom-success:active::after {
    transform: translate(-50%, -50%) scale(1);
    opacity: 0;
}

@media (max-width: 768px) {
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .action-group {
        width: 100%;
        gap: 0.5rem;
    }
    
    .btn-custom-primary,
    .btn-custom-info,
    .btn-custom-success {
        width: 100%;
        justify-content: center;
    }
}

/* Table Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
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
    color: white;
    border: none;
}

.btn-custom-info {
    background: linear-gradient(45deg, #0288d1, #29b6f6);
}

.btn-custom-secondary {
    background: linear-gradient(45deg, #546e7a, #78909c);
}

.btn-custom-warning {
    background: linear-gradient(45deg, #f57c00, #ffb74d);
}

.btn-action:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-action:active {
    transform: translateY(0) scale(0.95);
}

.btn-action i {
    font-size: 0.875rem;
    transition: transform 0.3s ease;
}

.btn-action:hover i {
    transform: scale(1.1);
}

/* Badge Styling */
.code-badge {
    background: #e3f2fd;
    color: #1565c0;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
}

.category-badge {
    background: #f5f5f5;
    color: #666;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.875rem;
}

/* Animation */
.card-animate {
    animation: slideIn 0.5s ease-out;
}

.animate-row {
    animation: fadeIn 0.5s ease-out forwards;
    opacity: 0;
}

@keyframes slideIn {
    from {
        transform: translateY(20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Card Styling */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
}

/* Table Styling */
.table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #1a1f36;
}

.table td {
    vertical-align: middle;
}

.table tr {
    transition: all 0.3s ease;
}

.table tr:hover {
    background-color: #f8f9fa;
    transform: scale(1.01);
}

/* Add animation for modal */
.modal.fade .modal-dialog {
    transform: scale(0.8);
    transition: transform 0.3s ease;
}

.modal.show .modal-dialog {
    transform: scale(1);
}

/* New styles for finance summary */
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
    transition: all 0.3s ease;
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

.summary-card:first-child .icon-wrapper {
    background: linear-gradient(135deg, #4CAF50, #81C784);
    color: white;
}

.summary-card:nth-child(2) .icon-wrapper {
    background: linear-gradient(135deg, #f44336, #E57373);
    color: white;
}

.summary-card:last-child .icon-wrapper {
    background: linear-gradient(135deg, #2196F3, #64B5F6);
    color: white;
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

.trend.positive {
    color: #2e7d32;
    background: rgba(46, 125, 50, 0.1);
}

.trend.negative {
    color: #c62828;
    background: rgba(198, 40, 40, 0.1);
}

.trend i {
    font-size: 0.875rem;
}

/* Animation for cards */
[data-aos] {
    opacity: 0;
    transform: translateY(20px);
    transition: transform 0.6s ease, opacity 0.6s ease;
}

[data-aos].aos-animate {
    opacity: 1;
    transform: translateY(0);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.summary-card:hover .trend {
    animation: pulse 1s ease infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });

    // Add button animations
    document.querySelectorAll('.btn-with-icon').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.btn-icon-wrapper i');
            icon.style.transform = 'scale(1.2) rotate(15deg)';
        });

        btn.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.btn-icon-wrapper i');
            icon.style.transform = 'scale(1) rotate(0deg)';
        });

        btn.addEventListener('click', function() {
            const icon = this.querySelector('.btn-icon-wrapper i');
            icon.style.transform = 'scale(0.8)';
            setTimeout(() => {
                icon.style.transform = 'scale(1)';
            }, 150);
        });
    });

    // Add hover animation for summary cards
    document.querySelectorAll('.summary-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.querySelector('.icon-wrapper i').style.transform = 'scale(1.2) rotate(15deg)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.querySelector('.icon-wrapper i').style.transform = 'scale(1) rotate(0)';
        });
    });
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

// Handle bukti preview
document.querySelectorAll('.view-bukti').forEach(button => {
    button.addEventListener('click', function() {
        const buktiUrl = this.getAttribute('data-bukti');
        document.getElementById('buktiPreview').src = buktiUrl;
    });
});

// Animate rows on load
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Add hover effect for action buttons
document.querySelectorAll('.btn-action').forEach(btn => {
    btn.addEventListener('mouseenter', function() {
        this.querySelector('i').style.transform = 'scale(1.1)';
    });
    
    btn.addEventListener('mouseleave', function() {
        this.querySelector('i').style.transform = 'scale(1)';
    });
});

// Add smooth animation for buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-with-icon').forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            this.querySelector('.btn-icon').style.transform = 'rotate(15deg)';
        });
        
        btn.addEventListener('mouseleave', function() {
            this.querySelector('.btn-icon').style.transform = 'rotate(0deg)';
        });

        btn.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });
    });
});

// Initialize AOS
AOS.init({
    duration: 800,
    once: true,
    offset: 50
});

function deleteTransaction(id) {
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
        fetch('delete_transaksi.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaksi berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal menghapus transaksi: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus transaksi');
        });
    }
}
</script>