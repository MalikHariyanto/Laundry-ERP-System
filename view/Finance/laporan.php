<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new FinanceController($conn);
$summary = $controller->summary();
$data = mysqli_fetch_assoc($summary);

$monthlyData = $controller->getMonthlyChartData();
$kategoriData = $controller->getKategoriData();

// Prepare data for charts
$labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
$pemasukan = array_column($monthlyData, 'pemasukan');
$pengeluaran = array_column($monthlyData, 'pengeluaran');
$saldo = array_column($monthlyData, 'saldo');

// Prepare kategori data
$kategoriPemasukan = [];
$kategoriPengeluaran = [];

while($row = mysqli_fetch_assoc($kategoriData)) {
    if($row['jenis'] == 'Pemasukan') {
        $kategoriPemasukan[] = [
            'kategori' => $row['kategori'],
            'total' => $row['total']
        ];
    } else {
        $kategoriPengeluaran[] = [
            'kategori' => $row['kategori'],
            'total' => $row['total']
        ];
    }
}
?>

<div class="container">
    <div class="page-header mb-4">
        <h2><i class="fas fa-chart-line"></i> Laporan Keuangan</h2>
        <div class="header-actions">
            <button onclick="printReport()" class="btn btn-secondary">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
    </div>

    <!-- Summary Cards with Animation -->
    <div class="finance-summary">
        <div class="summary-card income-card" data-aos="fade-up" data-aos-delay="100">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-up"></i>
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

        <div class="summary-card expense-card" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-wrapper">
                <i class="fas fa-arrow-down"></i>
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

        <div class="summary-card balance-card" data-aos="fade-up" data-aos-delay="300">
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

    <!-- Chart Section -->
    <div class="row mt-5" data-aos="fade-up" data-aos-delay="400">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Grafik Keuangan</h3>
                    <div class="header-actions">
                        <select id="chartType" class="form-select custom-select">
                            <option value="line">Line Chart</option>
                            <option value="bar">Bar Chart</option>
                            <option value="area">Area Chart</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="financeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="card mt-4" data-aos="fade-up" data-aos-delay="500">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Rincian Transaksi</h3>
            <div class="filter-section">
                <select id="filterJenis" class="form-select custom-select">
                    <option value="">Semua Jenis</option>
                    <option value="Pemasukan">Pemasukan</option>
                    <option value="Pengeluaran">Pengeluaran</option>
                </select>
                <input type="date" id="startDate" class="form-control">
                <input type="date" id="endDate" class="form-control">
                <button onclick="filterData()" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
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
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <span class="code-badge">
                                    <?= $row['kode_transaksi'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $row['jenis'] === 'Pemasukan' ? 'success' : 'danger' ?>">
                                    <?= $row['jenis'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="category-badge">
                                    <?= $row['kategori'] ?>
                                </span>
                            </td>
                            <td>Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= ($row['status'] ?? 'Pending') === 'Selesai' ? 'success' : 'warning' ?>">
                                    <?= $row['status'] ?? 'Pending' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="detail_transaksi.php?id=<?= $row['id'] ?>" 
                                       class="btn btn-sm btn-info" 
                                       data-bs-toggle="tooltip"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if(!empty($row['bukti_transaksi'])): ?>
                                    <button class="btn btn-sm btn-secondary view-bukti" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#buktiModal"
                                            data-bukti="/PSDP/uploads/bukti_transaksi/<?= $row['bukti_transaksi'] ?>"
                                            title="Lihat Bukti">
                                        <i class="fas fa-file-alt"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Update the kategori filter -->
    <div class="form-group">
        <label>Kategori</label>
        <select name="kategori" class="form-select custom-select">
            <option value="">Semua Kategori</option>
            <optgroup label="Pemasukan">
                <option value="Jasa Laundry">Jasa Laundry</option>
                <option value="Lainnya">Lainnya</option>
            </optgroup>
            <optgroup label="Pengeluaran">
                <option value="Bahan Baku">Bahan Baku</option>
                <option value="Operasional">Operasional</option>
                <option value="Utilitas">Utilitas</option>
            </optgroup>
        </select>
    </div>
</div>

<!-- Add required CSS -->
<style>
.finance-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    transition: transform 0.3s ease;
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.summary-card:hover {
    transform: translateY(-5px);
}

.icon-wrapper {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.income-card .icon-wrapper {
    background: #e8f5e9;
    color: #2e7d32;
}

.expense-card .icon-wrapper {
    background: #ffebee;
    color: #c62828;
}

.balance-card .icon-wrapper {
    background: #e3f2fd;
    color: #1565c0;
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
    color: #333;
}

.trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.875rem;
}

.trend.positive {
    color: #2e7d32;
}

.trend.negative {
    color: #c62828;
}

.filter-section {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.custom-select {
    min-width: 150px;
}

/* Animation classes */
[data-aos] {
    opacity: 0;
    transition: all 0.6s ease;
}

[data-aos].aos-animate {
    opacity: 1;
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

.table tr {
    animation: slideIn 0.3s ease forwards;
}
</style>

<!-- Add AOS library for animations -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize AOS
AOS.init();

// Initialize Finance Chart with real data
const ctx = document.getElementById('financeChart').getContext('2d');
const chartData = {
    labels: <?= json_encode($labels) ?>,
    datasets: [{
        label: 'Pemasukan',
        data: <?= json_encode($pemasukan) ?>,
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
        label: 'Saldo',
        data: <?= json_encode($saldo) ?>,
        borderColor: '#1565c0',
        backgroundColor: 'rgba(21, 101, 192, 0.1)',
        fill: true,
        tension: 0.4
    }]
};

let financeChart = new Chart(ctx, {
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
                    padding: 20,
                    font: {
                        size: 12
                    }
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

// Handle chart type change
document.getElementById('chartType').addEventListener('change', function() {
    financeChart.destroy();
    financeChart = new Chart(ctx, {
        type: this.value,
        data: chartData,
        options: financeChart.options
    });
});

// Functions for data handling
function loadTransactions(filters = {}) {
    const tbody = document.getElementById('transactionData');
    fetch('get_transactions.php?' + new URLSearchParams(filters))
        .then(response => response.json())
        .then(data => {
            tbody.innerHTML = data.map((transaction, index) => `
                <tr style="animation-delay: ${index * 0.1}s">
                    <td>${new Date(transaction.tanggal).toLocaleDateString('id-ID')}</td>
                    <td>
                        <span class="code-badge">${transaction.kode_transaksi}</span>
                    </td>
                    <td>
                        <span class="badge bg-${transaction.jenis === 'Pemasukan' ? 'success' : 'danger'}">
                            ${transaction.jenis}
                        </span>
                    </td>
                    <td>
                        <span class="category-badge">${transaction.kategori}</span>
                    </td>
                    <td>Rp ${Number(transaction.jumlah).toLocaleString('id-ID')}</td>
                    <td>
                        <span class="badge bg-${transaction.status === 'Selesai' ? 'success' : 'warning'}">
                            ${transaction.status}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="detail_transaksi.php?id=${transaction.id}" 
                               class="btn btn-sm btn-info" 
                               data-bs-toggle="tooltip" 
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            ${transaction.bukti_transaksi ? `
                                <button class="btn btn-sm btn-secondary view-bukti" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#buktiModal"
                                        data-bukti="/PSDP/uploads/bukti_transaksi/${transaction.bukti_transaksi}">
                                    <i class="fas fa-file-alt"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `).join('');
            
            // Reinitialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
}

function filterData() {
    const filters = {
        jenis: document.getElementById('filterJenis').value,
        start: document.getElementById('startDate').value,
        end: document.getElementById('endDate').value
    };
    loadTransactions(filters);
}

// Handle bukti preview
document.addEventListener('click', function(e) {
    if(e.target.closest('.view-bukti')) {
        const button = e.target.closest('.view-bukti');
        const buktiUrl = button.dataset.bukti;
        document.getElementById('buktiPreview').src = buktiUrl;
    }
});

// Initial load
loadTransactions();
</script>

<!-- Modal untuk preview bukti transaksi -->
<div class="modal fade" id="buktiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bukti Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <img id="buktiPreview" src="" class="img-fluid" alt="Bukti Transaksi">
            </div>
        </div>
    </div>
</div>

<?php include '../layout/footer.php'; ?>