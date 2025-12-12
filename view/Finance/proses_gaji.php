<?php
require_once '../../controller/PayrollController.php';
require_once '../../config/koneksi.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $controller = new PayrollController($conn);
    
    // Hapus duplikasi pemanggilan fungsi
    $result = $controller->tambahPembayaranGaji($_POST);
    
    if ($result) {
        header('Location: gaji.php?success=1&message=' . urlencode('Pembayaran gaji berhasil ditambahkan'));
        exit;
    }
} catch (Exception $e) {
    header('Location: form_gaji.php?error=1&message=' . urlencode($e->getMessage()));
    exit;
}