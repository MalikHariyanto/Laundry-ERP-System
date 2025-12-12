<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';

if (!isset($_GET['id'])) {
    die('ID transaksi tidak ditemukan');
}

$id = intval($_GET['id']);
$controller = new FinanceController($conn);
$transaksi = $controller->getTransactionById($id);

if (!$transaksi) {
    die('Transaksi tidak ditemukan');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detail Transaksi #<?= $transaksi['kode_transaksi'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .header { text-align: center; margin-bottom: 30px; }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .detail-table th, .detail-table td { padding: 10px; border: 1px solid #ddd; }
        .detail-table th { background: #f5f5f5; text-align: left; width: 200px; }
        .amount { font-size: 18px; font-weight: bold; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Detail Transaksi</h2>
        <p><?= $transaksi['kode_transaksi'] ?></p>
    </div>

    <table class="detail-table">
        <tr>
            <th>Tanggal</th>
            <td><?= date('d F Y', strtotime($transaksi['tanggal'])) ?></td>
        </tr>
        <tr>
            <th>Jenis</th>
            <td><?= $transaksi['jenis'] ?></td>
        </tr>
        <tr>
            <th>Kategori</th>
            <td><?= $transaksi['kategori'] ?></td>
        </tr>
        <tr>
            <th>Jumlah</th>
            <td class="amount">Rp <?= number_format($transaksi['jumlah'], 0, ',', '.') ?></td>
        </tr>
        <tr>
            <th>Metode Pembayaran</th>
            <td><?= $transaksi['metode_pembayaran'] ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= $transaksi['status'] ?></td>
        </tr>
        <tr>
            <th>Keterangan</th>
            <td><?= $transaksi['keterangan'] ?: '-' ?></td>
        </tr>
    </table>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">
            Cetak
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer;">
            Tutup
        </button>
    </div>
</body>
</html>