<style type="text/css">


table.dataTable thead .sorting::after,
table.dataTable thead .sorting_asc::after,
table.dataTable thead .sorting_desc::after {
    right: 90% !important;
}

table.dataTable tbody td.no-padding {
    padding: 0 !important;
}

* {
    box-sizing: border-box;
}

#filter {
    background-image: url('/css/searchicon.png');
    background-position: 10px 12px;
    background-repeat: no-repeat;
    width: 100%;
    font-size: 16px;
    padding: 12px 20px 12px 40px;
    border: 1px solid #ddd;
}

#filterEdit {
    background-image: url('/css/searchicon.png');
    background-position: 10px 12px;
    background-repeat: no-repeat;
    width: 100%;
    font-size: 16px;
    padding: 12px 20px 12px 40px;
    border: 1px solid #ddd;
}

#myUL {
    list-style-type: none;
    padding-left: 0px;
}

.item-group {
    border: 1px solid #ddd;
    margin-top: -1px;
    /* Prevent double borders */
    background-color: #fff;
    padding: 12px 50px;
    text-decoration: none;
    color: black;
    display: block
}

.category-group {
    border: 1px solid #ddd;
    margin-left: 30px !important;
}

.item-group:hover:not(.header) {
    background-color: #eee;
}

#myUL ul {
    list-style: none;
    padding-left: 0px;
}

.modal-body {
    max-height: calc(100vh - 212px);
    overflow-y: auto;
}

.tax-percent {
    border-right: 0px;
}

.tax-percent-addon {
    background-color: #fff;
}

.category-name-label {
    padding-bottom: 12px;
    padding-top: 12px;
}

