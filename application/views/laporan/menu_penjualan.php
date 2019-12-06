<div class="container-fluid" style="background: #fff">
    <!--    <div class="page-breadcrumb">-->
    <!--        <div class="row">-->
    <!--            <div class="col-md-7">-->
    <!--                <div class="page-breadcrumb-wrap">-->
    <!--                    <div class="page-breadcrumb-info">-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="col-md-5">-->
    <!--            </div>-->
    <!--        </div>-->
    <!--    </div>-->
    <br>
    <div class="alert alert-warning">
        Berdasarkan Tanggal Aktual
    </div>
    <div class="row">
        <?php if ($visibilityMenu['LaporanPenjualan']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualan'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br /> secara umum</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan-->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanRekapPenjualan']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualan'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Rekap Penjualan</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-list-alt"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan<br /> rekap penjualan</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPenjualanPerJam']) : ?>
            <!--Rekap Pejualan-->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualanperjam'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan per jam</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-clock-o"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br /> per jam</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPenjualanPerKasir']) : ?>
            <!--Rekap Pejualan per jam -->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualanperkasir'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan per kasir</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-user"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br /> per kasir</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Rekap Pejualan per kasir -->
        <?php endif ?>
    </div>
    <div class="row">
        <?php if ($visibilityMenu['LaporanRekapPenjualanPerKategori']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualanperkategori'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan per kategori</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-tags"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br /> per kategori</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Rekap Pejualan per kategori -->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanRataRataBelanjaPelanggan']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rataratabelanjaperpelanggan'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Rata-rata belanja <br /> per pelanggan</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-users"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan rata-rata<br />belanja pelanggan</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanDiskon']) : ?>
            <!--Rata2 belanja pelanggan -->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/diskon'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Diskon</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-percent"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan diskon<br /> yang sudah dikeluarkan</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPajak']) : ?>
            <!--Rekap Pejualan diskon-->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/pajak'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Pajak</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-sticky-note-o"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan pajak<br />per transaksi</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Rekap pajak -->
        <?php endif ?>
    </div>
    <div class="row">
        <?php if ($visibilityMenu['LaporanRekapPembayaran']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappembayaran'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Rekap Pembayaran</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-money"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan <br />rekap pembayaran pelanggan</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Rekap Pembayaran-->
        <?php endif ?>
        <?php if ($isLaporanPriceVarianVisible && $visibilityMenu['LaporanPenjualanVarian']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualanvarian'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan Varian</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-cart-plus"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br />berdasarkan varian</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($isLaporanPriceVarianVisible && $visibilityMenu['LaporanPenjualanPilihanEkstra']) : ?>
            <!--Penjualan varian-->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualanekstra'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan Pilihan Ekstra</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-cart-plus"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br />per pilihan ekstra</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan Ekstra-->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPembulatan']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/pembulatan'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Pembulatan</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-cart-plus"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan <br />pembulatan penjualan</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Pembulatan-->
        <?php endif ?>
    </div>
    <?php if ($visibilityMenu['LaporanPesananBelumLunas']) : ?>
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/pesananbelumlunas'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Pesanan belum Lunas</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan pesanan<br /> yang belum dilunasi</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPenjualanVoid']) : ?>
            <!--Penjualan-->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualanvoid'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan Void</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-list-alt"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan yang dihapus</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <!--Rekap Pejualan Void -->
        <?php if ($isLaporanOpsiMakanVisible && $visibilityMenu['LaporanPenjualanPerTipe']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualanpertipepenjualan'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan per Tipe</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-tags"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br /> per Tipe Penjualan</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Rekap Pejualan per opsi makan -->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPenjualanPerJamItem']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualanperjamperitem'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan per Jam per Item</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-tags"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Setiap jam ada item apa saja yang terjual</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Rekap Pejualan per opsi makan -->

        </div>
    <?php endif ?>
    <div class="row">
        <?php if ($visibilityMenu['LaporanRiwayatBelanjaPelanggan'] && $visibilityMenu['CustomerView']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/riwayatpelanggan'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Riwayat Belanja Pelanggan</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-sticky-note-o"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan riwayat<br />belanja pelanggan</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPesananBatal']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/pesananbatal'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Pesanan batal</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan pesanan yg batal<br />atau yang Qty-nya dikurangi</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan-->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPenjualanPerKategoriSemuaItem']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualanperkategorisemuaitem'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan per Kategori Semua Item</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan penjualan semua kategori<br />Termasuk yang tidak laku</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan-->
        <?php endif ?>
    </div>

    <?php if (
        $visibilityMenu['LaporanPenjualanPerShift']
        || $visibilityMenu['LaporanRekapPenjualanPerShift']
        || $visibilityMenu['LaporanRekapShift']
        || $visibilityMenu['LaporanPenjualanPerKasirShift']
    ) : ?>
        <div class="alert alert-warning">
            Tanggal Berdasarkan Shift (Buka-Tutup Outlet)
        </div>
    <?php endif ?>

    <div class="row">
        <?php if ($visibilityMenu['LaporanPenjualanPerShift']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualanpershift'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan per Shift</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br /> dikelompokkan per shift</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan-->
        <?php endif ?>

        <?php if ($visibilityMenu['LaporanRekapPenjualanPerShift']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappenjualanpershift'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Rekap Penjualan per Shift</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-list-alt"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan rekap penjualan<br /> dikelompokkan per shift</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanRekapShift']) : ?>
            <!--Rekap Pejualan-->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekapshift'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Rekap Shift</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-clock-o"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan rekap shift<br /> saat tutup outlet</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
    </div>
    <div class="row">
        <?php if ($visibilityMenu['LaporanPenjualanKategoriPerShift']) {  ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualankategoripershift'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan Kategori per Shift</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan penjualan Kategori<br /> dikelompokkan per shift</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan variant per shift-->
        <?php } ?>
        <?php if ($visibilityMenu['LaporanPenjualanVarianPerShift'] && $isLaporanPriceVarianVisible) { ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualanvarianpershift'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan Varian per Shift</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan penjualan Varian<br /> dikelompokkan per shift</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan variant per shift-->
        <?php } ?>
        <?php if ($visibilityMenu['LaporanPenjualanPilihanEkstraPerShift'] && $isLaporanPriceVarianVisible) { ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/penjualanpilihanekstrapershift'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Penjualan Pilihan Ekstra per Shift</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan penjualan Pilihan Ekstra<br /> dikelompokkan per shift</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Penjualan variant per shift-->
        <?php } ?>

        <?php if ($isLaporanOpsiMakanVisible && $visibilityMenu['LaporanTipePenjualanPerShift']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/opsimakanpershift'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Tipe Penjualan per Shift</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-shopping-cart"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan penjualan<br /> dikelompokan per shift per tipe</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
    </div>
</div>