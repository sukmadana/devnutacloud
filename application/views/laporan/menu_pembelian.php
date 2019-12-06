<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">

                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>
    <div class="row">
        <?php if ($visibilityMenu['LaporanPembelian']): ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/pembelian'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Pembelian</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-cart-arrow-down"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan pembelian<br/>secara umum</span>
                        </div>
                    </div>
                </a>
            </div><!--Pembelian-->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanRekapPembelian']): ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekappembelian'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Rekap Pembelian</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-cart-plus"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan <br/>rekap pembelian</span>
                        </div>
                    </div>
                </a>
            </div><!--Rekap Pembelian -->
        <?php endif ?>
    </div>
</div>
