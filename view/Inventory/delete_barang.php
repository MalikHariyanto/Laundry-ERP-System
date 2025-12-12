<?php
<?php
require_once '../../controller/InventoryController.php';
require_once '../../config/koneksi.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $controller = new InventoryController($conn);
    $id = intval($_POST['id']);
    
    try {
        if ($controller->delete($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Gagal menghapus barang'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}