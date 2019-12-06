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
        <?php if ($visibilityMenu['LaporanStok']): ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/stok'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Stok</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-tags"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan <br/>posisi stok</span>
                        </div>
                    </div>
                </a>
            </div><!--Stok-->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanKartuStok']): ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/kartustok'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Kartu Stok</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-tags"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan<br/>pergerakan stok</span>
                        </div>
                    </div>
                </a>
            </div><!--Kartu Stok-->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanRekapMutasiStok']): ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/rekapmutasistok'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Rekap Mutasi Stok</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-tags"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan<br/>rekap mutasi stok</span>
                        </div>
                    </div>
                </a>
            </div><!--Rekap Mutasi Stok-->
        <?php endif ?>
    </div>
</div>
