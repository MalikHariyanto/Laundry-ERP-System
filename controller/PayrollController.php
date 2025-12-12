<?php
require_once __DIR__ . '/../model/PayrollModel.php';

class PayrollController {
    private $conn;
    private $model;

    public function __construct($connection) {
        if (!$connection) {
            throw new Exception("Database connection is required");
        }
        $this->conn = $connection;
        $this->model = new PayrollModel($this->conn);
    }

    public function index() {
        return $this->model->getAllKaryawan();
    }

    public function hitungGaji($karyawan_id, $bulan, $tahun) {
        return $this->model->calculateGaji($karyawan_id, $bulan, $tahun);
    }

    public function prosesGaji($data) {
        return $this->model->prosesGaji($data);
    }

    public function getTotalGajiBulanIni() {
        return $this->model->getTotalGajiBulanIni();
    }

    public function getAverageGaji() {
        return $this->model->getAverageGaji();
    }

    public function getRiwayatGaji() {
        // Hapus DISTINCT karena tidak efektif, gunakan query yang lebih spesifik
        $sql = "SELECT gk.id, gk.karyawan_id, gk.bulan, gk.tahun, gk.gaji, 
                gk.tanggal_pembayaran, gk.status_pembayaran, gk.keuangan_id,
                k.nama, k.posisi, keu.kode_transaksi 
                FROM gaji_karyawan gk
                JOIN karyawan k ON gk.karyawan_id = k.id
                LEFT JOIN keuangan keu ON gk.keuangan_id = keu.id
                GROUP BY gk.id, gk.bulan, gk.tahun
                ORDER BY gk.tahun DESC, gk.bulan DESC";
                
        return mysqli_query($this->conn, $sql);
    }

    public function getAllKaryawan() {
        if (!$this->conn) {
            throw new Exception("Database connection lost");
        }

        $sql = "SELECT * FROM karyawan ORDER BY nama ASC";
        $result = mysqli_query($this->conn, $sql);
        
        if (!$result) {
            throw new Exception("Error: " . mysqli_error($this->conn));
        }
        
        return $result;
    }

    public function tambahPembayaranGaji($data) {
        try {
            $karyawan_id = mysqli_real_escape_string($this->conn, $data['karyawan_id']);
            $bulan = mysqli_real_escape_string($this->conn, $data['bulan']);
            $tahun = mysqli_real_escape_string($this->conn, $data['tahun']);
            $gaji = mysqli_real_escape_string($this->conn, $data['gaji']);
            $tanggal = date('Y-m-d H:i:s');

            mysqli_begin_transaction($this->conn);

            // Get karyawan name
            $sql_karyawan = mysqli_query($this->conn, "SELECT nama FROM karyawan WHERE id = '$karyawan_id'");
            $karyawan = mysqli_fetch_assoc($sql_karyawan);
            $nama_karyawan = $karyawan['nama'];

            // Insert into keuangan first
            $kode_transaksi = 'TRX-' . date('YmdHis');
            $keterangan = "Pembayaran gaji {$nama_karyawan} periode " . date("F Y", mktime(0, 0, 0, $bulan, 1, $tahun));
            
            $sql_keuangan = "INSERT INTO keuangan (
                kode_transaksi,
                tanggal,
                jenis,
                kategori,
                jumlah,
                keterangan,
                status
            ) VALUES (
                '$kode_transaksi',
                '$tanggal',
                'Pengeluaran',
                'Gaji Karyawan',
                '$gaji',
                '$keterangan',
                'Selesai'
            )";

            if (!mysqli_query($this->conn, $sql_keuangan)) {
                throw new Exception("Error recording transaction: " . mysqli_error($this->conn));
            }

            $keuangan_id = mysqli_insert_id($this->conn);

            // Insert into gaji_karyawan
            $sql_gaji = "INSERT INTO gaji_karyawan (
                karyawan_id,
                bulan,
                tahun,
                gaji,
                tanggal_pembayaran,
                status_pembayaran,
                keuangan_id
            ) VALUES (
                '$karyawan_id',
                '$bulan',
                '$tahun',
                '$gaji',
                '$tanggal',
                'Dibayar',
                '$keuangan_id'
            )";

