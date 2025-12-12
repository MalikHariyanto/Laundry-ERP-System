<?php
require_once '../../controller/InventoryController.php';
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new InventoryController($conn);
    
    $data = [
        'id_barang' => $_POST['id_barang'],
        'jumlah_pakai' => $_POST['jumlah_pakai'],
        'keterangan' => $_POST['keterangan']
    ];
    
    if ($controller->recordUsage($data)) {
        // Record to keuangan table
        $sql = "INSERT INTO keuangan (
                    kode_transaksi, 
                    tanggal, 
                    jenis, 
                    kategori,
                    jumlah,
                    keterangan,
                    status
                ) VALUES (
                    ?, 
                    NOW(), 
                    'Pengeluaran',
                    'Penggunaan Stok',
                    ?,
                    ?,
                    'Selesai'
                )";
        
        $stmt = mysqli_prepare($conn, $sql);
        $kode = 'TRX-' . date('YmdHis');
        $jumlah = $_POST['jumlah_pakai'] * $_POST['harga_satuan'];
        mysqli_stmt_bind_param($stmt, 'sds', $kode, $jumlah, $_POST['keterangan']);
        mysqli_stmt_execute($stmt);
        
        header('Location: penggunaan.php?success=1');
    } else {
        header('Location: penggunaan.php?error=1');
    }
    exit;
}