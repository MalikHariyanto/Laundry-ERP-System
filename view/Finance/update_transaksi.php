<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new FinanceController($conn);
    
    $data = [
        'id' => $_POST['id'],
        'tanggal' => $_POST['tanggal'],
        'jenis' => $_POST['jenis'],
        'kategori' => $_POST['kategori'],
        'jumlah' => $_POST['jumlah'],
        'keterangan' => $_POST['keterangan'],
        'status' => $_POST['status']  // Add this line
    ];
    
    try {
        if ($controller->updateTransaction($data)) {
            header('Location: list.php?success=update');
        } else {
            header('Location: detail_transaksi.php?id=' . $_POST['id'] . '&error=update');
        }
    } catch (Exception $e) {
        header('Location: detail_transaksi.php?id=' . $_POST['id'] . '&error=exception');
    }
    exit;
} else {
    header('Location: list.php');
    exit;
}
?>

<div class="form-group mb-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" required>
        <option value="Pending" <?= $transaksi['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Selesai" <?= $transaksi['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
        <option value="Batal" <?= $transaksi['status'] == 'Batal' ? 'selected' : '' ?>>Batal</option>
    </select>
</div>