<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new FinanceController($conn);
    
    $data = [
        'kode_transaksi' => 'TRX-' . date('YmdHis'),
        'tanggal' => $_POST['tanggal'],
        'jenis' => $_POST['jenis'],
        'kategori' => $_POST['kategori'],
        'jumlah' => $_POST['jumlah'],
        'metode_pembayaran' => $_POST['metode_pembayaran'],
        'keterangan' => $_POST['keterangan'],
        'status' => 'Selesai'
    ];
    
    if ($controller->addTransaction($data)) {
        header('Location: list.php?success=1');
    } else {
        header('Location: form_transaksi.php?error=1');
    }
    exit;
}