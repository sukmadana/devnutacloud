<style type="text/css">
    table.dataTable thead .sorting::after,
    table.dataTable thead .sorting_asc::after,
    table.dataTable thead .sorting_desc::after {
        right: 10% !important;
    }

    table.dataTable tbody td.no-padding {
        padding: 0 !important;
    }

    #listChoiceAdd td,
    #listChoiceEdit td {
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .has-success .input-group-addon {
        border-color: #d9d9d9 !important;
    }

    .has-error .input-group-addon {
        border-color: #FF0000 !important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>item/index">Items</a></li>
                <li class="active">Diskon</li>
            </ol>
        </div>
        <div class="col-md-6">
            <form class="form-horizontal">
                <div class="form-group row">
                    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                        <label class="control-label col-md-2">Outlet</label>
                        <div class="col-md-6 mb-5">
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
                            <a href="javascript:void(0)" class="px-20 btn <?php if ($visibilityMenu['ItemAdd'] && (intval($selected_outlet) > 0)) { ?> btn-primary btnAddKategori <?php } else { ?> btn-default <?php } ?>" <?php if ($visibilityMenu['ItemAdd'] && ((intval($selected_outlet) > 0))) { ?> data-toggle="modal" data-target="#modalAddDiscount" <?php } ?>>
                                Tambah Diskon
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
                            <h4 class="text-bolder line-height-2 pl-0 no-float text-block" style="margin-bottom: -10px">Daftar Diskon</h4>
                            <span class="font-11" id="totalData" style="line-height: 2.5">Total jumlah Diskon : <?= $totalDiscount ?></span>
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
                                            <th class="text-left" width="20%">Nama Diskon</th>
                                            <th class="text-left" width="65%">Nilai Diskon</th>
                                            <th class="text-left" width="15%"></th>
                                        </tr>
                                    </thead>
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
    <div id="modalAddDiscount" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium">
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
                    <h4 class="modal-title text-center text-bolder">Tambah Diskon</h4>
                </div>
                <form class="form-horizontal" id="formAddDiscount">
                    <input type="hidden" name="Percent" value="yes">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Nama Diskon</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="DiscountName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Diskon</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control no-border-right text-left" name="Discount" value="">
                                    <div class="input-group-addon py-4 background-white">
                                        <div class="switch-field mb-0">
                                            <input type="radio" id="percent-yes" name="r_percent" value="yes" />
                                            <label for="percent-yes" class="mb-0 mr-0" style="padding-top: 6px; padding-bottom: 6px">%</label>
                                            <input type="radio" id="percent-no" name="r_percent" value="no" />
                                            <label for="percent-no" class="mb-0 mr-0" style="padding-top: 6px; padding-bottom: 6px">Rp</label>
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
    <div id="modalEditDiscount" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium">
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
                    <h4 class="modal-title text-center text-bolder">Edit Diskon</h4>
                </div>
                <form class="form-horizontal" id="formEditDiscount">
                    <input type="hidden" name="DiscountID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <input type="hidden" name="Percent" value="">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Nama Diskon</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="DiscountName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Diskon</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input type="text" class="form-control no-border-right text-left" name="Discount" value="">
                                    <div class="input-group-addon py-4 background-white">
                                        <div class="switch-field mb-0">
                                            <input type="radio" id="percent-yes-edit" name="r_percent" value="yes" />
                                            <label for="percent-yes-edit" class="mb-0 mr-0" style="padding-top: 6px; padding-bottom: 6px">%</label>
                                            <input type="radio" id="percent-no-edit" name="r_percent" value="no" />
                                            <label for="percent-no-edit" class="mb-0 mr-0" style="padding-top: 6px; padding-bottom: 6px">Rp</label>
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
                    <h4 class="modal-title text-center text-bolder">Hapus Diskon</h4>
                </div>
                <div class="modal-body text-center p-25">
                    <input type="hidden" name="DiscountID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <p class="mt-10 text-muted">Apa anda yakin ingin menghapus diskon ini ? </p>
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
            Hapus User Berhasil
        </div>
        <div class="alert alert-danger text-center" role="alert" style="display: none">
            Hapus User Berhasil
        </div>
    </div>
<?php } ?>