<style type="text/css">
    table.dataTable thead .sorting::after,
    table.dataTable thead .sorting_asc::after,
    table.dataTable thead .sorting_desc::after {
        right: 10% !important;
    }

    table.dataTable tbody td.no-padding {
        padding: 0 !important;
    }

    .has-success .input-group-addon {
        border-color: #d9d9d9 !important;
    }

    .has-error .input-group-addon {
        border-color: #FF0000 !important;
    }

    .has-error .form-control,
    .has-error .bootstrap-select .dropdown-toggle {
        border-color: #FF0000 !important;
    }

    #accountOptionAdd .help-block,
    #accountOptionEdit .help-block {
        margin-top: 25px;
        text-align: left;
    }

    select.form-control.has-error,
    select.form-control.has-success,
    .has-error .bootstrap-select {
        border: none !important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>item/index">Items</a></li>
                <li class="active">Tipe Penjualan</li>
            </ol>
        </div>
        <div class="col-md-6">
            <form class="form-horizontal">
                <div class="form-group row">
                    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                        <label class="control-label col-md-2">Outlet</label>
                        <div class="col-md-5 mb-5">
                            <select class="form-control selectpicker show-menu-arrow show-tick" name="outlet" id="outlet" data-style="btn-white" style="width: 100%">
                                <?php
                                    if (count($outlets) > 1) { ?>
                                    <option value="-999" <?= $selected_outlet == -999 ? 'selected' : '' ?>>
                                        &nbsp;
                                    </option>
                                <?php }
                                    foreach ($outlets as $k => $v) { ?>
                                    <?php if ($k == $selected_outlet) { ?>
                                        <option value="<?= $k; ?>" selected=""><?= str_replace('#$%^', ' ', $v); ?></option>
                                    <?php } else { ?>
                                        <option value="<?= $k; ?>"><?= str_replace('#$%^', ' ', $v); ?></option>
                                <?php
                                        }
                                    }
                                    ?>
                            </select>
                        </div>
                        <div class="col-md-4 text-right">
                            <a href="javascript:void(0)" class="px-20 btn <?php if ($visibilityMenu['ItemAdd'] && (intval($selected_outlet) > 0)) { ?> btn-primary btnAddOpsiMakan <?php } else { ?> btn-default <?php } ?>" <?php if ($visibilityMenu['ItemAdd'] && ((intval($selected_outlet) > 0))) { ?> data-toggle="modal" data-target="#modalAddOpsiMakan" <?php } ?>>
                                Tambah Tipe Penjualan
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
    <?php if (intval($selected_outlet) > 0) { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box-widget widget-module">
                    <div class="widget-containner">
                        <div class="widget-head px-20 pt-5">
                            <h4 class="text-bolder line-height-2 pl-0 no-float text-block" style="margin-bottom: -10px">Daftar Tipe Penjualan</h4>
                            <span class="font-11" style="line-height: 2.5">Total jumlah Tipe Penjualan : <?= $totalOpsiMakan ?></span>
                        </div>
                        <div class="widget-block">
                            <div class="row">
                                <div class="col-md-12">
                                    <form class="form-inline pull-right">
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="searchBox" placeholder="Cari">
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-20 mb-20">
                                <table class="table table-borderless" id="grid-table">
                                    <thead>
                                        <tr>
                                            <th class="text-left" width="25%">Nama Tipe Penjualan</th>
                                            <th class="text-left" width="10%">Status</th>
                                            <th class="text-left" width="15%">Ojek Online</th>
                                            <th class="text-left" width="10%">Markup</th>
                                            <th class="text-left" width="25%">Pembulatan Markup</th>
                                            <th class="text-left" width="15%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rs_opsimakan as $i => $opsimakan) { ?>
                                            <tr>
                                                <td class="text-left"><?= $opsimakan['NamaOpsiMakan'] ?></td>
                                                <td class="text-left"><?= $opsimakan['IsActive'] == 1 ? 'Aktif' : 'Non Aktif' ?></td>
                                                <td class="text-left"><?= $opsimakan['OjekOnline'] == 1 ? 'Ya' : 'Tidak' ?></td>
                                                <td class="text-left"><?= format_number($opsimakan['MarkupPersen']) . '%' ?></td>
                                                <td class="text-left"><?= $opsimakan['MarkupRoundingRemark'] ?></td>
                                                <td class="text-right">
                                                    <div class="dropdown">
                                                        <?php if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemAdd'] || $visibilityMenu['ItemDelete']) { ?>
                                                            <a href="#" class="blue-sea dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                                            <ul class="dropdown-menu dropdown-menu-right">
                                                                <?php if ($visibilityMenu['ItemEdit']) { ?>
                                                                    <li>
                                                                        <a href=" javascript:void(0)" class="btnUpdateOpsiMakan py-10" data-opsimakan-id="<?= $opsimakan['OpsiMakanID'] ?>" data-device-no="<?= $opsimakan['DeviceNo'] ?>" data-opsimakan-name="<?= $opsimakan['NamaOpsiMakan'] ?>">Edit</a>
                                                                    </li>
                                                                <?php } ?>
                                                                <?php if ($visibilityMenu['ItemDelete']) { ?>
                                                                    <li class="divider my-0"></li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" class="btnDeleteOpsiMakan py-10" data-opsimakan-id="<?= $opsimakan['OpsiMakanID'] ?>" data-device-no="<?= $opsimakan['DeviceNo'] ?>" data-opsimakan-name="<?= $opsimakan['NamaOpsiMakan'] ?>">Hapus</a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        <?php } ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php if (intval($selected_outlet) > 0) { ?>
    <!-- Modal Tambah -->
    <div id="modalAddOpsiMakan" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium-3">
            <div class="modal-content">
                <div class="alert-fixed alert-fixed-danger" style="position: fixed; top: -70px; z-index: 999; display: none;">
                    <div class="alert alert-danger text-center" role="alert">
                    </div>
                </div>
                <div class="alert-fixed alert-fixed-success" style="position: fixed; top: -70px; z-index: 999; display: none;">
                    <div class="alert alert-success text-center" role="alert">
                    </div>
                </div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Tambah Tipe Penjualan</h4>
                </div>
                <form class="form-horizontal" id="formAddOpsiMakan">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Nama Tipe Penjualan</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="text" class="form-control" name="NamaOpsiMakan" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Ojek Online</label>
                            <div class="col-md-8 col-sm-8 col-xs-12 text-right">
                                <input type="hidden" name="OjekOnline" value="off">
                                <input type="checkbox" class="switch-small switch_access OjekOnline">
                                <div class="row mt-10" id="accountOptionAdd" style="display:none;">
                                    <label class="control-label col-md-4">Otomatis Masuk Ke Rekening</label>
                                    <div class="col-md-8">
                                        <select name="Account" class="form-control selectpicker show-menu-arrow show-tick" data-style="btn-white" style="width:100%;">
                                            <option value="" data-content="<div style='height: 40px'></div>"></option>
                                            <?php foreach ($rs_account as $account) { ?>
                                                <option value="<?= $account['AccountID'] . "#" . $account['DeviceNo'] ?>" data-content="<span class='text-bolder font-11'><?= $account['BankName'] ?></span><br><span class='font-11'><?= $account['AccountNumber'] ?></span><br><span class='text-bolder text-muted font-11'><?= $account['AccountName'] ?></span>">
                                                    <?= $account['BankName'] . " " . $account['AccountNumber'] . " " . $account['AccountName'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Share Revenue (Bagi Hasil)</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="text" class="form-control text-right" name="ShareRevenue" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Markup</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="text" class="form-control text-right" name="MarkupPersen" value="">
                                <p class="text-muted font-10">
                                    <i>
                                        Digunakan untuk me-mark up harga secara otomatis saat transaksi penjualan.<br>
                                        Misal harga normal Rp 15.000, mark up 20%, maka harga setelah mark up Rp 18.000
                                    </i>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Pembulatan Markup</label>
                            <div class="col-md-8 col-sm-8 col-xs-12 text-right">
                                <input type="hidden" name="MarkupRounding" value="off">
                                <input type="checkbox" class="switch-small switch_access MarkupRounding">
                                <div class="row mt-10" id="MarkupRoundingOptionAdd" style="display: none">
                                    <label class="col-md-4 col-sm-12 col-xs-12 control-label text-left">
                                        Bulatkan ke
                                    </label>
                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                        <div class="switch-field mb-0 btn-group">
                                            <input type="radio" id="rounding-100" name="MarkupRoundingValue" value="100" checked />
                                            <label for="rounding-100" class="mb-0 mr-0 py-10 btn btn-default btn-block">Rp 100</label>
                                            <input type="radio" id="rounding-500" name="MarkupRoundingValue" value="500" />
                                            <label for="rounding-500" class="mb-0 mr-0 py-10 btn btn-default btn-block">Rp 500</label>
                                        </div>
                                        <div class="radio text-left font-11">
                                            <label>
                                                <input type="radio" name="MarkupRoundingType" id="MarkupRoundingType0" value="0" style="margin-top:4px;" checked>
                                                <b>Terdekat</b><br>
                                                Misal : Rp 18.300 dibulatkan menjadi Rp 18.500<br>
                                                <span style="padding-left: 34px">Rp 18.200 dibulatkan menjadi Rp 18.000</span>

                                            </label>
                                        </div>
                                        <div class="radio text-left mt-20 font-11">
                                            <label>
                                                <input type="radio" name="MarkupRoundingType" id="MarkupRoundingType1" value="1" style="margin-top:4px;">
                                                <b>Ke Bawah</b><br>
                                                Misal : Rp 18.300 dibulatkan jadi Rp 18.000

                                            </label>
                                        </div>
                                        <div class="radio text-left mt-20 font-11">
                                            <label>
                                                <input type="radio" name="MarkupRoundingType" id="MarkupRoundingType2" value="2" style="margin-top:4px;">
                                                <b>Ke Atas</b><br>
                                                Misal : Rp 18.200 dibulatkan jadi Rp 18.500

                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-50 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" value="simpan" id="btnSimpan" class="btn btn-sm btn-success px-30 pull-right">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit -->
    <div id="modalUpdateOpsiMakan" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium-3">
            <div class="modal-content">
                <div class="alert-fixed alert-fixed-danger" style="position: fixed; top: -70px; z-index: 999; display: none;">
                    <div class="alert alert-danger text-center" role="alert">
                    </div>
                </div>
                <div class="alert-fixed alert-fixed-success" style="position: fixed; top: -70px; z-index: 999; display: none;">
                    <div class="alert alert-success text-center" role="alert">
                    </div>
                </div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Edit Tipe Penjualan</h4>
                </div>
                <form class="form-horizontal" id="formUpdateOpsiMakan">
                    <input type="hidden" name="OpsiMakanID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Nama Tipe Penjualan</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="text" class="form-control" name="NamaOpsiMakan" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Ojek Online</label>
                            <div class="col-md-8 col-sm-8 col-xs-12 text-right">
                                <input type="hidden" name="OjekOnline" value="off">
                                <input type="checkbox" class="switch-small switch_access OjekOnline">
                                <div class="row mt-10" id="accountOptionEdit" style="display:none;">
                                    <label class="control-label col-md-4">Otomatis Masuk Ke Rekening</label>
                                    <div class="col-md-8">
                                        <select name="Account" class="form-control selectpicker show-menu-arrow show-tick" data-style="btn-white" style="width:100%;">
                                            <option value="" data-content="<div style='height: 40px'></div>"></option>
                                            <?php foreach ($rs_account as $account) { ?>
                                                <option value="<?= $account['AccountID'] . "#" . $account['DeviceNo'] ?>" data-content="<span class='text-bolder font-11'><?= $account['BankName'] ?></span><br><span class='font-11'><?= $account['AccountNumber'] ?></span><br><span class='text-bolder text-muted font-11'><?= $account['AccountName'] ?></span>">
                                                    <?= $account['BankName'] . " " . $account['AccountNumber'] . " " . $account['AccountName'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Share Revenue (Bagi Hasil)</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="text" class="form-control text-right" name="ShareRevenue" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Markup</label>
                            <div class="col-md-8 col-sm-8 col-xs-12">
                                <input type="text" class="form-control text-right" name="MarkupPersen" value="">
                                <p class="text-muted font-10">
                                    <i>
                                        Digunakan untuk me-mark up harga secara otomatis saat transaksi penjualan.<br>
                                        Misal harga normal Rp 15.000, mark up 20%, maka harga setelah mark up Rp 18.000
                                    </i>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 col-sm-4 col-xs-12 control-label text-left">Pembulatan Markup</label>
                            <div class="col-md-8 col-sm-8 col-xs-12 text-right">
                                <input type="hidden" name="MarkupRounding" value="off">
                                <input type="checkbox" class="switch-small switch_access MarkupRounding">
                                <div class="row mt-10" id="MarkupRoundingOptionEdit" style="display: none">
                                    <label class="col-md-4 col-sm-12 col-xs-12 control-label text-left">
                                        Bulatkan ke
                                    </label>
                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                        <div class="switch-field mb-0 btn-group">
                                            <input type="radio" id="rounding-100" name="MarkupRoundingValue" value="100" checked />
                                            <label for="rounding-100" class="mb-0 mr-0 py-10 btn btn-default btn-block">Rp 100</label>
                                            <input type="radio" id="rounding-500" name="MarkupRoundingValue" value="500" />
                                            <label for="rounding-500" class="mb-0 mr-0 py-10 btn btn-default btn-block">Rp 500</label>
                                        </div>
                                        <div class="radio text-left font-11">
                                            <label>
                                                <input type="radio" name="MarkupRoundingType" id="MarkupRoundingType0" value="0" style="margin-top:4px;" checked>
                                                <b>Terdekat</b><br>
                                                Misal : Rp 18.300 dibulatkan menjadi Rp 18.500<br>
                                                <span style="padding-left: 34px">Rp 18.200 dibulatkan menjadi Rp 18.000</span>

                                            </label>
                                        </div>
                                        <div class="radio text-left mt-20 font-11">
                                            <label>
                                                <input type="radio" name="MarkupRoundingType" id="MarkupRoundingType1" value="1" style="margin-top:4px;">
                                                <b>Ke Bawah</b><br>
                                                Misal : Rp 18.300 dibulatkan jadi Rp 18.000

                                            </label>
                                        </div>
                                        <div class="radio text-left mt-20 font-11">
                                            <label>
                                                <input type="radio" name="MarkupRoundingType" id="MarkupRoundingType2" value="2" style="margin-top:4px;">
                                                <b>Ke Atas</b><br>
                                                Misal : Rp 18.200 dibulatkan jadi Rp 18.500

                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-50 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" value="simpan" id="btnSimpan" class="btn btn-sm btn-success px-30 pull-right">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Hapus -->
    <div id="modalHapus" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Hapus Tipe Penjualan</h4>
                </div>
                <div class="modal-body text-center p-25">
                    <input type="hidden" name="OpsiMakanID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <p class="mt-10 text-muted">Apa anda yakin ingin menghapus tipe penjualan ini ? </p>
                </div>
                <div class="modal-footer pb-15">
                    <button type="submit" id="btnDelete" class="btn btn-sm btn-success px-20">Yakin</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Alert Hapus dan Edit -->
    <div class="alert-fixed" id="alert-update-delete" style="position: fixed; top: 30px; z-index: 1100; display: none;">
        <div class="alert alert-success text-center" role="alert" style="display: none">

        </div>
        <div class="alert alert-danger text-center" role="alert" style="display: none">

        </div>
    </div>
<?php } ?>