<?php
// Di awal laba_rugi.php, tambahkan:
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new FinanceController($conn);
$summary = mysqli_fetch_assoc($controller->getProfitLossSummary());
$detail = $controller->getProfitLossDetail();

// Get monthly data for chart
$monthlyData = $controller->getMonthlyData();
$labels = [];
$pendapatan = [];
$pengeluaran = [];
$labaRugi = [];

while($row = mysqli_fetch_assoc($monthlyData)) {
    $labels[] = date('M', mktime(0, 0, 0, $row['bulan'], 1));
    $pendapatan[] = (float)$row['pendapatan'];
    $pengeluaran[] = (float)$row['pengeluaran'];
    $labaRugi[] = (float)$row['laba_rugi'];
}
?>

<div class="container">
    <div class="page-header mb-4">
        <h2><i class="fas fa-chart-line"></i> Laporan Laba Rugi</h2>
        <div class="header-actions">
            <div class="btn-group">
                <button onclick="printReport()" class="btn btn-custom-primary btn-with-icon">
                    <div class="btn-icon">
                        <i class="fas fa-print"></i>
                    </div>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="finance-summary">
        <div class="summary-card" data-aos="fade-up" data-aos-delay="100">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-circle-up"></i>
            </div>
            <div class="info">
                <span class="label">Total Pendapatan</span>
                <h3 class="amount">Rp <?= number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') ?></h3>
                <div class="trend positive">
                    <i class="fas fa-chart-line"></i>
                    <span>Pemasukan</span>
                </div>
            </div>
        </div>

        <div class="summary-card" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-wrapper expense-icon">
                <i class="fas fa-arrow-circle-down"></i>
            </div>
            <div class="info">
                <span class="label">Total Pengeluaran</span>
                <h3 class="amount">Rp <?= number_format($summary['total_pengeluaran'] ?? 0, 0, ',', '.') ?></h3>
                <div class="trend negative">
                    <i class="fas fa-chart-line"></i>
                    <span>Pengeluaran</span>
                </div>
            </div>
        </div>

        <div class="summary-card" data-aos="fade-up" data-aos-delay="300">
            <div class="icon-wrapper profit-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="info">
                <span class="label">Laba/Rugi Bersih</span>
                <h3 class="amount">Rp <?= number_format(abs($summary['laba_rugi'] ?? 0), 0, ',', '.') ?></h3>
                <div class="trend <?= ($summary['laba_rugi'] ?? 0) >= 0 ? 'positive' : 'negative' ?>">
                    <i class="fas fa-<?= ($summary['laba_rugi'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                    <span><?= ($summary['laba_rugi'] ?? 0) >= 0 ? 'LABA' : 'RUGI' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card" data-aos="fade-up" data-aos-delay="400">
                <div class="card-header">
                    <h3><i class="fas fa-chart-bar"></i> Grafik Laba Rugi</h3>
                    <div class="header-actions">
                        <select id="chartPeriod" class="form-select custom-select">
                            <option value="monthly">Bulanan</option>
                            <option value="quarterly">Kuartalan</option>
                            <option value="yearly">Tahunan</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="profitLossChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Section -->
    <div class="row">
        <!-- Income Details -->
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="500">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h3><i class="fas fa-arrow-up"></i> Rincian Pendapatan</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($detail['pendapatan'])): ?>
                                    <?php foreach($detail['pendapatan'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['kategori']) ?></td>
                                        <td>Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada data pendapatan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Details -->
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="600">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h3><i class="fas fa-arrow-down"></i> Rincian Pengeluaran</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($detail['pengeluaran'])): ?>
                                    <?php foreach($detail['pengeluaran'] as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['kategori']) ?></td>
                                        <td>Rp <?= number_format($item['jumlah'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" class="text-center">Tidak ada data pengeluaran</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Finance Summary Cards */
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

/* Chart Section */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.1);
}

.card-header {
    background: linear-gradient(45deg, #1976d2, #64b5f6);
    color: white;
    border: none;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.custom-select {
    background-color: rgba(255,255,255,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 0.4rem 2rem 0.4rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.custom-select:focus {
    outline: none;
    box-shadow: 0 0 0 2px rgba(255,255,255,0.3);
}

.custom-select option {
    background: white;
    color: #333;
}

/* Animation classes */
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
// Initialize AOS
document.addEventListener('DOMContentLoaded', function() {
    AOS.init({
        duration: 800,
        once: true,
        offset: 50
    });

    // Add hover animations for cards
    document.querySelectorAll('.summary-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.querySelector('.icon-wrapper i').style.transform = 'scale(1.2) rotate(15deg)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.querySelector('.icon-wrapper i').style.transform = 'scale(1) rotate(0)';
        });
    });
});

// Initialize chart with real data from database
const ctx = document.getElementById('profitLossChart').getContext('2d');
const chartData = {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
        label: 'Pendapatan',
        data: <?= json_encode($pendapatan) ?>,
        borderColor: '#2e7d32',
        backgroundColor: 'rgba(46, 125, 50, 0.1)',
        fill: true,
        tension: 0.4
    }, {
        label: 'Pengeluaran',
        data: <?= json_encode($pengeluaran) ?>,
        borderColor: '#c62828',
        backgroundColor: 'rgba(198, 40, 40, 0.1)',
        fill: true,
        tension: 0.4
    }, {
        label: 'Laba/Rugi',
        data: <?= json_encode($labaRugi) ?>,
        borderColor: '#1565c0',
        backgroundColor: 'rgba(21, 101, 192, 0.1)',
        fill: true,
        tension: 0.4
    }]
};

let profitLossChart = new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    usePointStyle: true,
                    padding: 20
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            label += new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR'
                            }).format(context.parsed.y);
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            maximumSignificantDigits: 3
                        }).format(value);
                    }
                }
            }
        }
    }
});

// Handle period change
document.getElementById('chartPeriod').addEventListener('change', async function() {
    const period = this.value;
    
    try {
        const response = await fetch(`get_profit_loss_data.php?period=${period}`);
        const newData = await response.json();
        
        profitLossChart.data.labels = newData.labels;
        profitLossChart.data.datasets[0].data = newData.pendapatan;
        profitLossChart.data.datasets[1].data = newData.pengeluaran;
        profitLossChart.data.datasets[2].data = newData.laba_rugi;
        
        profitLossChart.update();
    } catch (error) {
        console.error('Error fetching data:', error);
    }
});

// Add smooth animations
Chart.defaults.animation = {
    duration: 2000,
    easing: 'easeInOutQuart'
};

function printReport() {
    window.print();
}
</script>

<?php include '../layout/footer.php'; ?>