<?php
    function htmlSwitch($title, $akses, $val) {
        echo "
            <div class='w-info-chart-meta'>
                <div class='progress-wrap'>
                    <div class='clearfix progress-meta'>
                        <span style='font-size:14px;' class='pull-left progress-label'>
                            <p>" . $title . "</p>
                        </span>
                        <span class='pull-right'>
                            <input type='checkbox' onchange='switchUserAksesOnOffChanged(this)'
                                data-tag='" .  $akses . "'
                                class='switch-small'
                                " . $val . "
                            />
                        </span>
                    </div>
                    <div class='progress'><div style='width: 100%;' class='progress-bar progress-bar-success'></div></div>
                </div>
            </div>
        ";
    }
?>

<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <a href="<?= base_url(); ?>perusahaan/usertablet?outlet=<?=$outlet?>" class="btn btn-default"><i
                                    class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Penjualan</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Kasir', 'AllowKasir', $akses['AllowKasir'])?>
                        <?=htmlSwitch('Ubah Nama Resto', 'AllowEditNamaStand', $akses['AllowEditNamaStand'])?>
                        <?=htmlSwitch('Tambah Item', 'AllowTambahMenu', $akses['AllowTambahMenu'])?>
                        <?=htmlSwitch('Edit Item', 'AllowEditMenu', $akses['AllowEditMenu'])?>
                        <?=htmlSwitch('Hapus Item', 'AllowHapusMenu', $akses['AllowHapusMenu'])?>
                        <?=htmlSwitch('Edit Penjualan', 'AllowEditPenjualan', $akses['AllowEditPenjualan'])?>
                        <?=htmlSwitch('Hapus Penjualan', 'AllowHapusPenjualan', $akses['AllowHapusPenjualan'])?>
                        <?=htmlSwitch('Hapus Order', 'AllowHapusOrder', $akses['AllowHapusOrder'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Data Rekening</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Tambah Data Rekening', 'AllowTambahDataRekening', $akses['AllowTambahDataRekening'])?>
                        <?=htmlSwitch('Edit Data Rekening', 'AllowEditDataRekening', $akses['AllowEditDataRekening'])?>
                        <?=htmlSwitch('Hapus Data Rekening', 'AllowHapusDataRekening', $akses['AllowHapusDataRekening'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Uang Masuk</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Tambah Uang Masuk', 'AllowTambahUangMasuk', $akses['AllowTambahUangMasuk'])?>
                        <?=htmlSwitch('Edit Uang Masuk', 'AllowEditUangMasuk', $akses['AllowEditUangMasuk'])?>
                        <?=htmlSwitch('Hapus Uang Masuk', 'AllowHapusUangMasuk', $akses['AllowHapusUangMasuk'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Uang Keluar</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Tambah Uang Keluar', 'AllowTambahUangKeluar', $akses['AllowTambahUangKeluar'])?>
                        <?=htmlSwitch('Edit Uang Keluar', 'AllowEditUangKeluar', $akses['AllowEditUangKeluar'])?>
                        <?=htmlSwitch('Hapus Uang Keluar', 'AllowHapusUangKeluar', $akses['AllowHapusUangKeluar'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Laporan</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Laporan Penjualan', 'AllowLaporanPenjualan', $akses['AllowLaporanPenjualan'])?>
                        <?=htmlSwitch('Laporan Rekap Penjualan', 'AllowLaporanRekapPenjualan', $akses['AllowLaporanRekapPenjualan'])?>
                        <?=htmlSwitch('Laporan Rekap Pembayaran', 'AllowLaporanRekapPembayaran', $akses['AllowLaporanRekapPembayaran'])?>
                        <?=htmlSwitch('Laporan Saldo Kas / Rekening', 'AllowLaporanSaldoKasRekening', $akses['AllowLaporanSaldoKasRekening'])?>
                        <?=htmlSwitch('Laporan Pembelian', 'AllowLaporanPembelian', $akses['AllowLaporanPembelian'])?>
                        <?=htmlSwitch('Laporan Rekap Pembelian', 'AllowLaporanRekapPembelian', $akses['AllowLaporanRekapPembelian'])?>
                        <?=htmlSwitch('Laporan Stok', 'AllowLaporanStok', $akses['AllowLaporanStok'])?>
                        <?=htmlSwitch('Laporan Kartu Stok', 'AllowLaporanKartuStok', $akses['AllowLaporanKartuStok'])?>
                        <?=htmlSwitch('Laporan Rekap Mutasi Stok', 'AllowLaporanRekapMutasiStok', $akses['AllowLaporanRekapMutasiStok'])?>
                        <?=htmlSwitch('Laporan di Awan', 'AllowLaporanAwan', $akses['AllowLaporanAwan'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Lainnya</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Pengaturan', 'AllowPengaturan', $akses['AllowPengaturan'])?>
                        <?=htmlSwitch('Aktivasi', 'AllowAktivasi', $akses['AllowAktivasi'])?>
                        <?=htmlSwitch('Hapus Data Transaksi', 'AllowHapusDataTransaksi', $akses['AllowHapusDataTransaksi'])?>
                        <?=htmlSwitch('Download Data dari Awan', 'AllowDownloadDataAwan', $akses['AllowDownloadDataAwan'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Pembelian</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Pembelian', 'AllowPembelian', $akses['AllowPembelian'])?>
                        <?=htmlSwitch('Tambah Item Pembelian', 'AllowTambahItemPembelian', $akses['AllowTambahItemPembelian'])?>
                        <?=htmlSwitch('Edit Item Pembelian', 'AllowEditItemPembelian', $akses['AllowEditItemPembelian'])?>
                        <?=htmlSwitch('Hapus Item Pembelian', 'AllowHapusItemPembelian', $akses['AllowHapusItemPembelian'])?>
                        <?=htmlSwitch('Tambah Supplier', 'AllowTambahSupplier', $akses['AllowTambahSupplier'])?>
                        <?=htmlSwitch('Edit Nama Supplier', 'AllowEditSupplier', $akses['AllowEditSupplier'])?>
                        <?=htmlSwitch('Hapus Supplier', 'AllowHapusSupplier', $akses['AllowHapusSupplier'])?>
                        <?=htmlSwitch('Edit Pembelian', 'AllowEditPembelian', $akses['AllowEditPembelian'])?>
                        <?=htmlSwitch('Hapus Pembelian', 'AllowHapusPembelian', $akses['AllowHapusPembelian'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Stok</h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">                        
                        <?=htmlSwitch('Koreksi Stok', 'AllowKoreksiStok', $akses['AllowKoreksiStok'])?>
                        <?=htmlSwitch('Tambah Item Stok', 'AllowTambahItemStok', $akses['AllowTambahItemStok'])?>
                        <?=htmlSwitch('Edit Item Stok', 'AllowEditItemStok', $akses['AllowEditItemStok'])?>
                        <?=htmlSwitch('Hapus Item Stok', 'AllowHapusItemStok', $akses['AllowHapusItemStok'])?>
                        <?=htmlSwitch('Edit Koreksi Stok', 'AllowEditKoreksiStok', $akses['AllowEditKoreksiStok'])?>
                        <?=htmlSwitch('Hapus Koreksi Stok', 'AllowHapusKoreksiStok', $akses['AllowHapusKoreksiStok'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class=" widget-block">
                        <button class="btn  btn-danger btn-block" id="konfirmdeleteusertablet">
                            <i class="icon-play"></i>Hapus user <?= $selecteduser; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>