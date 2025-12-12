<?php
require_once '../../controller/InventoryController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new InventoryController($conn);
$data = $controller->index();
?>

<div class="container">
    <div class="page-header mb-4">
        <h2><i class="fas fa-boxes"></i> Manajemen Stok Barang</h2>
        <div class="header-actions">
            <a href="form.php" class="btn btn-custom-primary btn-with-icon">
                <div class="btn-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <span>Tambah Barang</span>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="finance-summary">
        <div class="summary-card inventory-total" data-aos="fade-up" data-aos-delay="100">
            <div class="icon-wrapper">
                <i class="fas fa-box"></i>
            </div>
            <div class="info">
                <span class="label">Total Item</span>
                <h3 class="amount"><?= $controller->getTotalItems() ?> Items</h3>
                <div class="trend positive">
                    <i class="fas fa-check-circle"></i>
                    <span>Dalam Inventaris</span>
                </div>
            </div>
        </div>

        <div class="summary-card inventory-warning" data-aos="fade-up" data-aos-delay="200">
            <div class="icon-wrapper">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="info">
                <span class="label">Stok Menipis</span>
                <h3 class="amount"><?= $controller->getLowStockCount() ?> Items</h3>
                <div class="trend warning">
                    <i class="fas fa-arrow-down"></i>
                    <span>Perlu Perhatian</span>
                </div>
            </div>
        </div>

        <div class="summary-card inventory-value" data-aos="fade-up" data-aos-delay="300">
            <div class="icon-wrapper">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="info">
                <span class="label">Total Nilai Stok</span>
                <h3 class="amount">Rp <?= number_format($controller->getTotalValue(), 0, ',', '.') ?></h3>
                <div class="trend">
                    <i class="fas fa-chart-line"></i>
                    <span>Nilai Inventaris</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="card filter-card mb-4" data-aos="fade-up" data-aos-delay="400">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="search-box">
                        <label class="form-label text-muted">
                            <i class="fas fa-search"></i> Search Items
                        </label>
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control custom-input" 
                                   placeholder="Enter item name...">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label text-muted">&nbsp;</label>
                    <button onclick="filterData()" class="btn btn-primary w-100 custom-button">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Table -->
    <div class="card" data-aos="fade-up" data-aos-delay="500">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $data = $controller->index();
                        while($row = mysqli_fetch_assoc($data)): 
                        ?>
                        <tr>
                            <td><?= $row['kode_barang'] ?></td>
                            <td><?= $row['nama_barang'] ?></td>
                            <td><?= $row['kategori'] ?></td>
                            <td><?= $row['jumlah'] ?></td>
                            <td>Rp <?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge bg-<?= $row['jumlah'] <= $row['min_stok'] ? 'danger' : 'success' ?>">
                                    <?= $row['jumlah'] <= $row['min_stok'] ? 'Kritis' : 'Aman' ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="form.php?id=<?= $row['id_barang'] ?>" 
                                       class="btn btn-sm btn-custom-warning" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-custom-danger" 
                                            onclick="deleteBarang(<?= $row['id_barang'] ?>)"
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

<style>
/* Summary Cards */
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
}

.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.summary-card .icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.inventory-total .icon-wrapper {
    background: #e3f2fd;
    color: #1565c0;
}

.inventory-warning .icon-wrapper {
    background: #fff8e1;
    color: #f57c00;
}

.inventory-value .icon-wrapper {
    background: #e8f5e9;
    color: #2e7d32;
}

/* Search Box Styling */
.search-box {
    position: relative;
}

.search-box .form-label {
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.custom-input {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 0.8rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.custom-input:focus {
    border-color: #2196F3;
    box-shadow: 0 0 0 3px rgba(33,150,243,0.1);
    background: white;
}

/* Button Styling */
.header-actions {
    display: flex;
    gap: 1rem;
}

.btn-with-icon {
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
}

.btn-custom-primary {
    background: linear-gradient(45deg, #1976d2, #2196F3);
    color: white;
}

.btn-custom-primary:hover {
    background: linear-gradient(45deg, #1565c0, #1976d2);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(33,150,243,0.3);
    color: white;
    text-decoration: none;
}

.btn-custom-secondary {
    background: linear-gradient(45deg, #5c6bc0, #7986cb);
    color: white;
}

.btn-custom-secondary:hover {
    background: linear-gradient(45deg, #3949ab, #5c6bc0);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(92,107,192,0.3);
    color: white;
    text-decoration: none;
}

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 8px;
    background: rgba(255,255,255,0.2);
}

.btn-icon i {
    font-size: 0.9rem;
}

/* Animation for buttons */
.btn-with-icon {
    position: relative;
    overflow: hidden;
}

.btn-with-icon::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.1);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
}

.btn-with-icon:hover::after {
    transform: translateX(0);
}

/* Filter Card Styling */
.filter-card {
    background: white;
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.filter-card .card-body {
    padding: 1.5rem;
}

/* Row Spacing */
.row.g-3 {
    row-gap: 1rem !important;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .filter-card .card-body {
        padding: 1rem;
    }
    
    .row.g-3 > div {
        margin-bottom: 1rem;
    }

    .header-actions {
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-with-icon {
        width: 100%;
        justify-content: center;
    }
}

/* Table Animation */
.animate-row {
    animation: slideIn 0.3s ease forwards;
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

/* Spacing fix */
.g-3 {
    gap: 1rem;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-custom-warning,
.btn-custom-danger {
    width: 32px;
    height: 32px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.btn-custom-warning {
    background: linear-gradient(135deg, #ff9800, #ffb74d);
    color: white;
}

.btn-custom-danger {
    background: linear-gradient(135deg, #f44336, #e57373);
    color: white;
}

.btn-custom-warning:hover,
.btn-custom-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    color: white;
}

.btn-custom-warning:hover {
    background: linear-gradient(135deg, #f57c00, #ff9800);
}

.btn-custom-danger:hover {
    background: linear-gradient(135deg, #d32f2f, #f44336);
}
</style>

<script>
// Initialize AOS
AOS.init({
    duration: 800,
    once: true
});

// Search and Filter functionality
function filterData() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const namaBarang = row.cells[1].textContent.toLowerCase();
        if (namaBarang.includes(search)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Add event listeners
document.getElementById('searchInput').addEventListener('keyup', filterData);

function recordUsage(id) {
    // Implement usage recording functionality
    window.location.href = `penggunaan.php?id=${id}`;
}

function deleteBarang(id) {
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
        fetch('delete_barang.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Barang berhasil dihapus');
                location.reload();
            } else {
                alert('Gagal menghapus barang: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus barang');
        });
    }
}

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});
</script>

<?php include '../layout/footer.php'; ?>