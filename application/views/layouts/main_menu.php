<?php
$d = getLoggedInNamaPerusahaan();
$n = getLoggedInUsername();
if (trim($n) === '') {
    $n = 'Single Outlet';
}
if ($d == "Individual") {
    $d = "Single Outlet";
}

if (isset($menu)) {
    $menu = $menu;
} else {
    $menu = '';
}

?>
<ul class="list-accordion">
    <li class="list-accordion-account">
        <a href="#" class="clearfix dropdown-toggle">
            <span class="user-thumb pull-left">
                <img src="<?= str_replace('live.nutacloud.com/uploaded_photos/', 'nutacloud.com/uploaded_photos/', str_replace('live.nutacloud.com//uploaded_photos/', 'nutacloud.com/uploaded_photos/', str_replace('www.nutacloud.com//uploaded_photos/', 'nutacloud.com/uploaded_photos/', str_replace('www.nutacloud.com/uploaded_photos/', 'nutacloud.com/uploaded_photos/', getFoto())))) ; ?>" alt="image">
            </span>
            <span class="user-info pull-left"><?= $n; ?> <br /><?= $d; ?></span>
        </a>
        <ul class="sub-menu">
            <li><a href="<?= base_url(); ?>cloud/account">
                    <span class="user-nav-icon"><i class="fa fa-user"></i></span>
                    <span class="user-nav-label">Akun Saya</span>
                </a>
            </li>
            <li>
                <a href="<?= base_url(); ?>authentication/logout">
                    <span class="user-nav-icon"><i class="fa fa-lock"></i></span>
                    <span class="user-nav-label">Logout</span>
                </a>
            </li>
        </ul>
    </li>
    <?php if (!isAccountExpired()) { ?>
        <?php if ($visibilityMenu['Dashboard']) { ?>
            <li>
                <a href="<?= base_url(); ?>cloud/main" class="nuta-nav">
                    <span class="nav-icon icon"><i class="fa fa-pie-chart"></i></span>
                    <span class="nav-label nuta-center-nav-menu">Dashboard</span>
                </a>
            </li>
        <?php } ?>
        <?php if ($visibilityMenu['ItemView']) { ?>
            <li>
                <a href="<?= base_url(); ?>item/index" class="nuta-nav <?php is_active($menu, 'produk') ?>">
                    <span class="nav-icon icon"><i class="fa fa-tags"></i></span>
                    <span class="nav-label nuta-center-nav-menu">Items</span>
                </a>
            </li>
        <?php } ?>
        <?php if ($visibilityMenu['OutletView']) { ?>
            <li>
                <a href="<?= base_url(); ?>perusahaan/outlet" class="nuta-nav">
                    <span class="nav-icon icon"><i class="fa fa-building"></i></span>
                    <span class="nav-label nuta-center-nav-menu">Outlet</span>
                </a>
            </li>
            <!-- <li>
        <a href="<?= base_url(); ?>perusahaan/perangkat" class="nuta-nav">
        <span class="nav-icon icon"><i class="fa fa-tablet"></i></span>
        <span class="nav-label nuta-center-nav-menu">Perangkat</span>
    </a>
</li> -->
        <?php } ?>
        <?php if ($visibilityMenu['CustomerView']) { ?>
            <li><a href="<?= base_url(); ?>pelanggan/daftarpelanggan" class="nuta-nav">
                    <span class="nav-icon icon"><i class="fa fa-users "></i></span>
                    <span class="nav-label nuta-center-nav-menu">Pelanggan</span></a>
            </li>
        <?php } ?>
        <?php if ($visibilityMenu['PromoView']) {
                log_message('error', 'Promo View c visible'); ?>
            <li><a href="<?= base_url(); ?>promo/listpromo" class="nuta-nav">
                    <span class="nav-icon icon"><i class="fa fa-percent "></i></span>
                    <span class="nav-label nuta-center-nav-menu">Promo</span></a>
            </li>
        <?php } ?>
        <?php if (getLoggedInMenuPerusahaanVisibility()) { ?>
            <li><a href="<?= base_url(); ?>perusahaan/user" class="nuta-nav">
                    <span class="nav-icon icon"><i class="fa fa-user"></i></span>
                    <span class="nav-label nuta-center-nav-menu">User</span></a>
            </li>
            <li style="display:none;"><a href="<?= base_url(); ?>perusahaan/usertablet" class="nuta-nav">
                    <span class="nav-icon icon"><i class="fa fa-user"></i></span>
                    <span class="nav-label nuta-center-nav-menu">User Tablet</span></a>
            </li>
        <?php } ?>
        <?php if ($visibilityMenu['SupplierView']) { ?>
        <?php } ?>
        <?php if ($visibilityMenu['PurchaseView']) { ?>
        <?php } ?>
        <?php if (($visibilityMenu['StockView'] && $isLaporanStokVisible)
                || ($visibilityMenu['IncomingStockView'] && $isLaporanStokVisible)
                || ($visibilityMenu['OutgoingStockView'] && $isLaporanStokVisible)
                || ($visibilityMenu['TransferStockView'] && $isLaporanStokVisible)
                || ($visibilityMenu['SupplierView'] && $isLaporanPembelianVisible)
                || ($visibilityMenu['PurchaseView'] && $isLaporanPembelianVisible)
            ) { ?>
            <li>
                <a href="#" class="nuta-nav <?php is_active($menu, 'stok') ?>">
                    <span class="nav-icon icon"><i class="fa fa-check-square"></i></span>
                    <span class="nav-label">Stok</span></a>
                <ul class="sub-menu">
                    <?php if ($visibilityMenu['SupplierView'] && $isLaporanPembelianVisible) { ?>
                        <li>
                            <a href="<?= base_url(); ?>supplier/index">
                                <span class="nav-label ">Supplier</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($visibilityMenu['PurchaseView'] && $isLaporanPembelianVisible) { ?>
                        <li>

                            <a href="<?= base_url(); ?>pembelian">
                                <span class="nav-label">Pembelian</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($visibilityMenu['IncomingStockView'] && $isLaporanStokVisible) { ?>
                        <li>

                            <a href="<?= base_url(); ?>stokmasuk">
                                <span class="nav-label ">Stok Masuk</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($visibilityMenu['OutgoingStockView'] && $isLaporanStokVisible) { ?>
                        <li>

                            <a href="<?= base_url(); ?>stokkeluar">
                                <span class="nav-label ">Stok Keluar</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($visibilityMenu['StockView'] && $isLaporanStokVisible) { ?>
                        <li>

                            <a href="<?= base_url(); ?>koreksistok">
                                <span class="nav-label ">Koreksi Stok</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($visibilityMenu['TransferStockView'] && $isLaporanStokVisible && 1 == 0) { ?>
                        <li>
                            <a href="<?= base_url(); ?>transferstok">
                                <span class="nav-label ">Transfer Stok</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if ($visibilityMenu['MoneyView'] || $visibilityMenu['DataRekeningView'] || $visibilityMenu['CashBankOutView']) { ?>
            <li>
                <a href="#" class="nuta-nav <?php is_active($menu, 'uang') ?>">
                    <span class="nav-icon icon"><i class="fa fa-money"></i></span>
                    <span class="nav-label">Uang</span></a>
                <ul class="sub-menu">
                    <?php if ($visibilityMenu['DataRekeningView']) { ?>
                        <li>
                            <a href="<?= base_url(); ?>datarekening">
                                <span class="nav-label ">Data Rekening</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($visibilityMenu['MoneyView']) { ?>
                        <li>
                            <a href="<?= base_url(); ?>uangmasuk">
                                <span class="nav-label ">Uang Masuk</span>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($visibilityMenu['CashBankOutView']) { ?>
                        <li>
                            <a href="<?= base_url(); ?>uangkeluar">
                                <span class="nav-label ">Uang Keluar</span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <li><a href="#" class="nuta-nav">
                <span class="nav-icon icon"><i class="fa fa fa-line-chart"></i></span>
                <span class="nav-label nuta-center-nav-menu">Laporan</span></a>
            <ul class="sub-menu">
                <?php if ($visibilityMenu['LaporanPenjualan'] || $visibilityMenu['LaporanRekapPenjualan'] || $visibilityMenu['LaporanRekapPenjualanPerKategori']) { ?>
                    <li>
                        <a href="<?= base_url(); ?>laporan/menupenjualan">
                            <span class="nav-label ">Penjualan</span>
                        </a>
                    </li>
                    <?php }
                        if ($isLaporanPembelianVisible) {
                            if ($visibilityMenu['LaporanPembelian']) {
                                ?>
                        <li><a href="<?= base_url(); ?>laporan/menupembelian">
                                <span class="nav-label ">Pembelian</span>
                            </a>
                        </li>
                    <?php }
                        }
                        if (
                            $visibilityMenu['LaporanSaldoKasRekening']
                            || $visibilityMenu['LaporanMutasiKasRekening']
                            || $visibilityMenu['LaporanPengeluaran']
                            || $visibilityMenu['LaporanPengeluaranPerDibayarKe']
                        ) { ?>
                    <li>
                        <a href="<?= base_url(); ?>laporan/menukeuangan">
                            <span class="nav-label ">Keuangan</span>
                        </a>
                    </li>
                <?php }
                    if ($isLaporanStokVisible && ($visibilityMenu['LaporanStok'] || $visibilityMenu['LaporanKartuStok'] || $visibilityMenu['LaporanRekapMutasiStok'])) { ?>
                    <li>
                        <a href="<?= base_url(); ?>laporan/menustok">
                            <span class="nav-label ">Stok</span>
                        </a>
                    </li>
                <?php }
                    if ($visibilityMenu['LaporanLaba']) { ?>
                    <li>
                        <a href="<?= base_url(); ?>laporan/menulaba">
                            <span class="nav-label ">Laba</span>
                        </a>
                    </li>
                    <?php
                            //Request Mas Rahmat 28 Oktober 19:02
                            // rincian laba di hidden
                            $diTampilkan = false;
                            if ($diTampilkan) {
                                ?>
                        <li>
                            <a href="<?= base_url(); ?>laporan/rincianlaba">
                                <span class="nav-label ">Rincian Laba</span>
                            </a>
                        </li>
                <?php }
                    }
                    ?>

                <?php if ($visibilityMenu['LaporanPenjualan'] && $visibilityMenu['CustomerView']) { ?>
                    <li>
                        <a href="<?= base_url(); ?>laporan/feedback">
                            <span class="nav-label ">Feedback Pelanggan</span>
                        </a>
                    </li>
                <?php } ?>


            </ul>

        </li>
    <?php } ?>
    <?php if ($visibilityMenu['Aktivasi']) { ?>
        <li>
            <a href="<?= base_url(); ?>activation/index" class="nuta-nav">
                <span class="nav-icon icon"><i class="fa fa-check-square "></i></span>
                <span class="nav-label nuta-center-nav-menu">Aktivasi</span></a>
        </li>
    <?php } ?>
    <?php if (!isAccountExpired()) { ?>
        <?php
            if ($visibilityMenu['HapusData']) {
                ?>

            <li>
                <a href="<?= base_url(); ?>laporan/hapusdata" class="nuta-nav">
                    <span class="nav-icon icon"><i class="fa fa-trash-o "></i></span>
                    <span class="nav-label nuta-center-nav-menu">Hapus Data</span></a>
            </li>
        <?php } ?>
    <?php } ?>
</ul>