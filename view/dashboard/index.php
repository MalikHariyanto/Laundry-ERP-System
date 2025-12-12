<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>ERP Laundry - Dashboard</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; }
        .container { max-width: 800px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);}
        h1 { text-align: center; color: #2196F3; }
        .row { display: flex; justify-content: space-between; }
        .card { background: #f9f9f9; padding: 20px; border-radius: 8px; margin: 10px; flex: 1; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .card h3 { color: #333; }
        .btn { display: inline-block; background: #2196F3; color: #fff; padding: 10px 20px; border-radius: 6px; text-align: center; text-decoration: none; font-size: 16px; transition: background 0.2s;}
        .btn:hover { background: #1769aa; }
    </style>
</head>
<body>
<?php
require_once '../../config/koneksi.php';
require_once '../../controller/InventoryController.php';
require_once '../../controller/FinanceController.php';
require_once '../../controller/PayrollController.php';

$inventoryController = new InventoryController($conn);
$financeController = new FinanceController($conn);
$payrollController = new PayrollController($conn);

include '../layout/header.php';
include '../layout/sidebar.php';

// Get financial summary
$summary = mysqli_fetch_assoc($financeController->summary());
$totalPemasukan = $summary['total_pemasukan'] ?? 0;
$totalPengeluaran = $summary['total_pengeluaran'] ?? 0;
$saldoTersedia = $totalPemasukan - $totalPengeluaran;

// Get inventory summary
$totalItems = $inventoryController->getTotalItems();
$lowStock = $inventoryController->getLowStockCount();
$inventoryValue = $inventoryController->getTotalValue();

// Get monthly data for charts
$monthlyData = $financeController->getMonthlyChartData();
?>

<div class="container dashboard-container">
    <!-- Updated Dashboard Title -->
    <div class="d-flex align-items-center mb-3">
        <div class="logo-box bg-light rounded-3 me-3">
            <i class="fas fa-tachometer-alt text-dark fs-5"></i>
        </div>
        <h2 class="mb-0">Dashboard Overview</h2>
    </div>
    <div class="date">
        <i class="fas fa-calendar"></i> <?= date('l, d F Y') ?>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid" data-aos="fade-up">
        <div class="stat-card primary">
            <div class="stat-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>Total Pemasukan</h3>
                <p class="amount">Rp <?= number_format($totalPemasukan, 0, ',', '.') ?></p>
                <span class="trend positive">
                    <i class="fas fa-chart-line"></i> +15% bulan ini
                </span>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-icon">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-info">
                <h3>Total Pengeluaran</h3>
                <p class="amount">Rp <?= number_format($totalPengeluaran, 0, ',', '.') ?></p>
                <span class="trend negative">
                    <i class="fas fa-chart-line"></i> +8% bulan ini
                </span>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <h3>Saldo Tersedia</h3>
                <p class="amount">Rp <?= number_format($saldoTersedia, 0, ',', '.') ?></p>
                <span class="trend positive">
                    <i class="fas fa-balance-scale"></i> Neraca Positif
                </span>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-icon">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-info">
                <h3>Stok Menipis</h3>
                <p class="amount"><?= $lowStock ?> Items</p>
                <span class="trend">
                    <i class="fas fa-exclamation-triangle"></i> Perlu Perhatian
                </span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Financial Chart -->
        <div class="chart-card" data-aos="fade-up" data-aos-delay="100">
            <div class="card-header">
                <h3>Grafik Keuangan</h3>
                <select id="chartPeriod" class="form-select">
                    <option value="weekly">Mingguan</option>
                    <option value="monthly" selected>Bulanan</option>
                    <option value="yearly">Tahunan</option>
                </select>
            </div>
            <div class="card-body">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <!-- Inventory Status -->
        <div class="chart-card" data-aos="fade-up" data-aos-delay="200">
            <div class="card-header">
                <h3>Status Inventory</h3>
                <button class="btn btn-sm btn-outline-primary" onclick="refreshInventory()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            <div class="card-body">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="activities-section" data-aos="fade-up" data-aos-delay="300">
        <h3>Aktivitas Terbaru</h3>
        <div class="timeline">
            <?php
            $recentTransactions = $financeController->getRecentTransactions();
            while ($trx = mysqli_fetch_assoc($recentTransactions)):
            ?>
            <div class="timeline-item">
                <div class="time"><?= date('H:i', strtotime($trx['tanggal'])) ?></div>
                <div class="content">
                    <h4><?= $trx['kategori'] ?></h4>
                    <p>Rp <?= number_format($trx['jumlah'], 0, ',', '.') ?></p>
                    <span class="badge <?= $trx['jenis'] == 'Pemasukan' ? 'bg-success' : 'bg-danger' ?>">
                        <?= $trx['jenis'] ?>
                    </span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<style>
/* Update dashboard container margin */
.dashboard-container {
    padding: 2rem;
    max-width: 1400px;
    margin: 2rem auto; /* Changed from 0 auto to add top margin */
}

.d-flex.align-items-center.mb-3 {
    margin-top: 1.5rem; /* Add margin to the title section */
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 1.5rem;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.stat-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: transform 0.3s ease;
}

.stat-card:hover .stat-icon {
    transform: scale(1.1) rotate(10deg);
}

/* Card variations */
.stat-card.primary .stat-icon { background: linear-gradient(135deg, #1976d2, #64b5f6); color: white; }
.stat-card.danger .stat-icon { background: linear-gradient(135deg, #d32f2f, #ef5350); color: white; }
.stat-card.success .stat-icon { background: linear-gradient(135deg, #388e3c, #66bb6a); color: white; }
.stat-card.warning .stat-icon { background: linear-gradient(135deg, #f57c00, #ffb74d); color: white; }

/* Charts Grid */
.charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr; /* Change this line - make second column smaller */
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.chart-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
}

/* Add max-width for inventory chart */
.chart-card:nth-child(2) {
    max-width: 400px; /* Add this line - limit width of inventory chart */
}

/* Add responsive adjustments */
@media (max-width: 992px) {
    .charts-grid {
        grid-template-columns: 1fr; /* Stack on smaller screens */
    }
    
    .chart-card:nth-child(2) {
        max-width: 100%; /* Full width on mobile */
    }
}

/* Timeline */
.timeline {
    margin-top: 1rem;
}

.timeline-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.timeline-item:hover {
    transform: translateX(5px);
}

.time {
    min-width: 60px;
    color: #666;
}

.content h4 {
    margin: 0;
    font-size: 1rem;
}

/* Animations */
@keyframes slideIn {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.stat-card {
    animation: slideIn 0.5s ease forwards;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-container {
        padding: 1rem;
    }

    .charts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: true
    });

    // Finance Chart
    const ctx = document.getElementById('financeChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Pemasukan',
                data: [3000000, 3500000, 2800000, 4200000, 3800000, 4500000],
                borderColor: '#4CAF50',
                tension: 0.4
            }, {
                label: 'Pengeluaran',
                data: [2500000, 2300000, 2600000, 2900000, 3100000, 2800000],
                borderColor: '#f44336',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Inventory Chart
    const ctxInventory = document.getElementById('inventoryChart').getContext('2d');
    new Chart(ctxInventory, {
        type: 'doughnut',
        data: {
            labels: ['Aman', 'Menipis', 'Kritis'],
            datasets: [{
                data: [65, 25, 10],
                backgroundColor: ['#4CAF50', '#FF9800', '#f44336']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

function refreshInventory() {
    // Add refresh logic here
    location.reload();
}
</script>

<?php include '../layout/footer.php'; ?>
</body>
</html>

