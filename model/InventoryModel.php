<?php
class InventoryModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllStock() {
        $sql = "SELECT *, 
                CASE 
                    WHEN jumlah <= min_stok THEN 'Kritis'
                    WHEN jumlah <= (min_stok + ((max_stok - min_stok) * 0.2)) THEN 'Rendah'
                    ELSE 'Aman'
                END as status_stok
                FROM stok_barang 
                WHERE status = 'Aktif'
                ORDER BY status_stok ASC, kategori ASC";
        return mysqli_query($this->conn, $sql);
    }

    public function createStock($data) {
        $sql = "INSERT INTO stok_barang (
                    kode_barang, nama_barang, kategori, jumlah, satuan, 
                    harga_satuan, min_stok, max_stok, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Aktif')";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssisiis', 
            $data['kode_barang'],
            $data['nama_barang'],
            $data['kategori'],
            $data['jumlah'],
            $data['satuan'],
            $data['harga_satuan'],
            $data['min_stok'],
            $data['max_stok']
        );
        return mysqli_stmt_execute($stmt);
    }

    public function updateStock($id, $data) {
        $sql = "UPDATE stok_barang SET
                    kode_barang = ?,
                    nama_barang = ?,
                    kategori = ?,
                    jumlah = ?,
                    satuan = ?,
                    harga_satuan = ?,
                    min_stok = ?,
                    max_stok = ?,
                    status = ?
                WHERE id_barang = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sssisiissi', 
            $data['kode_barang'],
            $data['nama_barang'],
            $data['kategori'],
            $data['jumlah'],
            $data['satuan'],
            $data['harga_satuan'],
            $data['min_stok'],
            $data['max_stok'],
            $data['status'],
            $id
        );
        return mysqli_stmt_execute($stmt);
    }

    public function recordStockUsage($data) {
        // Start transaction
        mysqli_begin_transaction($this->conn);
        try {
            // Record usage
            $sql1 = "INSERT INTO penggunaan_stok (
                kode_penggunaan, tanggal_pakai, id_barang, jumlah_pakai, keterangan
            ) VALUES (?, NOW(), ?, ?, ?)";
            
            $stmt1 = mysqli_prepare($this->conn, $sql1);
            mysqli_stmt_bind_param($stmt1, 'siis', 
                $data['kode_penggunaan'], $data['id_barang'],
                $data['jumlah_pakai'], $data['keterangan']
            );
            mysqli_stmt_execute($stmt1);

            // Update stock
            $sql2 = "UPDATE stok_barang 
                    SET jumlah = jumlah - ?, tanggal_update = NOW()
                    WHERE id_barang = ?";
            
            $stmt2 = mysqli_prepare($this->conn, $sql2);
            mysqli_stmt_bind_param($stmt2, 'ii', 
                $data['jumlah_pakai'], $data['id_barang']
            );
            mysqli_stmt_execute($stmt2);

            mysqli_commit($this->conn);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            return false;
        }
    }

    public function countTotalItems() {
        $sql = "SELECT COUNT(*) as total FROM stok_barang WHERE status = 'Aktif'";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function countLowStockItems() {
        $sql = "SELECT COUNT(*) as total FROM stok_barang 
                WHERE jumlah <= min_stok AND status = 'Aktif'";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }

    public function calculateTotalValue() {
        $sql = "SELECT SUM(jumlah * harga_satuan) as total_value 
                FROM stok_barang 
                WHERE status = 'Aktif'";
        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total_value'] ?? 0;
    }

    public function deleteStock($id) {
        $sql = "UPDATE stok_barang SET status = 'Tidak Aktif' WHERE id_barang = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if (!$stmt) {
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, 'i', $id);
        return mysqli_stmt_execute($stmt);
    }
}