            if (!mysqli_query($this->conn, $sql_gaji)) {
                throw new Exception("Error inserting salary data: " . mysqli_error($this->conn));
            }

            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            throw $e;
        }
    }

    // Add new method to get karyawan gaji
    public function getKaryawanGaji($id) {
        $id = mysqli_real_escape_string($this->conn, $id);
        $sql = "SELECT gaji FROM karyawan WHERE id = '$id'";
        $result = mysqli_query($this->conn, $sql);
        
        if ($result && $row = mysqli_fetch_assoc($result)) {
            return $row['gaji'];
        }
        return 0;
    }

    public function updateGaji($id, $data) {
        try {
            $gaji = mysqli_real_escape_string($this->conn, $data['gaji']);
            $tanggal = date('Y-m-d H:i:s');

            mysqli_begin_transaction($this->conn);

            // Get keuangan_id from gaji_karyawan
            $sql_get = "SELECT keuangan_id, karyawan_id, bulan, tahun FROM gaji_karyawan WHERE id = '$id'";
            $result = mysqli_query($this->conn, $sql_get);
            $row = mysqli_fetch_assoc($result);

            if (!$row) {
                throw new Exception("Salary record not found");
            }

            // Update gaji_karyawan
            $sql_gaji = "UPDATE gaji_karyawan SET 
                gaji = '$gaji',
                tanggal_pembayaran = '$tanggal'
                WHERE id = '$id'";

            if (!mysqli_query($this->conn, $sql_gaji)) {
                throw new Exception("Error updating salary: " . mysqli_error($this->conn));
            }

            // Update keuangan
            $sql_keuangan = "UPDATE keuangan SET 
                jumlah = '$gaji',
                tanggal = '$tanggal'
                WHERE id = '{$row['keuangan_id']}'";

            if (!mysqli_query($this->conn, $sql_keuangan)) {
                throw new Exception("Error updating transaction: " . mysqli_error($this->conn));
            }

            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            throw $e;
        }
    }

    public function deleteGaji($id) {
        try {
            mysqli_begin_transaction($this->conn);

            // Get specific gaji record with keuangan_id
            $sql_get = "SELECT k.nama, gk.keuangan_id 
                        FROM gaji_karyawan gk
                        JOIN karyawan k ON gk.karyawan_id = k.id 
                        WHERE gk.id = ? LIMIT 1";
                        
            $stmt_get = mysqli_prepare($this->conn, $sql_get);
            mysqli_stmt_bind_param($stmt_get, 'i', $id);
            mysqli_stmt_execute($stmt_get);
            $result = mysqli_stmt_get_result($stmt_get);
            $row = mysqli_fetch_assoc($result);

            if (!$row) {
                throw new Exception("Data gaji tidak ditemukan");
            }

            // Delete specific gaji record by ID
            $sql_gaji = "DELETE FROM gaji_karyawan WHERE id = ?";
            $stmt_gaji = mysqli_prepare($this->conn, $sql_gaji);
            mysqli_stmt_bind_param($stmt_gaji, 'i', $id);
            
            if (!mysqli_stmt_execute($stmt_gaji)) {
                throw new Exception("Error deleting from gaji_karyawan");
            }

            // Delete corresponding keuangan record
            if ($row['keuangan_id']) {
                $sql_keuangan = "DELETE FROM keuangan WHERE id = ?";
                $stmt_keuangan = mysqli_prepare($this->conn, $sql_keuangan);
                mysqli_stmt_bind_param($stmt_keuangan, 'i', $row['keuangan_id']);
                
                if (!mysqli_stmt_execute($stmt_keuangan)) {
                    throw new Exception("Error deleting from keuangan");
                }
            }

            mysqli_commit($this->conn);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->conn);
            throw $e;
        }
    }
}