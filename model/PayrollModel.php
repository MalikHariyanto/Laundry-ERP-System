<?php
class PayrollModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllKaryawan() {
        $sql = "SELECT * FROM karyawan ORDER BY nama ASC";
        return mysqli_query($this->conn, $sql);
    }

    public function calculateGaji($karyawan_id, $bulan, $tahun) {
        $sql = "SELECT k.*, 
                COALESCE(g.tunjangan, 0) as tunjangan,
                COALESCE(g.potongan, 0) as potongan,
                COALESCE(g.total_gaji, 0) as total_gaji,
                g.status_pembayaran
                FROM karyawan k
                LEFT JOIN gaji_karyawan g ON k.id = g.karyawan_id
                AND g.bulan = ? AND g.tahun = ?
                WHERE k.id = ?";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $bulan, $tahun, $karyawan_id);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    public function prosesGaji($data) {
        mysqli_begin_transaction($this->conn);
        
        try {
            // Format keterangan untuk trigger
            $keterangan = "Gaji " . $data['nama_karyawan'] . " periode " . 
                         date('F Y', mktime(0, 0, 0, $data['bulan'], 1, $data['tahun']));

            // Insert ke tabel keuangan dulu - trigger akan menangani insert ke gaji_karyawan
            $sql_keuangan = "INSERT INTO keuangan (
                kode_transaksi,
                tanggal,
                jenis,
                kategori,
                jumlah,
                metode_pembayaran,
                keterangan,
                status
            ) VALUES (?, NOW(), 'Pengeluaran', 'Gaji Karyawan', ?, ?, ?, 'Selesai')";
            
            $kode_transaksi = 'TRX-' . date('YmdHis');
            $stmt = mysqli_prepare($this->conn, $sql_keuangan);
            mysqli_stmt_bind_param($stmt, "sdss", 
                $kode_transaksi,
                $data['gaji'],
                $data['metode_pembayaran'],
                $keterangan
            );
            
            if (mysqli_stmt_execute($stmt)) {
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

    public function getTotalGajiBulanIni() {
        $bulan = date('n');
        $tahun = date('Y');
        
        $sql = "SELECT SUM(gaji) as total 
                FROM gaji_karyawan 
                WHERE bulan = ? AND tahun = ?";
                
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            return 0; // Return 0 if query preparation fails
        }
        
        mysqli_stmt_bind_param($stmt, "ii", $bulan, $tahun);
        if (!mysqli_stmt_execute($stmt)) {
            return 0; // Return 0 if execution fails
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    public function getAverageGaji() {
        $bulan = date('n');
        $tahun = date('Y');
        
        $sql = "SELECT AVG(gaji) as rata_rata 
                FROM gaji_karyawan 
                WHERE bulan = ? AND tahun = ?";
                
        $stmt = mysqli_prepare($this->conn, $sql);
        if (!$stmt) {
            return 0; // Return 0 if query preparation fails
        }
        
        mysqli_stmt_bind_param($stmt, "ii", $bulan, $tahun);
        if (!mysqli_stmt_execute($stmt)) {
            return 0; // Return 0 if execution fails
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['rata_rata'] ?? 0;
    }

    public function getRiwayatGaji() {
        $sql = "SELECT gk.*, k.nama, k.posisi, keu.kode_transaksi
                FROM gaji_karyawan gk
                LEFT JOIN karyawan k ON gk.karyawan_id = k.id
                LEFT JOIN keuangan keu ON gk.keuangan_id = keu.id
                WHERE k.id IS NOT NULL
                ORDER BY gk.tahun DESC, gk.bulan DESC";
        return mysqli_query($this->conn, $sql);
    }

    public function isPayrollExists($karyawan_id, $bulan, $tahun) {
        $sql = "SELECT id FROM gaji_karyawan 
                WHERE karyawan_id = ? AND bulan = ? AND tahun = ?";
                
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $karyawan_id, $bulan, $tahun);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_num_rows($result) > 0;
    }

    public function getGajiById($id) {
        $sql = "SELECT gk.*, k.nama, k.posisi 
                FROM gaji_karyawan gk
                JOIN karyawan k ON gk.karyawan_id = k.id
                WHERE gk.id = ?";
                
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    public function updateGaji($id, $data) {
        $total_gaji = $data['gaji_pokok'] + $data['tunjangan'] - $data['potongan'];
        
        $sql = "UPDATE gaji_karyawan 
                SET tunjangan = ?, 
                    potongan = ?, 
                    total_gaji = ?,
                    updated_at = NOW()
                WHERE id = ?";
                
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "dddi", 
            $data['tunjangan'],
            $data['potongan'],
            
            $total_gaji,
            $id
        );
        
        return mysqli_stmt_execute($stmt);
    }

    public function deleteGaji($id) {
        mysqli_begin_transaction($this->conn);
        
        try {
            // Get gaji data first
            $sql_get = "SELECT gk.*, k.nama 
                        FROM gaji_karyawan gk 
                        JOIN karyawan k ON gk.karyawan_id = k.id 
                        WHERE gk.id = ?";
            $stmt_get = mysqli_prepare($this->conn, $sql_get);
            mysqli_stmt_bind_param($stmt_get, 'i', $id);
            mysqli_stmt_execute($stmt_get);
            $result = mysqli_stmt_get_result($stmt_get);
            $gaji = mysqli_fetch_assoc($result);

            if (!$gaji) {
                throw new Exception("Data gaji tidak ditemukan");
            }

            // Delete from keuangan will trigger deletion in gaji_karyawan
            $sql_delete = "DELETE FROM keuangan 
                           WHERE kategori = 'Gaji Karyawan' 
                           AND jumlah = ? 
                           AND MONTH(tanggal) = ? 
                           AND YEAR(tanggal) = ?";
            
            $stmt_delete = mysqli_prepare($this->conn, $sql_delete);
            mysqli_stmt_bind_param($stmt_delete, 'dii', 
                $gaji['gaji'],
                $gaji['bulan'],
                $gaji['tahun']
            );
            
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

    // Add this method to the class
    public function createKaryawan($data) {
        // First, check if table exists
        $checkTable = "SHOW TABLES LIKE 'karyawan'";
        $tableExists = mysqli_query($this->conn, $checkTable);
        
        if (mysqli_num_rows($tableExists) == 0) {
            // Create table if it doesn't exist
            $createTable = "CREATE TABLE karyawan (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nama VARCHAR(100) NOT NULL,
                gaji_pokok DECIMAL(10,2) NOT NULL,
                tanggal_masuk DATE NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            mysqli_query($this->conn, $createTable);
        }

        // Prepare insert statement
        $sql = "INSERT INTO karyawan (nama, gaji_pokok, tanggal_masuk) 
                VALUES (?, ?, ?)";
        
        $stmt = mysqli_prepare($this->conn, $sql);
        
        if ($stmt === false) {
            error_log("Prepare failed: " . mysqli_error($this->conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'sds', 
            $data['nama'],
            $data['gaji_pokok'],
            $data['tanggal_masuk']
        );
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }

    // Add new method to clean orphaned records
    public function cleanOrphanedRecords() {
        $sql = "DELETE FROM gaji_karyawan 
                WHERE karyawan_id NOT IN (SELECT id FROM karyawan)";
        return mysqli_query($this->conn, $sql);
    }
}