.categoryItemGroup{
    padding: 0px 0px 0px 0px !important;
    max-height: 200px;
    overflow-x: hidden;
}
table.dataTable thead .sorting::after, table.dataTable thead .sorting_asc::after, table.dataTable thead .sorting_desc::after {
    right: 10% !important;
}
.modal {
    overflow-y: auto;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>item/index">Produk</a></li>
                <li class="active">Pajak</li>
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
                            <a href="javascript:void(0)" class="px-20 btn <?php if ($visibilityMenu['ItemAdd'] and (intval($selected_outlet) > 0 ) ) { ?> btn-primary btnAddPajak <?php } else { ?> btn-default <?php } ?>" <?php if ($visibilityMenu['ItemAdd'] and (intval($selected_outlet) > 0 ) ) { ?> data-toggle="modal" data-target="#modalAddTax" <?php } ?>>
                            Tambah Pajak
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-containner">
                    <div class="widget-head px-20 pt-5">
                        <h4 class="text-bolder line-height-2 pl-0 no-float text-block" style="margin-bottom: -10px">Daftar Pajak</h4>
                        <span class="font-11">Total jumlah Pajak : <?= count($pajak) ?></span>
                    </div>
                    <div class="widget-block">
                        <table class="table" id="tax-table">
                            <thead>
                                <tr>
                                    <th>Nama Pajak</th>
                                    <th>Nilai Pajak</th>
                                    <th>Harga Jual</th>
                                    <th>Semua Produk Kena Pajak</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pajak as $tax) { ?>
                                <tr>
                                    <td>
                                        <?= $tax['TaxName'] ?>
                                    </td>
                                    <td>
                                        <?= $tax['TaxPercent'] ?> %
                                    </td>
                                    <td>
                                        <?= ($tax['PriceIncludeTax'] == 0) ? "Belum Termasuk Pajak" : "Sudah Termasuk Pajak";?>
                                    </td>
                                    <td>
                                        <?= ($tax['ApplyToAllItems'] == 0) ? "Tidak" : "Ya";?>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <?php if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemDelete']) { ?>
                                            <a href="#" class="blue-sea dropdown-toggle" data-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                            <ul class="dropdown-menu py-0" style="left: -30px">
                                                <?php if ($visibilityMenu['ItemEdit']) { ?>
                                                <li>
                                                    <a href="javascript:void(0)" class="btnUpdatePajak py-10" data-tax-id="<?= $tax['TaxID'] ?>" data-tax-name="<?= $tax['TaxName'] ?>" data-tax-percent="<?= $tax['TaxPercent'] ?>" data-price-include-tax="<?= $tax['PriceIncludeTax'] ?>" data-apply-to-all-items="<?= $tax['ApplyToAllItems'] ?>" data-applicable-categories="<?= $tax['ApplicableCategories'] ?>" data-applicable-items="<?= $tax['ApplicableItems'] ?>" data-toggle="modal" data-target="#modalEditTax">Edit</a>
                                                </li>
                                                <?php } ?>
                                                <?php if ($visibilityMenu['ItemDelete']) { ?>
                                                <li class="divider my-0"></li>
                                                <li>
                                                    <a href="javascript:void(0)" onclick="showDeleteModal(this);" class="btnDeleteTax py-10" data-tax-id="<?= $tax['TaxID'] ?>" data-tax-name="<?= $tax['TaxName'] ?>" >Hapus</a>
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
<!-- Modal Tambah Pajak -->
<div id="modalAddTax" class="modal fade">
    <div class="modal-dialog modal-medium modal-confirm">
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
                <h4 class="modal-title text-center"><strong>Tambah Pajak</strong></h4>
            </div>
            <form class="form-horizontal" id="formAddTax">
                <div class="modal-body p-25">
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Nama Pajak</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="TaxName" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Persen Pajak</label>
                        <div class="col-md-8">
                            <div class="input-group">								
                                <input type="text" class="pull-right form-control tax-percent" name="TaxPercent" value="">
                                <span class="input-group-addon tax-percent-addon" id="basic-addon2">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Harga Jual</label>
                        <div class="col-md-8">
                            <div class="radio">
                                <label><input type="radio" value="0" name="PriceIncludeTax">Harga Jual Belum Termasuk Pajak</label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" value="1" name="PriceIncludeTax">Harga Jual Sudah Termasuk Pajak</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Semua Produk Kena Pajak?</label>
                        <div class="col-md-8 control-label">
                            <input type="checkbox" checked="checked" id="ApplyToAllItems" class="switch-small">
                            <input type="hidden" id="ApplyToAllItemsVal" name="ApplyToAllItems">
                        </div>
                    </div>
                </div>
                <div class="modal-body categoryItemGroup" id="categoryItemGroup">
                    <input type="text" id="filter" placeholder="Cari Produk.." title="Type in a name">
                    <div class="form-group">
                        <ul id="myUL">
                            <?php foreach ($kategori as $key=>$value): ?>
                            <li>
                                <input class="category-group" type="checkbox" name="ApplicableCategories[]" value="<?=intval($value['DeviceID'])?>.<?=$value['CategoryID']?>">
                                <label class="control-label category-name-label" for="<?=$value['CategoryID']?>"><?=$value['CategoryName']?></label>
                                <?php foreach ($item as $item_key=>$item_value): ?>
                                <?php if ($item_value->CategoryID == $value['CategoryID']): ?>
                                <ul>
                                    <li class="item-group">
                                        <input type="checkbox" name="ApplicableItems[]" value="<?=intval($item_value->DeviceID)?>.<?=$item_value->ItemID?>">
                                        <label for="tall-1"><?=$item_value->ItemName?></label>
                                    </li>
                                </ul>
                                <?php endif ;?>
                                <?php endforeach ;?>
                            </li>
                            <?php endforeach ;?>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer p-25 pb-15">
                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="button" value="simpan" id="btnSimpan" class="btnSimpan btn btn-sm btn-success px-20 pull-right">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Pajak -->
<div id="modalEditTax" class="modal fade">
    <div class="modal-dialog modal-medium modal-confirm">
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
                <h4 class="modal-title text-center"><strong>Edit Pajak</strong></h4>
            </div>
            <form class="form-horizontal" id="formEditTax">
                <div class="modal-body p-25">
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Nama Pajak</label>
                        <div class="col-md-8">
                            <input type="hidden" class="form-control" name="TaxID" value="">
                            <input type="hidden" id="ApplyToAllItemsValEdit" name="ApplyToAllItems" value="off">
                            <input type="text" class="form-control" name="TaxName" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Persen Pajak</label>
                        <div class="col-md-8">
                            <div class="input-group">								
                                <input type="text" class="form-control tax-percent pull-right" name="TaxPercent" value="">
                                <span class="input-group-addon tax-percent-addon" id="basic-addon2">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Harga Jual</label>
                        <div class="col-md-8">
                            <div class="radio">
                                <label>
                                	<input type="radio" id="PriceIncludeTax0" value="0" name="PriceIncludeTax"/>Harga Jual Belum Termasuk Pajak
                                </label>
                            </div>
                            <div class="radio">
                                <label><input type="radio" id="PriceIncludeTax1" value="1" name="PriceIncludeTax">Harga Jual Sudah Termasuk Pajak</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label pt-7 text-left">Semua Produk Kena Pajak?</label>
                        <div class="col-md-8 control-label">
                            <input type="checkbox" id="ApplyToAllItemsEdit" >
                        </div>
                    </div>
                </div>
                <div id="categoryItemGroupEdit" class="categoryItemGroup modal-body">
                    <input type="text" id="filterEdit" placeholder="Cari Produk.." title="Type in a name">
                    <div class="form-group">
                        <div class="col-md-12">
                            <ul id="myUL">
                                <?php foreach ($kategori as $key=>$value): ?>
                                <li>
                                    <input class="category-group" id="category-<?=$value['CategoryID']?>" type="checkbox" name="ApplicableCategories[]" value="<?=intval($value['DeviceID'])?>.<?=$value['CategoryID']?>">
                                    <label class="control-label category-name-label" for="<?=$value['CategoryID']?>"><?=$value['CategoryName']?></label>
                                    <?php foreach ($item as $item_key=>$item_value): ?>
                                    <?php if ($item_value->CategoryID == $value['CategoryID']): ?>
                                    <ul>
                                        <li class="item-group">
                                            <input type="checkbox" id="item-<?=$item_value->ItemID?>" name="ApplicableItems[]" value="<?=intval($item_value->DeviceID)?>.<?=$item_value->ItemID?>">
                                            <label for="tall-1"><?=$item_value->ItemName?></label>
                                        </li>
                                    </ul>
                                    <?php endif ;?>
                                    <?php endforeach ;?>
                                </li>
                                <?php endforeach ;?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-25 pb-15">
                    <div class="form-group">
                        <div class="col-md-12">
                            <button type="button" value="simpan" id="btnSimpanEdit" class="btnSimpan btn btn-sm btn-success px-20 pull-right">Simpan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Pajak -->
<div id="modalHapus" class="modal fade">
    <div class="modal-dialog modal-confirm modal-medium">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center"><strong>Hapus Pajak</strong></h4>
            </div>
            <div class="modal-body text-center p-25">
                <input type="hidden" name="TaxID" value="">
                <p class="mt-10 text-muted">Apa anda yakin ingin menghapus pajak ini <span id="delete-Pajak"></span>? </p>
            </div>
            <div class="modal-footer pb-15">
                <button type="submit" id="btnDelete" class="btn btn-sm btn-success px-20">Yakin</button>
            </div>
        </div>
    </div>
</div>
<!-- Alert Hapus dan Edit Pajak -->
<div class="alert-fixed" id="alert-update-delete" style="position: fixed; top: 30px; z-index: 1100; display: none;">
    <div class="alert alert-success text-center" role="alert" style="display: none">
        Hapus User Berhasil
    </div>
    <div class="alert alert-danger text-center" role="alert" style="display: none">
        Hapus User Berhasil
    </div>
</div>

