DROP DATABASE IF EXISTS laundry;


CREATE DATABASE laundry;
USE laundry;

-- Tabel Pelanggan
CREATE TABLE pelanggan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100),
    kontak VARCHAR(100)
);

-- Tabel Karyawan
CREATE TABLE karyawan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100),
    posisi VARCHAR(50),
    gaji DECIMAL(10,2),
    tanggal_masuk DATE
);

-- Tabel Transaksi Laundry
CREATE TABLE transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pelanggan_id INT,
    karyawan_id INT,
    tanggal DATE,
    STATUS ENUM('masuk', 'diproses', 'selesai', 'diambil'),
    FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id),
    FOREIGN KEY (karyawan_id) REFERENCES karyawan(id)
);

-- Tabel Item Barang Laundry
CREATE TABLE item_laundry (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaksi_id INT,
    jenis_barang ENUM('baju', 'boneka', 'sprei', 'bedcover'),
    berat_kg DECIMAL(5,2),
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id)
);

-- Tabel Layanan
CREATE TABLE layanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_layanan ENUM('cuci basah', 'cuci kering', 'setrika', 'cuci setrika'),
    harga_per_kg DECIMAL(10,2)
);

-- Tabel Relasi Item Laundry dan Layanan
CREATE TABLE item_layanan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    item_laundry_id INT,
    layanan_id INT,
    FOREIGN KEY (item_laundry_id) REFERENCES item_laundry(id),
    FOREIGN KEY (layanan_id) REFERENCES layanan(id)
);

-- Tabel Pembayaran
CREATE TABLE pembayaran (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaksi_id INT,
    jumlah DECIMAL(10,2),
    metode ENUM('cash', 'transfer'),
    tanggal DATE,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id)
);

-- Tabel Laporan Keuangan Terkait Transaksi
CREATE TABLE laporan_keuangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaksi_id INT,
    total_pemasukan DECIMAL(10,2),
    tanggal DATE,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id)
);

-- Tabel Stok Barang (Inventory)
CREATE TABLE stok_barang (
    id_barang INT PRIMARY KEY AUTO_INCREMENT,
    kode_barang VARCHAR(20) UNIQUE,
    nama_barang VARCHAR(100),
    kategori ENUM('Deterjen', 'Pewangi', 'Pemutih', 'Peralatan', 'Supplies'),
    jumlah INT,
    satuan VARCHAR(20),
    harga_satuan DECIMAL(10,2),
    min_stok INT,
    max_stok INT,
    lokasi_simpan VARCHAR(50),
    STATUS ENUM('Aktif', 'Tidak Aktif') DEFAULT 'Aktif',
    tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pencatatan Penggunaan Barang
CREATE TABLE penggunaan_stok (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_penggunaan VARCHAR(20),
    tanggal_pakai DATETIME,
    id_barang INT,
    jumlah_pakai INT,
    keterangan TEXT,
    FOREIGN KEY (id_barang) REFERENCES stok_barang(id_barang)
);

-- Tabel Gaji Karyawan (Finance) - versi revisi
CREATE TABLE gaji_karyawan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    karyawan_id INT,
    bulan INT,
    tahun INT,
    gaji DECIMAL(10,2),
    status_pembayaran ENUM('Pending', 'Dibayar') DEFAULT 'Pending',
    tanggal_pembayaran DATE,
    FOREIGN KEY (karyawan_id) REFERENCES karyawan(id) ON DELETE CASCADE
);



-- Keuangan Umum (Pemasukan & Pengeluaran)
CREATE TABLE keuangan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kode_transaksi VARCHAR(20),
    tanggal DATE,
    jenis ENUM('Pemasukan', 'Pengeluaran'),
    kategori VARCHAR(50),
    jumlah DECIMAL(10,2),
    metode_pembayaran ENUM('Cash', 'Transfer', 'Debit', 'Credit'),
    keterangan TEXT,
    STATUS ENUM('Pending', 'Selesai', 'Batal') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE gaji_karyawan
