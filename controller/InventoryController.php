<?php
require_once __DIR__ . '/../model/InventoryModel.php';

class InventoryController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new InventoryModel($conn);
    }

    public function index() {
        return $this->model->getAllStock();
    }

    public function create($data) {
        // Generate kode barang
        $data['kode_barang'] = $this->generateKodeBarang($data['kategori']);
        return $this->model->createStock($data);
    }

    public function recordUsage($data) {
        $data['kode_penggunaan'] = 'USE-' . date('YmdHis');
        return $this->model->recordStockUsage($data);
    }

    public function getLowStock() {
        return $this->model->getLowStockItems();
    }

    public function getTotalItems() {
        return $this->model->countTotalItems();
    }

    public function getLowStockCount() {
        return $this->model->countLowStockItems();
    }

    public function getTotalValue() {
        return $this->model->calculateTotalValue();
    }

    public function delete($id) {
        return $this->model->deleteStock($id);
    }

    private function generateKodeBarang($kategori) {
        $prefix = substr($kategori, 0, 3);
        $date = date('Ymd');
        $random = rand(1000, 9999);
        return strtoupper($prefix . $date . $random);
    }
}
?>