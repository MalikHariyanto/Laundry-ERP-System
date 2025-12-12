<?php
require_once '../../controller/FinanceController.php';
require_once '../../config/koneksi.php';
include '../layout/header.php';
include '../layout/sidebar.php';
?>

<div class="container">
    <div class="page-header">
        <h2><i class="fas fa-plus-circle"></i> Input Transaksi Keuangan</h2>
    </div>

    <?php if(isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php
        switch($_GET['error']) {
            case '1':
                echo 'Gagal menyimpan transaksi';
                break;
            case '2':
                echo 'Gagal mengupload file';
                break;
            default:
                echo 'Terjadi kesalahan';
        }
        ?>
    </div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">
        Transaksi berhasil disimpan
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="proses_transaksi.php" method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jenis Transaksi</label>
                            <select name="jenis" class="form-control" required>
                                <option value="">Pilih Jenis</option>
                                <option value="Pemasukan">Pemasukan</option>
                                <option value="Pengeluaran">Pengeluaran</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" 
                                   value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Kategori</label>
                            <select name="kategori" class="form-control" required>
                                <option value="">Pilih Kategori</option>
                                <optgroup label="Pemasukan">
                                    <option value="Jasa Laundry">Jasa Laundry</option>
                                    <option value="Lainnya">Lainnya</option>
                                </optgroup>
                                <optgroup label="Pengeluaran">
                                    <option value="Bahan Baku">Bahan Baku</option>
                                    <option value="Operasional">Operasional</option>
                                    <option value="Utilitas">Utilitas</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Jumlah (Rp)</label>
                            <input type="number" name="jumlah" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-control" required>
                                <option value="Cash">Cash</option>
                                <option value="Transfer">Transfer Bank</option>
                                <option value="Debit">Kartu Debit</option>
                                <option value="Credit">Kartu Kredit</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Transaksi
                        </button>
                        <a href="list.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('select[name="jenis"]').addEventListener('change', function() {
    const kategoriSelect = document.querySelector('select[name="kategori"]');
    const optgroups = kategoriSelect.querySelectorAll('optgroup');
    
    optgroups.forEach(group => {
        if (group.label === this.value) {
            group.style.display = '';
        } else {
            group.style.display = 'none';
        }
    });
});
</script>

<?php include '../layout/footer.php'; ?>