ADD COLUMN keuangan_id INT,
ADD FOREIGN KEY (keuangan_id) REFERENCES keuangan(id) ON DELETE CASCADE;

-- ==== INSERT DATA DUMMY REALISTIS ====

INSERT INTO pelanggan (nama, kontak) VALUES
('Andi Setiawan', '081234567890'),
('Siti Aminah', '082112345678'),
('Rizky Hidayat', '085123456789'),
('Linda Mariani', '083823456789');

INSERT INTO karyawan (nama, posisi, gaji, tanggal_masuk) VALUES
('Budi Hartono', 'Kasir', 2500000.00, '2023-03-10'),
('Yuni Arifah', 'Petugas Laundry', 2700000.00, '2022-11-05'),
('Dewi Lestari', 'Admin', 2800000.00, '2023-01-15');

INSERT INTO transaksi (pelanggan_id, karyawan_id, tanggal, STATUS) VALUES
(1, 1, '2025-06-01', 'masuk'),
(2, 2, '2025-06-02', 'diproses'),
(3, 2, '2025-06-03', 'selesai'),
(4, 3, '2025-06-03', 'diambil');

INSERT INTO item_laundry (transaksi_id, jenis_barang, berat_kg) VALUES
(1, 'baju', 3.5),
(2, 'sprei', 2.0),
(3, 'boneka', 1.0),
(4, 'bedcover', 4.0);

INSERT INTO layanan (nama_layanan, harga_per_kg) VALUES
('cuci basah', 4000.00),
('cuci kering', 5000.00),
('setrika', 3000.00),
('cuci setrika', 6000.00);

INSERT INTO item_layanan (item_laundry_id, layanan_id) VALUES
(1, 4),
(2, 2),
(3, 4),
(4, 4);

INSERT INTO pembayaran (transaksi_id, jumlah, metode, tanggal) VALUES
(1, 21000.00, 'cash', '2025-06-01'),
(2, 10000.00, 'transfer', '2025-06-02'),
(3, 6000.00, 'cash', '2025-06-03'),
(4, 24000.00, 'transfer', '2025-06-04');

INSERT INTO laporan_keuangan (transaksi_id, total_pemasukan, tanggal) VALUES
(1, 21000.00, '2025-06-01'),
(2, 10000.00, '2025-06-02'),
(3, 6000.00, '2025-06-03'),
(4, 24000.00, '2025-06-04');

INSERT INTO stok_barang (kode_barang, nama_barang, kategori, jumlah, satuan, harga_satuan, min_stok, max_stok, lokasi_simpan, STATUS) VALUES
('DT001', 'Deterjen Bubuk', 'Deterjen', 20, 'kg', 25000.00, 5, 50, 'Gudang A', 'Aktif'),
('PW001', 'Pewangi Lavender', 'Pewangi', 15, 'liter', 30000.00, 5, 30, 'Gudang B', 'Aktif'),
('PM001', 'Pemutih Cair', 'Pemutih', 10, 'liter', 20000.00, 3, 20, 'Gudang A', 'Aktif'),
('PR001', 'Sarung Tangan Karet', 'Peralatan', 5, 'pasang', 15000.00, 2, 10, 'Rak 1', 'Aktif');

INSERT INTO penggunaan_stok (kode_penggunaan, tanggal_pakai, id_barang, jumlah_pakai, keterangan) VALUES
('PGN001', '2025-06-01 09:00:00', 1, 2, 'Deterjen untuk 5 kg cucian'),
('PGN002', '2025-06-02 09:30:00', 2, 1, 'Pewangi digunakan untuk pelanggan setrika'),
('PGN003', '2025-06-03 10:00:00', 3, 1, 'Pemutih untuk pakaian putih'),
('PGN004', '2025-06-04 11:00:00', 1, 1, 'Tambahan deterjen untuk cucian berat');

