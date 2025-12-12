<?php
class FinanceModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getSummary() {
        $sql = "SELECT 
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pemasukan,
                SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran
                FROM keuangan 
                WHERE status = 'Selesai'";
        return mysqli_query($this->conn, $sql);
    }

    public function getDetailPengeluaran() {
        $sql = "SELECT k.*, ps.kode_penggunaan, ps.jumlah_pakai, 
                sb.nama_barang, sb.harga_satuan
                FROM keuangan k
                LEFT JOIN penggunaan_stok ps ON k.kode_transaksi = ps.kode_penggunaan
                LEFT JOIN stok_barang sb ON ps.id_barang = sb.id_barang
                WHERE k.jenis = 'Pengeluaran'
                ORDER BY k.tanggal DESC";
        return mysqli_query($this->conn, $sql);
    }

    public function getDetailPemasukan() {
        $sql = "SELECT * FROM keuangan 
                WHERE jenis = 'Pemasukan' 
                ORDER BY tanggal DESC";
        return mysqli_query($this->conn, $sql);
    }

    public function getTotalExpenses() {
        $sql = "SELECT SUM(jumlah) as total FROM keuangan 
                WHERE jenis = 'Pengeluaran' AND status = 'Selesai'";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function getProfitLossSummary() {
        $sql = "SELECT 
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as total_pendapatan,
                SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as total_pengeluaran,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE -jumlah END) as laba_rugi
                FROM keuangan 
                WHERE status = 'Selesai'";
        
        return mysqli_query($this->conn, $sql);
    }

    public function getProfitLossDetail() {
        $pendapatan = mysqli_query($this->conn, "
            SELECT kategori, SUM(jumlah) as jumlah 
            FROM keuangan 
            WHERE jenis = 'Pemasukan' 
            AND status = 'Selesai'
            GROUP BY kategori
        ");

        $pengeluaran = mysqli_query($this->conn, "
            SELECT kategori, SUM(jumlah) as jumlah 
            FROM keuangan 
            WHERE jenis = 'Pengeluaran'
            AND status = 'Selesai' 
            GROUP BY kategori
        ");

        return [
            'pendapatan' => mysqli_fetch_all($pendapatan, MYSQLI_ASSOC),
            'pengeluaran' => mysqli_fetch_all($pengeluaran, MYSQLI_ASSOC)
        ];
    }

    public function createTransaction($data) {
        $sql = "INSERT INTO keuangan (
                    kode_transaksi,
                    tanggal,
                    jenis,
                    kategori,
                    jumlah,
                    metode_pembayaran,
                    keterangan,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssssdsss',
            $data['kode_transaksi'],
            $data['tanggal'],
            $data['jenis'],
            $data['kategori'],
            $data['jumlah'],
            $data['metode_pembayaran'],
            $data['keterangan'],
            $data['status']
        );
        
        return mysqli_stmt_execute($stmt);
    }

    public function getTodayIncome() {
        $sql = "SELECT SUM(jumlah) as total FROM keuangan 
                WHERE jenis = 'Pemasukan' 
                AND DATE(tanggal) = CURDATE()
                AND status = 'Selesai'";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function getMonthlyExpenses() {
        $sql = "SELECT SUM(jumlah) as total FROM keuangan 
                WHERE jenis = 'Pengeluaran' 
                AND MONTH(tanggal) = MONTH(CURRENT_DATE())
                AND YEAR(tanggal) = YEAR(CURRENT_DATE())
                AND status = 'Selesai'";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function getRecentTransactions($limit = 5) {
        $sql = "SELECT * FROM keuangan 
                ORDER BY tanggal DESC LIMIT $limit";
        return mysqli_query($this->conn, $sql);
    }

    // Tambahkan method baru
    public function getAllTransactions() {
        $sql = "SELECT id, kode_transaksi, tanggal, jenis, kategori, jumlah, 
                status FROM keuangan ORDER BY tanggal DESC";
        return mysqli_query($this->conn, $sql);
    }

    // Add this method to the FinanceModel class
    public function getTransactionById($id) {
        $sql = "SELECT * FROM keuangan WHERE id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    public function getMonthlyData() {
        $sql = "SELECT 
                MONTH(tanggal) as bulan,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as pendapatan,
                SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as pengeluaran,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE -jumlah END) as laba_rugi
                FROM keuangan
                WHERE YEAR(tanggal) = YEAR(CURRENT_DATE)
                AND status = 'Selesai'
                GROUP BY MONTH(tanggal)
                ORDER BY MONTH(tanggal)";
        
        return mysqli_query($this->conn, $sql);
    }

    public function getQuarterlyData($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                QUARTER(tanggal) as quarter,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as pendapatan,
                SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as pengeluaran,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE -jumlah END) as laba_rugi
                FROM keuangan
                WHERE YEAR(tanggal) = ? AND status = 'Selesai'
                GROUP BY QUARTER(tanggal)
                ORDER BY QUARTER(tanggal)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $year);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function getYearlyData() {
        $sql = "SELECT 
                YEAR(tanggal) as tahun,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as pendapatan,
                SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as pengeluaran,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE -jumlah END) as laba_rugi
                FROM keuangan
                WHERE status = 'Selesai'
                GROUP BY YEAR(tanggal)
                ORDER BY YEAR(tanggal)";
        
        return mysqli_query($this->conn, $sql);
    }

    public function getMonthlyChartData($year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $sql = "SELECT 
                MONTH(tanggal) as bulan,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE 0 END) as pemasukan,
                SUM(CASE WHEN jenis = 'Pengeluaran' THEN jumlah ELSE 0 END) as pengeluaran,
                SUM(CASE WHEN jenis = 'Pemasukan' THEN jumlah ELSE -jumlah END) as saldo
                FROM keuangan
                WHERE YEAR(tanggal) = ? AND status = 'Selesai'
                GROUP BY MONTH(tanggal)
                ORDER BY MONTH(tanggal)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $year);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = array_fill(0, 12, [
            'pemasukan' => 0,
            'pengeluaran' => 0,
            'saldo' => 0
        ]);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $index = $row['bulan'] - 1;
            $data[$index] = [
                'pemasukan' => (float)$row['pemasukan'],
                'pengeluaran' => (float)$row['pengeluaran'],
                'saldo' => (float)$row['saldo']
            ];
        }
        
        return $data;
    }

    public function getKategoriData() {
        $sql = "SELECT 
                kategori,
                jenis,
                SUM(jumlah) as total
                FROM keuangan
                WHERE status = 'Selesai'
                GROUP BY kategori, jenis
                ORDER BY jenis, total DESC";
        
        return mysqli_query($this->conn, $sql);
    }

    public function updateTransaction($data) {
        $sql = "UPDATE keuangan SET 
                tanggal = ?,
                jenis = ?,
                kategori = ?,
                jumlah = ?,
                keterangan = ?,
                status = ?
                WHERE id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, 'sssdssi', 
            $data['tanggal'],
            $data['jenis'],
            $data['kategori'],
            $data['jumlah'],
            $data['keterangan'],
            $data['status'],
            $data['id']
        );
        
        return mysqli_stmt_execute($stmt);
    }

    public function deleteTransaction($id) {
        mysqli_begin_transaction($this->conn);
        
        try {
            // Get transaction details
            $sql_get = "SELECT * FROM keuangan WHERE id = ?";
            $stmt_get = mysqli_prepare($this->conn, $sql_get);
            mysqli_stmt_bind_param($stmt_get, 'i', $id);
            mysqli_stmt_execute($stmt_get);
            $result = mysqli_stmt_get_result($stmt_get);
            $transaction = mysqli_fetch_assoc($result);

            // If it's a salary payment, the CASCADE will handle gaji_karyawan deletion
            $sql_delete = "DELETE FROM keuangan WHERE id = ?";
            $stmt_delete = mysqli_prepare($this->conn, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, 'i', $id);
            
            if (mysqli_stmt_execute($stmt_delete)) {
                mysqli_commit($this->conn);
                return true;
            }

            mysqli_rollback($this->conn);
            return false;
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return false;
        }
    }
}