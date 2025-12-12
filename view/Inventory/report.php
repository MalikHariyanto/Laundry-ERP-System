<?php
require_once '../../controller/InventoryController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';

$controller = new InventoryController($conn);
$lowStock = $controller->getLowStock();
?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-chart-bar"></i> Laporan Inventory</h2>
    </div>

    <!-- Warning Cards -->
    <div class="row">
        <div class="col-md-6">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h4><i class="fas fa-exclamation-triangle"></i> Stok Menipis</h4>
                    <p>Beberapa barang memerlukan pemesanan segera!</p>
                </div>
                <div class="card-footer">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Stok</th>
                                <th>Min. Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($lowStock)): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td><?= $row['jumlah'] ?></td>
                                <td><?= $row['min_stok'] ?></td>
                                <td>
                                    <span class="badge bg-danger">Kritis</span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Value Report -->
    <div class="card mt-4">
        <div class="card-header">
            <h3>Nilai Inventory</h3>
        </div>
        <div class="card-body">
            <canvas id="stockValueChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize stock value chart
const ctx = document.getElementById('stockValueChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Deterjen', 'Pewangi', 'Pemutih', 'Peralatan', 'Supplies'],
        datasets: [{
            label: 'Nilai Inventory (Rp)',
            data: [/* Data will be loaded via AJAX */],
            backgroundColor: [
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 99, 132, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(255, 99, 132, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include '../layout/footer.php'; ?>