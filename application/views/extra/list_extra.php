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

    #table-terapkan-produk tbody tr td:first-child {
        width: 50px;

    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>item/index">Items</a></li>
                <li class="active">Pilihan Ekstra</li>
            </ol>
        </div>
        <div class="col-md-7">
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
                            <a href="javascript:void(0)" class="px-20 btn <?php if ($visibilityMenu['ItemAdd'] && (intval($selected_outlet) > 0)) { ?> btn-primary btnAddKategori <?php } else { ?> btn-default <?php } ?>" <?php if ($visibilityMenu['ItemAdd'] && ((intval($selected_outlet) > 0))) { ?> data-toggle="modal" data-target="#modalAddModifier" <?php } ?>>
                                Tambah Pilihan Ekstra
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
                            <h4 class="text-bolder line-height-2 pl-0 no-float text-block" style="margin-bottom: -10px">Daftar Pilihan Ekstra</h4>
                            <span class="font-11" id="totalData" style="line-height: 2.5">Total jumlah Pilihan Ekstra : <?= count($rs_modifier) ?></span>
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
                                            <th class="text-left" width="1%"></th>
                                            <th class="text-left" width="20%">Pilihan Ekstra</th>
                                            <th class="text-left" width="64%">Detail Pilihan ekstra</th>
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
    <!-- Modal Tambah Modifier -->
    <div id="modalAddModifier" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium-2">
            <div class="modal-content" style="margin-bottom: 10px; margin-top: 60px">
                <div class="alert-fixed alert-fixed-danger" style="position: fixed; top: -56px; z-index: 999; display: none;">
                    <div class="alert alert-danger text-center" role="alert">
                    </div>
                </div>
                <div class="alert-fixed alert-fixed-success" style="position: fixed; top: -56px; z-index: 999; display: none;">
                    <div class="alert alert-success text-center" role="alert">
                    </div>
                </div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Tambah Pilihan Ekstra</h4>
                </div>
                <form class="form-horizontal" id="formAddModifier">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="ModifierName" value="" placeholder="Misal : Toping">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="form-control row" style="height: auto; margin-left: 0px;">
                                    <div class="col-md-9 col-xs-9 pl-0">
                                        Pelanggan hanya bisa pilih satu Pilihan Ekstra
                                    </div>
                                    <div class="col-md-3 col-xs-3 text-right">
                                        <input type="checkbox" class="switch-small switch_access ChooseOneOnly">
                                        <input type="hidden" name="ChooseOneOnly" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p class="pl-15">
                                    <small class="text-muted font-italic font-11">Nyalakan pengaturan ini agar pilihan ekstra ini hanya bisa dipilih pada satu</small>
                                </p>
                            </div>
                        </div>
                        <?php if ($Options->StockModifier == 1) : ?>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="form-control row" style="height: auto; margin-left: 0px;">
                                        <div class="col-md-9 col-xs-9 pl-0">
                                            Pelanggan bisa menambah jumlah per pilihan
                                        </div>
                                        <div class="col-md-3 col-xs-3 text-right">
                                            <input type="checkbox" class="switch-small switch_access CanAddQuantity">
                                            <input type="hidden" name="CanAddQuantity" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="table-wrapper-sv" style="max-height: 200px; width:100%">
                                    <table id="listChoiceAdd" cellspacing="0" style="width:100%">
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <div class="col-name col-md-7 col-sm-7 col-xs-12 mb-5">
                                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[1]" value="" placeholder="Misal : Keju">
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:none">
                                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[1]" value="" placeholder="Qty">
                                                    </div>
                                                    <div class="col-price col-md-4 col-sm-4 col-xs-5">
                                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[1]" value="" placeholder="Rp">
                                                    </div>
                                                    <div class="col-md-1 col-sm-1 col-xs-2 text-left pl-0">
                                                        <a href="#" class="text-muted btnRemoveChoice">
                                                            <i class="fa fa-times-circle fa-2x"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <div class="col-name col-md-7 col-sm-7 col-xs-12 mb-5">
                                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[2]" value="" placeholder="Misal : Keju">
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:none">
                                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[2]" value="" placeholder="Qty">
                                                    </div>
                                                    <div class="col-price col-md-4 col-sm-4 col-xs-5">
                                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[2]" value="" placeholder="Rp">
                                                    </div>
                                                    <div class="col-md-1 col-sm-1 col-xs-2 text-center text-left pl-0">
                                                        <a href="#" class="text-muted btnRemoveChoice">
                                                            <i class="fa fa-times-circle fa-2x"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="row">
                                                    <div class="col-name col-md-7 col-sm-7 col-xs-12 mb-5">
                                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[3]" value="" placeholder="Misal : Keju">
                                                    </div>
                                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:none">
                                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[3]" value="" placeholder="Qty">
                                                    </div>
                                                    <div class="col-price col-md-4 col-sm-4 col-xs-5">
                                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[3]" value="" placeholder="Rp">
                                                    </div>
                                                    <div class="col-md-1 col-sm-1 col-xs-2 text-center text-left pl-0">
                                                        <a href="#" class="text-muted btnRemoveChoice">
                                                            <i class="fa fa-times-circle fa-2x"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="display:none;">
                            <div class="col-md-11 pr-0">
                            </div>
                            <div class="col-md-1 text-center mt-5">
                                <a href="javascript:void(0)" id="btnAddChoice">
                                    <i class="fa fa-plus-circle fa-2x"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-50 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" value="simpan" id="btnSimpan" class="btn btn-sm btn-success px-30 pull-right">Ok</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Modifier -->
    <div id="modalEditModifier" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium-2">
            <div class="modal-content" style="margin-bottom: 10px; margin-top: 60px">
                <div class="alert-fixed alert-fixed-danger" style="position: fixed; top: -56px; z-index: 999; display: none;">
                    <div class="alert alert-danger text-center" role="alert">
                    </div>
                </div>
                <div class="alert-fixed alert-fixed-success" style="position: fixed; top: -56px; z-index: 999; display: none;">
                    <div class="alert alert-success text-center" role="alert">
                    </div>
                </div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Edit Pilihan Ekstra</h4>
                </div>
                <form class="form-horizontal" id="formEditModifier">
                    <input type="hidden" name="ModifierID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <div class="col-md-12">
                                <input type="text" class="form-control" name="ModifierName" value="" placeholder="Misal : Toping">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="form-control row" style="height: auto; margin-left: 0px;">
                                    <div class="col-md-9 col-xs-9 pl-0">
                                        Pelanggan hanya bisa pilih satu Pilihan Ekstra
                                    </div>
                                    <div class="col-md-3 col-xs-3 text-right">
                                        <input type="checkbox" class="switch-small switch_access ChooseOneOnly">
                                        <input type="hidden" name="ChooseOneOnly" value="0">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <p class="pl-15">
                                    <small class="text-muted font-italic font-11">Nyalakan pengaturan ini agar pilihan ekstra ini hanya bisa dipilih pada satu</small>
                                </p>
                            </div>
                        </div>
                        <?php if ($Options->StockModifier == 1) : ?>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="form-control row" style="height: auto; margin-left: 0px;">
                                        <div class="col-md-9 col-xs-9 pl-0">
                                            Pelanggan bisa menambah jumlah per pilihan
                                        </div>
                                        <div class="col-md-3 col-xs-3 text-right">
                                            <input type="checkbox" class="switch-small switch_access CanAddQuantity">
                                            <input type="hidden" name="CanAddQuantity" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="table-wrapper-sv" style="max-height: 200px; width:100%">
                                    <table id="listChoiceEdit" cellspacing="0" style="width:100%">
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="form-group" style="display:none;">
                            <div class="col-md-11 pr-0">
                            </div>
                            <div class="col-md-1 text-center mt-5">
                                <a href="javascript:void(0)" id="btnAddChoiceEdit">
                                    <i class="fa fa-plus-circle fa-2x"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-50 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" data-modifier-id="" data-device-no="" data-modifier-name="" class="btn btn-sm btn-white text-danger px-30 pull-left btnDeleteModifier">Hapus</button>
                                <button type="button" value="simpan" id="btnSimpan" class="btn btn-sm btn-success px-30 pull-right">Ok</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Terapkan Produk -->
    <div id="modalTerapkanProduk" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium-2">
            <div class="modal-content">
                <div class="alert-fixed alert-produk alert-fixed-danger" style="position: fixed; top: -70px; z-index: 999; display: none;">
                    <div class="alert alert-danger text-center" role="alert">
                    </div>
                </div>
                <div class="alert-fixed alert-produk alert-fixed-success" style="position: fixed; top: -70px; z-index: 999; display: none;">
                    <div class="alert alert-success text-center" role="alert">
                    </div>
                </div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Terapkan Pilihan Ekstra ini Pada Produk</h4>
                </div>
                <form class="form-horizontal" id="formTerapkanProduk">
                    <input type="hidden" id="ModifierID" name="ModifierID" value="">
                    <input type="hidden" id="DeviceNo" name="DeviceNo" value="">
                    <div class="modal-body p-0" style="background-color: #FFF2E2;">
                        <p class="text-center p-15 mb-0" style="line-height: 1.4;">
                            Pilihan Ekstra ini (<span class="ModifierName"></span>) akan dterapkan pada <strong><span id="countTerapkanProduk">0</span> produk</strong>
                        </p>
                    </div>
                    <div modal-body p-0>
                        <div class="input-group">
                            <div class="input-group-addon background-white pr-0">
                                <span class="fa fa-search"></span>
                            </div>
                            <input type="text" class="form-control no-border-left pl-5" id="searchBoxItems" placeholder="Cari Produk">
                        </div>

                    </div>
                    <div class="modal-body p-0">
                        <p class="text-center my-25" id="loadingProduk">
                            <i class="fa fa-spinner fa-spin mr-5"></i> Memuat data produk
                        </p>
                        <table class="table table-borderless" id="table-terapkan-produk" style="display: none;">
                            <thead style="display: none;">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer p-25 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" id="btnSimpanProduk" class="btn btn-sm btn-success px-20 pull-right">Terapkan pada Produk</button>
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
                    <h4 class="modal-title text-center text-bolder">Hapus Pilihan Ekstra</h4>
                </div>
                <div class="modal-body text-center p-25">
                    <input type="hidden" name="ModifierID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <p class="mt-10 text-muted">Apa anda yakin ingin menghapus <span id="delete-modifier"></span> secara permanen ? </p>
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