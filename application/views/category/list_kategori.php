<style type="text/css">
    table.dataTable thead .sorting::after,
    table.dataTable thead .sorting_asc::after,
    table.dataTable thead .sorting_desc::after {
        right: 90% !important;
    }

    table.dataTable tbody td.no-padding {
        padding: 0 !important;
    }

    #table-move-produk tbody tr td:first-child {
        width: 50px !important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>item/index">Items</a></li>
                <li class="active">Kategori</li>
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
                            <a href="javascript:void(0)" class="px-20 btn <?php if ($visibilityMenu['ItemAdd'] && (intval($selected_outlet) > 0)) { ?> btn-primary btnAddKategori <?php } else { ?> btn-default <?php } ?>" <?php if ($visibilityMenu['ItemAdd'] && ((intval($selected_outlet) > 0))) { ?> data-toggle="modal" data-target="#modalAddKategori" <?php } ?>>
                                Tambah Kategori
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
                            <h4 class="text-bolder line-height-2 pl-0 no-float text-block" style="margin-bottom: -10px">Kategori</h4>
                            <span class="font-11" style="line-height: 2.5" K>Total jumlah Kategori : <?= count($rs_kategori) ?></span>
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
                                            <th class="text-left" width="84%">Kategori</th>
                                            <th class="text-left" width="15%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rs_kategori as $kategori) { ?>
                                            <tr>
                                                <td>
                                                    <?php if ($kategori['totalItems'] > 0) { ?>
                                                        <a href="javascript:void(0)" data-category-id="<?= $kategori['CategoryID'] ?>" data-device-no="<?= $kategori['DeviceNo'] ?>" class="blue-sea detail-items icon">
                                                            <i class="fa fa-chevron-right"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                <td><a href="javascript:void(0)" data-category-id="<?= $kategori['CategoryID'] ?>" data-device-no="<?= $kategori['DeviceNo'] ?>" class="blue-sea detail-items"><?= $kategori['CategoryName'] ?></a></td>
                                                <td class="text-right">
                                                    <div class="dropdown">
                                                        <?php if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemAdd'] || $visibilityMenu['ItemDelete']) { ?>
                                                            <a href="#" class="blue-sea dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                                            <ul class="dropdown-menu dropdown-menu-right">
                                                                <?php if ($visibilityMenu['ItemEdit']) { ?>
                                                                    <li>
                                                                        <a href=" javascript:void(0)" class="btnUpdateKategori py-10" data-category-id="<?= $kategori['CategoryID'] ?>" data-device-no="<?= $kategori['DeviceNo'] ?>" data-category-name="<?= $kategori['CategoryName'] ?>">Edit Kategori</a>
                                                                    </li>
                                                                <?php } ?>
                                                                <?php if ($visibilityMenu['ItemAdd'] || $visibilityMenu['ItemEdit']) { ?>
                                                                    <li class="divider my-0"></li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" class="btnMoveProduk py-10" data-category-id="<?= $kategori['CategoryID'] ?>" data-device-no="<?= $kategori['DeviceNo'] ?>" data-category-name="<?= $kategori['CategoryName'] ?>">Tambahkan Produk</a>
                                                                    </li>
                                                                <?php } ?>
                                                                <?php if ($visibilityMenu['ItemDelete']) { ?>
                                                                    <li class="divider my-0"></li>
                                                                    <li>
                                                                        <a href="javascript:void(0)" class="btnDeleteCategory py-10" data-category-id="<?= $kategori['CategoryID'] ?>" data-device-no="<?= $kategori['DeviceNo'] ?>" data-category-name="<?= $kategori['CategoryName'] ?>">Hapus</a>
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
    <!-- Modal Tambah Kategori -->
    <div id="modalAddKategori" class="modal fade">
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
                    <h4 class="modal-title text-center">Tambah Kategori</h4>
                </div>
                <form class="form-horizontal" id="formAddCategory">
                    <div class="modal-body p-25">
                        <div class="form-group">
                            <label class="col-md-4 control-label pt-7 text-left">Nama Kategori</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="CategoryName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4"></label>
                            <div class="col-md-8">
                                <?php if ($options->PrintToBar == 1 || $options->PrintToKitchen == 1) { ?>
                                    <span class="text-sm">Kategori di atas ingin diprint kemana ?</span>
                                <?php } ?>
                                <?php if ($options->PrintToKitchen == 1) { ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="IPPrinter" id="IPPrinterDapur" value="Dapur">
                                            Dapur
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php if ($options->PrintToBar == 1) { ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="IPPrinter" id="IPPrinterBar" value="Bar">
                                            Bar
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php if ($options->PrintToBar == 1 || $options->PrintToKitchen == 1) { ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="IPPrinter" id="IPPrinterNone" value="TidakCetak">
                                            Tidak Cetak
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-25 pb-15">
                        <div class="form-group">
                            <div class="col-md-6 col-sm-6 col-xs-12 text-left mb-5">
                                <button type="button" value="simpanAddProduk" class="btnSimpan btn btn-sm btn-success-outline px-10 btn-block">Simpan dan Tambahkan Produk</button>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-12 text-right mb-5 pl-0">
                                <button type="button" value="simpan" class="btnSimpan btn btn-sm btn-success px-10">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Pindahkan / Tambah Produk -->
    <div id="modalMoveProduk" class="modal fade">
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
                    <h4 class="modal-title text-center">Tambahkan Produk Pada Kategori <span class="CategoryName"></span></h4>
                </div>
                <form class="form-horizontal" id="formMoveProduk">
                    <input type="hidden" id="newCategoryID" name="newCategoryID" value="">
                    <input type="hidden" id="newCategoryDeviceNo" name="newCategoryDeviceNo" value="">
                    <div class="modal-body p-0" style="background-color: #FFF2E2;">
                        <p class="text-center p-15 mb-0" style="line-height: 1.4;">
                            Item yang saat ini dalam kategori lain akan dipindahkan ke ketegori ini.<br>
                            <strong><span id="countMoveProduk">0</span> produk</strong> akan dipindahkan ke kategori <span class="CategoryName"></span>
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
                        <table class="table table-borderless" id="table-move-produk" style="max-width:583px !important">
                            <thead style="display: none;">
                                <tr>
                                    <td width="50px"></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer p-25 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" id="btnSimpanProduk" class="btn btn-sm btn-success px-20 pull-right">Pindakan Produk</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Edit Nama Kategori -->
    <div id="modalUpdateKategori" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Edit Nama Kategori</h4>
                </div>
                <form class="form-horizontal" id="formUpdateKategori">
                    <input type="hidden" name="CategoryID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <div class="modal-body p-25 pb-0">
                        <div class="form-group">
                            <label class="col-md-4 control-label pt-7 text-left">Nama Kategori</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="CategoryName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4"></label>
                            <div class="col-md-8">
                                <?php if ($options->PrintToBar == 1 || $options->PrintToKitchen == 1) { ?>
                                    <span class="text-sm">Kategori di atas ingin diprint kemana ?</span>
                                <?php } ?>
                                <?php if ($options->PrintToKitchen == 1) { ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="IPPrinter" id="IPPrinterDapur" value="Dapur">
                                            Dapur
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php if ($options->PrintToBar == 1) { ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="IPPrinter" id="IPPrinterBar" value="Bar">
                                            Bar
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php if ($options->PrintToBar == 1 || $options->PrintToKitchen == 1) { ?>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="IPPrinter" id="IPPrinterNone" value="TidakCetak">
                                            Tidak Cetak
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer p-25 pt-10 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" id="btnUpdateKategori" class="btn btn-sm btn-success px-20 pull-right">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal Hapus Kategori -->
    <div id="modalHapus" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center text-bolder">Hapus Nama Kategori</h4>
                </div>
                <div class="modal-body text-center p-25">
                    <input type="hidden" name="CategoryID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <p class="mt-10 text-muted">Apa anda yakin ingin menghapus <span id="delete-kategori"></span> secara permanen ? </p>
                </div>
                <div class="modal-footer pb-15">
                    <button type="submit" id="btnDelete" class="btn btn-sm btn-success px-20">Yakin</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Alert Hapud dan Edit Kategori -->
    <div class="alert-fixed" id="alert-update-delete" style="position: fixed; top: 30px; z-index: 1100; display: none;">
        <div class="alert alert-success text-center" role="alert" style="display: none">
            Hapus User Berhasil
        </div>
        <div class="alert alert-danger text-center" role="alert" style="display: none">
            Hapus User Berhasil
        </div>
    </div>
<?php } ?>