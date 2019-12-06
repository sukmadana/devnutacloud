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
        <?php if ($visibilityMenu['LaporanSaldoKasRekening']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/saldokasrekening'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Saldo Kas/Rekening</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-money"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan <br />saldo kas atau rekening</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanMutasiKasRekening']) : ?>
            <!--Saldo Kas / Rekening-->
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/mutasikas'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Mutasi Kas/Rekening</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-money"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-title">Menampilkan laporan <br />mutasi kas atau rekening</span>
                        </div>
                    </div>
                </a>
            </div>
            <!--Mutasi Kas / Rekening-->
        <?php endif ?>
        <?php if ($visibilityMenu['LaporanPengeluaran']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/pengeluaran'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Pengeluaran Uang Operasional</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-money"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-value"></span>
                            <span class="w-meta-title">Menampilkan laporan <br />pengeluaran uang operasional</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
        <!--Pengeluaran Uang Operasional-->
        <?php if ($visibilityMenu['LaporanPengeluaranPerDibayarKe']) : ?>
            <div class="col-md-3 col-sm-6">
                <a href="<?= base_url('laporan/pengeluarangrub'); ?>" class="nuta-link-box-menu">
                    <div class="iconic-w-wrap">
                        <span class="stat-w-title">Pengeluaran per Dibayar Ke</span>
                        <div class="ico-cirlce-widget ">
                            <span><i class="fa fa-money"></i></span>
                        </div>
                        <div class="w-meta-info">
                            <span class="w-meta-value"></span>
                            <span class="w-meta-title">Menampilkan laporan <br />pengeluaran dikelompokkan berdasarkan Dibayar Ke</span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif ?>
    </div>
</div>