INSERT INTO gaji_karyawan (karyawan_id, bulan, tahun, gaji, status_pembayaran, tanggal_pembayaran) VALUES
(1, 6, 2025, 2500000.00, 'Dibayar', '2025-06-05'),
(2, 6, 2025, 2700000.00, 'Dibayar', '2025-06-05'),
(3, 6, 2025, 2800000.00, 'Pending', NULL);

INSERT INTO keuangan (kode_transaksi, tanggal, jenis, kategori, jumlah, metode_pembayaran, keterangan, STATUS) VALUES
('TRX001', '2025-06-01', 'Pemasukan', 'Laundry Masuk', 21000.00, 'Cash', 'Pembayaran pelanggan Andi', 'Selesai'),
('TRX002', '2025-06-02', 'Pemasukan', 'Laundry Masuk', 10000.00, 'Transfer', 'Pembayaran pelanggan Siti', 'Selesai'),
('TRX003', '2025-06-05', 'Pengeluaran', 'Gaji Karyawan', 2500000.00, 'Transfer', 'Gaji Budi bulan Juni', 'Selesai'),
('TRX004', '2025-06-05', 'Pengeluaran', 'Gaji Karyawan', 2700000.00, 'Transfer', 'Gaji Yuni bulan Juni', 'Selesai');


--                                                   TRIGGER 

-- Trigger untuk delete otomatis
DELIMITER //
CREATE TRIGGER delete_gaji_after_keuangan_delete 
AFTER DELETE ON keuangan
FOR EACH ROW 
BEGIN
    IF OLD.kategori = 'Gaji Karyawan' THEN
        DELETE FROM gaji_karyawan 
        WHERE gaji = OLD.jumlah 
        AND MONTH(tanggal_pembayaran) = MONTH(OLD.tanggal)
        AND YEAR(tanggal_pembayaran) = YEAR(OLD.tanggal);
    END IF;
END//

-- Trigger untuk update otomatis
DELIMITER //

CREATE TRIGGER update_gaji_after_keuangan_update
AFTER UPDATE ON keuangan
FOR EACH ROW 
BEGIN
    IF NEW.kategori = 'Gaji Karyawan' THEN
        UPDATE gaji_karyawan 
        SET gaji = NEW.jumlah,
            tanggal_pembayaran = NEW.tanggal
        WHERE transaksi_id = NEW.kode_transaksi;
    END IF;
END//

DELIMITER ;


-- Trigger untuk insert otomatis
DELIMITER //

CREATE TRIGGER insert_gaji_after_keuangan_insert
AFTER INSERT ON keuangan
FOR EACH ROW 
BEGIN
    DECLARE karyawan_id INT;

    IF NEW.kategori = 'Gaji Karyawan' THEN
        -- Ambil ID karyawan yang namanya ada dalam keterangan (pastikan hanya satu hasil)
        SELECT id INTO karyawan_id
        FROM karyawan
        WHERE INSTR(NEW.keterangan, nama) > 0
        LIMIT 1;

        IF karyawan_id IS NOT NULL THEN
            INSERT INTO gaji_karyawan (
                karyawan_id,
                bulan,
                tahun,
                gaji,
                status_pembayaran,
                tanggal_pembayaran
            ) VALUES (
                karyawan_id,
                MONTH(NEW.tanggal),
                YEAR(NEW.tanggal),
                NEW.jumlah,
                'Dibayar',
                NEW.tanggal
            );
        END IF;
    END IF;
END//

DELIMITER ;


-- Tambahkan index untuk meningkatkan performa trigger
ALTER TABLE gaji_karyawan
ADD INDEX idx_gaji_tanggal (gaji, tanggal_pembayaran);

ALTER TABLE keuangan
ADD INDEX idx_kategori_tanggal (kategori, tanggal);


-- Jalankan query ini di database untuk memastikan struktur tabel benar
ALTER TABLE karyawan MODIFY COLUMN id INT AUTO_INCREMENT;
ALTER TABLE karyawan MODIFY COLUMN gaji DECIMAL(10,2);
ALTER TABLE karyawan MODIFY COLUMN tanggal_masuk DATE;