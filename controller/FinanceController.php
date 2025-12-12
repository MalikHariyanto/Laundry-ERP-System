<?php
require_once __DIR__ . '/../model/FinanceModel.php';

class FinanceController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new FinanceModel($conn);
    }

    public function summary() {
        return $this->model->getSummary();
    }

    public function detailPengeluaran() {
        return $this->model->getDetailPengeluaran();
    }

    public function detailPemasukan() {
        return $this->model->getDetailPemasukan();
    }

    public function getProfitLossSummary() {
        return $this->model->getProfitLossSummary();
    }

    public function getProfitLossDetail() {
        // Change this to use the model instead of direct mysqli_query
        return $this->model->getProfitLossDetail();
    }

    public function addTransaction($data) {
        return $this->model->createTransaction($data);
    }

    public function getTransactionsByDate($startDate, $endDate) {
        return $this->model->getTransactionsByDateRange($startDate, $endDate);
    }

    // Add these methods
    public function getTodayIncome() {
        return $this->model->getTodayIncome();
    }

    public function getMonthlyExpenses() {
        return $this->model->getMonthlyExpenses();
    }

    public function getRecentTransactions() {
        return $this->model->getRecentTransactions();
    }

    // Tambahkan method baru
    public function getAllTransactions() {
        return $this->model->getAllTransactions();
    }

    // Add this method to the FinanceController class
    public function getTransactionById($id) {
        return $this->model->getTransactionById($id);
    }

    public function getMonthlyData() {
        return $this->model->getMonthlyData();
    }

    public function getQuarterlyData($year = null) {
        return $this->model->getQuarterlyData($year);
    }

    public function getYearlyData() {
        return $this->model->getYearlyData();
    }

    public function getMonthlyChartData($year = null) {
        return $this->model->getMonthlyChartData($year);
    }

    public function getKategoriData() {
        return $this->model->getKategoriData();
    }

    public function updateTransaction($data) {
        return $this->model->updateTransaction($data);
    }

    public function deleteTransaction($id) {
        return $this->model->deleteTransaction($id);
    }
}
?>