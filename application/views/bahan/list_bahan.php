<style type="text/css">
    table.dataTable thead .sorting::after,
    table.dataTable thead .sorting_asc::after,
    table.dataTable thead .sorting_desc::after {
        /* left: 33% !important; */
    }

    table.dataTable tbody td.no-padding {
        padding: 0 !important;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>item/index">Items</a></li>
                <li class="active">Bahan</li>
            </ol>
        </div>
        <div class="col-md-6">
            <form class="form-horizontal">
                <div class="form-group row mr-0">
                    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                        <label class="control-label col-md-2">Outlet</label>
                        <div class="col-md-7 mb-5">
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
                        <div class="col-md-3 text-right">
                            <a href="javascript:void(0)" class="btn <?php if ($visibilityMenu['ItemAdd'] && (intval($selected_outlet) > 0)) { ?> btn-primary btnAddKategori <?php } else { ?> btn-default <?php } ?>" <?php if ($visibilityMenu['ItemAdd'] && ((intval($selected_outlet) > 0))) { ?> data-toggle="modal" data-target="#modalAddBahan" <?php } ?>>
                                Tambah Bahan
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
                            <h4 class="text-bolder line-height-2 pl-0 no-float text-block" style="margin-bottom: -10px">Daftar Bahan</h4>
                            <span class="font-11" id="totalData" style="line-height: 2.5">Total jumlah Bahan : <?= $totalBahan ?></span>
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
                                            <th class="text-left">Bahan</th>
                                            <th class="text-left">Kategori</th>
                                            <th class="text-left">Satuan</th>
                                            <th class="text-left">Harga Beli</th>
                                            <th class="text-left"></th>
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
    <!-- Modal tambah bahan -->
    <div id="modalAddBahan" class="modal fade">
        <div class="modal-dialog modal-confirm modal-medium-2">
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
                    <h4 class="modal-title text-center text-bolder">Tambah Bahan</h4>
                </div>
                <form class="form-horizontal" id="formAddBahan">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Nama Bahan</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="ItemName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Kategori</label>
                            <!-- <div class="col-md-8" style="padding-left: 20px; padding-right: 10px">
                            <select name="CategoryID" class="form-control selectpicker show-menu-arrow" data-style="btn-white" placeholder="Pilih Kategori">
                                <option value=""></option>
                                <?php
                                    // foreach ($rs_kategori as $kategori) {
                                    //     echo '<option value="' . $kategori['CategoryID'] . '">' . $kategori['CategoryName'] . '</option>';
                                    // }
                                    ?>
                            </select>
                        </div> -->
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="CategoryName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Satuan</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="Unit" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Harga Beli</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PurchasePrice" value="">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-50 pb-15">
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" value="simpan" class="btnSimpan btn btn-sm btn-success-outline px-20 pull-left">Simpan</button>
                                <button type="button" value="simpanTambah" class="btnSimpan btn btn-sm btn-success px-20 pull-right">Simpan dan Tambah</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal edit bahan -->
    <div id="modalEditBahan" class="modal fade">
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
                    <h4 class="modal-title text-center text-bolder">Edit Bahan</h4>
                </div>
                <form class="form-horizontal" id="formEditBahan">
                    <input type="hidden" name="ItemID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <div class="modal-body px-50">
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Nama Bahan</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="ItemName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Kategori</label>
                            <!-- <div class="col-md-8" style="padding-left: 20px; padding-right: 10px">
                            <select name="CategoryID" class="form-control selectpicker show-menu-arrow" data-style="btn-white" placeholder="Pilih Kategori">
                                <option value=""></option>
                                <?php
                                    // foreach ($rs_kategori as $kategori) {
                                    //     echo '<option value="' . $kategori['CategoryID'] . '">' . $kategori['CategoryName'] . '</option>';
                                    // }
                                    ?>
                            </select>
                        </div> -->
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="CategoryName" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Satuan</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="Unit" value="">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-4 control-label text-left">Harga Beli</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <div class="input-group-addon py-4 pr-0 background-white">
                                        Rp
                                    </div>
                                    <input type="text" class="form-control no-border-left pl-5" name="PurchasePrice" value="">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-15">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <button type="button" value="simpanTambah" class="btnSimpan btn btn-sm btn-success px-20 pull-right">Simpan Perubahan</button>
                                </div>
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
                    <h4 class="modal-title text-center">Hapus Bahan</h4>
                </div>
                <div class="modal-body text-center p-25">
                    <input type="hidden" name="ItemID" value="">
                    <input type="hidden" name="DeviceNo" value="">
                    <p class="mt-10 text-muted">Apa anda yakin ingin menghapus bahan ini ? </p>
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
