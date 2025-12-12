<?php
require_once '../../controller/InventoryController.php';
require_once '../../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new InventoryController($conn);
    
    $data = [
        'nama_barang' => $_POST['nama_barang'],
        'kategori' => $_POST['kategori'],
        'jumlah' => $_POST['jumlah'],
        'satuan' => $_POST['satuan'],
        'harga_satuan' => $_POST['harga_satuan'],
        'min_stok' => $_POST['min_stok'],
        'max_stok' => $_POST['max_stok']
    ];
    
    try {
        if ($controller->create($data)) {
            header('Location: list.php?success=add');
        } else {
            header('Location: form.php?error=1');
        }
    } catch (Exception $e) {
        header('Location: form.php?error=2');
    }
    exit;
} else {
    header('Location: form.php');
    exit;
}