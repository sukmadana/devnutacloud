<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 10/12/2015
 * Time: 10:20
 */
?>
<?php
/*
 * This file created by Em Husnan
 * Copyright 2015
 */
?>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <a href="<?= base_url(); ?>perusahaan/user" class="btn btn-default"><i
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
                    <h4>Daily Report untuk <?= $selectedusername; ?></h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Terima Daily Report
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox"
                                                       onchange="switchUserDailyReportOnOffChanged(this)"
                                                       data-tag="LaporanPenjualan"
                                                       class="switch-small"
                                                    <?= $allowDailyRerport; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Hak Akses nutacloud untuk <?= $selectedusername; ?></h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <?php
                        foreach ($pekerjaan as $v) {
                            echo '
                        <div id="div' . $v->key . '" class="w-info-chart-meta" ' . (!$v->isVisible($pekerjaan) ? "style=\"display:none\"" : "\"\"") . '>
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                ' . $v->name . ' 
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="' . $v->key . '"
                                                       id="' . $v->name . '"
                                                       class="switch-small"
                                                    ' . ($v->isAllow == 1 ? "checked" : "") . '
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                            ';
                        }
                        ?>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Penjualan
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanPenjualan"
                                                       class="switch-small"
                                                    <?= $allowLaporanPenjualan; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Rekap Penjualan
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanRekapPenjualan"
                                                       class="switch-small"
                                                    <?= $allowLaporanRekapPenjualan; ?>
                                                />
                                        </span>
                                </div>
                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Rekap Penjualan per Kategori
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanRekapPenjualanPerKategori"
                                                       class="switch-small"
                                                    <?= $allowLaporanRekapPenjualanPerKategori; ?>
                                                />
                                        </span>
                                </div>
                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Rekap Pembayaran
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanRekapPembayaran"
                                                       class="switch-small"
                                                    <?= $allowLaporanRekapPembayaran; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Pembelian
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanPembelian"
                                                       class="switch-small"
                                                    <?= $allowLaporanPembelian; ?>/>
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Rekap Pembelian
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanRekapPembelian"
                                                       class="switch-small"
                                                    <?= $allowLaporanRekapPembelian; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Saldo Kas / Rekening
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanSaldoKasRekening"
                                                       class="switch-small"
                                                    <?= $allowLaporanSaldoKasRekening; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Stok
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanStok"
                                                       class="switch-small"
                                                    <?= $allowLaporanStok; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan KartuStok
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanKartuStok"
                                                       class="switch-small"
                                                    <?= $allowLaporanKartuStok; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Rekap Mutasi Stok
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanRekapMutasiStok"
                                                       class="switch-small"
                                                    <?= $allowLaporanRekapMutasiStok; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Laba
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanLaba"
                                                       class="switch-small"
                                                    <?= $allowLaporanLaba; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Laporan Pengeluaran
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="LaporanPengeluaran"
                                                       class="switch-small"
                                                    <?= $allowLaporanPengeluaran; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                                <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                Hapus Data
                                            </p>
                                        </span>
                                    <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserAksesOnOffChanged(this)"
                                                       data-tag="HapusData"
                                                       class="switch-small"
                                                    <?= $allowHapusData; ?>
                                                />
                                        </span>
                                </div>

                                <div class="progress">
                                    <div style="width: 100%;" class="progress-bar progress-bar-success">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class=" widget-block">
                        <button class="btn  btn-danger btn-block" id="konfirmdeleteuser">
                            <i class="icon-play"></i>Hapus user <?= $selectedusername; ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4>Outlet untuk user <?= $selectedusername; ?></h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <?php foreach ($daftardevice as $cabang) { ?>
                            <div class="w-info-chart-meta">
                                <div class="progress-wrap">
                                    <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">
                                            <p>
                                                <?= $cabang['namacabang'] ?><br/>
                                                <span style="font-size: 12px;"><?= $cabang['alamat']; ?></span>
                                            </p>
                                        </span>
                                        <span class="pull-right">
                                                <input type="checkbox" onchange="switchUserCabangOnOffChanged(this)"
                                                       class="switch-small"
                                                       data-tag="<?= $cabang['outletid']; ?>"
                                                    <?= $cabang['allow'] ? 'checked' : ''; ?>/>
                                        </span>
                                    </div>

                                    <div class="progress">
                                        <div style="width: 100%;" class="progress-bar progress-bar-success">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

