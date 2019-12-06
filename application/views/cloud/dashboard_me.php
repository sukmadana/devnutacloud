<?php
/*
 * This file created by Em Husnan
 * Copyright 2015
 */
?>
<div class="container-fluid">
    <?php if ($visibilityMenu['Dashboard']) { ?>
    <div class="page-breadcrumb">
        <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
        <?php if (count($outlets) <= 0) { ?>
        <div class="alert alert-warning" role="alert" style="text-align:center">
            Tidak ada tablet yang terhubung dengan ID Perusahaan
            <strong><?= getLoggedInUserID(); ?></strong></a>
        </div>
        <?php } ?>
        <form class="form-inline" method="get">
            <input type="hidden" name="date_start" value="<?= $selected_datestart; ?>" />
            <input type="hidden" name="date_end" value="<?= $selected_dateend; ?>" />
            <div class="dashboard-filter">
                <div class="dashboard-filter__col">
                    <div class="dashboard-filter__form">
                        <div><label>Outlet</label></div>
                        <div>
                            <select name="outlet" class="form-control dashboard-filter__input" id="outlet" width="max-width: 100%">
                                <?php foreach ($outlets as $k => $v) {
                                            if ($selected_outlet == $k) { ?>
                                <option value="<?= $k; ?>" selected><?= $v; ?></option>
                                <?php } else { ?>
                                <option value="<?= $k; ?>"><?= $v; ?></option>
                                <?php }
                                        } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="dashboard-filter__col">
                    <div class="dashboard-filter__form">
                        <div><label>Periode</label></div>
                        <div class="dashboard-filter__date">
                            <div class="input-group date" id="datestart">
                                <input type="text" class="form-control" />
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-filter__col">
                    <div class="dashboard-filter__form">
                        <div><label>Sampai</label></div>
                        <div class="dashboard-filter__date">
                            <div class="input-group date" id="dateend">
                                <input type="text" class="form-control" />
                                <span class="input-group-addon">
                                    <i class="glyphicon glyphicon-th"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-filter__col">
                    <button type="submit" class="btn btn-green">Terapkan</button>
                </div>
            </div>
        </form>
        <?php } ?>
    </div>

    <div class="row">
        <div class="col-widget">
            <?php $this->load->view('dashboard/penjualan_hari_ini_me', array('date_start' => $selected_datestart, 'date_end' => $selected_dateend)); ?>
        </div>
        <div class="col-widget ">
            <?php $this->load->view('dashboard/jumlah_transaksi_hari_ini_me'); ?>
        </div>
        <div class="col-widget ">
            <?php $this->load->view('dashboard/ratarata_transaksi_hari_ini_me'); ?>
        </div>
        <?php if ($visibilityMenu['LaporanLaba']) { ?>
        <div class="col-widget">
            <?php $this->load->view('dashboard/laba_kotor_hari_ini_me'); ?>
        </div>
        <?php } ?>
        <div class="col-widget ">
            <?php $this->load->view('dashboard/biaya_hari_ini_me'); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php $this->load->view('dashboard/penjualan_bulan_ini_me'); ?>
        </div>
    </div>

    <?php if ($isDiningTableVisible) { ?>
    <div class="row">
        <div class="col-md-12">
            <?php $this->load->view('dashboard/pengunjung_bulan_ini_me'); ?>
        </div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-6">
            <?php $this->load->view('dashboard/penjualan_terlaris_me'); ?>
        </div>
        <div class="col-md-6">
            <?php $this->load->view('dashboard/rekap_pembayaran_me'); ?>
        </div>
    </div>

    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
    <?php if (count($outlets) > 1) { ?>
    <div class="row">
        <div class="col-md-6">
            <?php $this->load->view('dashboard/outlet_terlaris_me'); ?>
        </div>
    </div>
    <?php } ?>
    <?php } ?>
    <?php } ?>
</div>