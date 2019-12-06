<?php /* <div class="col-md-6" style="margin: 30px 0">
    <a href="<?= base_url() ?>pembelian?outlet=<?= $deviceid ?>" class="btn btn-default">Kembali</a>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Ubah Pembelian</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class="widget-block">
                    <form class="form-horizontal form-store" method="post"
                          action="/pembelian/update/<?= $deviceid . '/' . $purchase->TransactionID ?>">
                        <input type="hidden" name="outlet" value="<?= $deviceid ?>">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Tanggal</label>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <div class="input-group">
                                            <input type="text" name="datetime" class="form-control datetime" required
                                                   value="<?= $purchase->PurchaseDate . ' ' . $purchase->PurchaseTime ?>"/>
                                            <span class="input-group-addon button-focus">
                                                <i class="glyphicon glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Supplier</label>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="supplier" class="form-control">
                                            <?php foreach ($suppliers as $key => $value): ?>
                                                <option value="<?= $value->SupplierID ?>"
                                                    <?php if ($value->Nama == $purchase->SupplierName): ?>
                                                        selected
                                                    <?php endif ?>
                                                ><?= $value->Nama ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-7">
                                <table class="table table-bordered  table-striped " id="grid-item">
                                    <thead>
                                    <tr>
                                        <th>Nama Item</th>
                                        <th>Jumlah</th>
                                        <th>Harga</th>
                                        <th>Sub total</th>
                                        <td></td>
                                    </tr>
                                    </thead>
                                    <tbody id="compiling-form">

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 control-label">
                                <a type="button" onclick="render_row()" style="cursor: pointer">+ Tambah Item Lain</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Pembayaran</label>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="pembayaran" class="form-control" id="pembayaran"
                                                onchange="purcaseLoad()">
                                            <option value=""></option>
                                            <option value="tunai" <?= $purchase->PaymentMode == 1 ? "selected" : "" ?>>
                                                Tunai
                                            </option>
                                            <option value="kartu" <?= $purchase->PaymentMode == 2 ? "selected" : "" ?>>
                                                Kartu
                                            </option>
                                            <option value="campuran" <?= $purchase->PaymentMode == 3 ? "selected" : "" ?>>
                                                Campuran
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="paymentContainer"></div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-8">
                                <div class="form-actions">
                                    <a href="<?= base_url() ?>pembelian?outlet=<?= $deviceid ?>"
                                       class="btn btn-default">Cancel</a>
                                    <button type="submit" class="btn btn-primary  has-spinner"
                                            id="btn-simpan-single-outlet">
                                        <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<template id="tunai">
    <div class="form-group">
        <label class="col-md-4 control-label">Jumlah Bayar</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" class="form-control" name="jumlah-bayar" id="bayartunai"
                           onchange="purchase.bayarTunai()" value="<?= $purchase->CashPaymentAmount ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Kembalian</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" class="form-control" name="kembalian" id="kembalian" readonly
                           value="<?= $purchase->Change ?>">
                </div>
            </div>
        </div>
    </div>
</template>

<template id="kartu">
    <div class="form-group">
        <label class="control-label col-md-4">Total Belanja</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" class="form-control" name="belanjaKartu" readonly id="belanjakartu"
                           value="<?= $purchase->Total ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Masuk Ke</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <select name="masukKeKartu" class="form-control alertBank">
                        <option value=""></option>
                        <?php foreach ($getAccount as $key => $value): ?>
                            <option value="<?= $value->AccountID ?>"
                                <?= $value->AccountID == $purchase->BankAccountID ? 'selected' : '' ?>>
                                <?= $value->AccountName ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Charge</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number"
                           value="<?= ($purchase->TotalPayment - $purchase->Total) / $purchase->Total * 100 ?>" min="0"
                           class="form-control" name="chargeKartu" id="chargekartu" onchange="purchase.bayarKartu()">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Total</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" class="form-control" name="totalKartu" readonly id="totalkartu"
                           value="<?= $purchase->TotalPayment ?>">
                </div>
            </div>
        </div>
    </div>
</template>

<template id="campuran">
    <div class="form-group">
        <label class="col-md-4 control-label">Total Belanja</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" class="form-control" name="belanjaCampuran" id="belanjacampuran"
                           readonly
                           value="<?= $purchase->Total ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Tunai</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" class="form-control" name="tunaiCampuran" id="tunaicampuran"
                           value="<?= $purchase->CashPaymentAmount ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Masuk Ke</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6 col-xs-12">
                    <select name="masukKeCampuran" class="form-control alertBank">
                        <option></option>
                        <?php foreach ($getAccount as $key => $value): ?>
                            <option value="<?= $value->AccountID ?>"
                                <?= $value->AccountID == $purchase->BankAccountID ? 'selected' : '' ?>>
                                <?= $value->AccountName ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-md-4"></label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0"
                           value="<?= ($purchase->TotalPayment - ($purchase->Total - $purchase->CashPaymentAmount)) / ($purchase->Total - $purchase->CashPaymentAmount) * 100 ?>"
                           class="form-control" name="chargeCampuran" id="chargecampuran"
                           onchange="purchase.bayarCampuran()">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-4 control-label">Total</label>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" min="0" class="form-control" name="totalCampuran" id="totalcampuran" readonly
                           value="<?= $purchase->TotalPayment ?>">
                </div>
            </div>
        </div>
    </div>
</template>
<script type="text/javascript">
    var interval = setInterval(function () {
        if (document.readyState === 'complete') {
            clearInterval(interval);
            $("#pembayaran").trigger('change');
            <?php foreach ($purchase_detail as $key => $value): ?>
            render_row(<?= $value->ItemID ?>, <?= $value->Quantity ?>);
            <?php endforeach ?>
        }
    }, 100);
</script>

*/?>

<?php //print_r($purchase); ?>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<form class="form-horizontal" id="form-outlet" style="margin-top: 20px">
    <div class="col-md-6">
        <a href="<?= base_url() ?>pembelian/?outlet=<?= $deviceid ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
</form>

<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-plus"></i></span>
                <h4>Edit Transaksi Pembelian</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class="widget-block">
                    <form class="form-horizontal form-store" method="post" action="<?= base_url('pembelian/update/'. $deviceid . '/' . $purchase->TransactionID . '.' . $purchase->DeviceNo) ?>">
                        <input type="hidden" name="outlet" value="<?= $deviceid ?>">
                        <input type="hidden" name="date_start" value="<?= $date_start ?>">
                        <input type="hidden" name="date_end" value="<?= $date_end ?>">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Outlet:</label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" value="<?= str_replace('#$%^', ' ', $outlets[$deviceid]); ?>" disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Supplier:</label>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="supplier" class="form-control">
                                            <option></option>
                                        </select>
                                    </div>
                                    <a href="#" id="hapus_supplier" class="btn btn-default" disabled>Hapus</a>
                                    <a href="#" id="edit_supplier" class="btn btn-default" disabled>Edit</a>
                                    <a href="#" id="tambah_supplier" class="btn btn-info">Tambah</a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label"></label>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <textarea class="form-control" id="alamat_supplier" placeholder="Alamat Supplier" disabled="disabled"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display: none;" id="alert-box">
                            <div class="alert"></div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2 control-label">Item yang dibeli:</div>
                            <div class="col-md-10">
                                <table class="table table-bordered  table-striped " id="grid-item" style="min-width:800px">
                                    <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Harga Satuan</th>
                                        <th>Diskon</th>
                                        <th>Total</th> 
										<th></th>
                                    </tr>
                                    </thead>
                                    <tbody id="compiling-form">
                                        <?php 
                                        
                                        // print_r($items);
                                        $number=1;
                                        foreach ($purchase_detail as $key => $item_detail): ?>
                                           
                                        
                                        <tr data-id="<?= $number  ?>">
                                            <td>
												<select data-show-subtext="true" data-live-search="true"
                                                            class="form-control selectpicker" name="item-name[]"
                                                            data-placeholder="Pilih item...">
															<option></option>
                                                    <?php foreach ($items as $key => $item): ?>
                                                        <option <?php if($item_detail->ItemID == $item->ItemID && $item_detail->ItemDeviceNo == $item->DeviceNo){echo 'selected="selected"';} ?> value="<?= $item->ItemID. "." . $item->DeviceNo ?>"><?= $item->ItemName ?></option>
                                                    <?php endforeach ?>
                                                </select>
												
                                            </td>
                                            <td id="jumlah-<?= $number  ?>">
                                                <div class="input-group input-value" value="0" data-id="<?= $number  ?>">
                                                    <input type="number" step="0.0001" value="<?= $item_detail->Quantity ?>" name="item-total[]" class="form-control" min="0">
                                                    <div class="input-group-addon satuan">PCS</div>
                                                </div>
                                            </td>
                                            <td><input value="<?= $item_detail->UnitPrice ?>" type="number" name="price[]" class="form-control" min="0" step="0.01" required="" id="price-<?= $number ?>"></td>
                                            <td>
                                                <div class="row" id="diskon-<?= $number  ?>">
                                                    <?php
                                                        $discount = explode("%",$item_detail->Discount);
                                                        if(count($discount)>1) {
                                                            $persen_selected = ' selected="selected" ';
                                                            $rupiah_selected = '';
                                                        }
                                                        else {
                                                            $persen_selected = '';
                                                            $rupiah_selected = ' selected="selected" ';
                                                        }
                                                    ?>
                                                    <div style="padding-right:3px;width: calc(100% - 97px);float:left;margin-left:15px"><input type="number" name="diskon[]" value="<?= $discount[0] ?>" class="form-control" min="0"></div>
                                                    <div style="padding-left:3px;width:67px;float:right;margin-right:15px">


                                                        <select class="form-control" name="jenis_diskon[]">
                                                            <option <?= $persen_selected ?> value="%">%</option>
                                                            <option <?= $rupiah_selected ?> value="Rp">Rp</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><input type="text" name="sum[]" class="form-control" readonly="" min="0" required="" id="total-<?= $number  ?>"></td>
                                            <td style="display: none"><input type="text" value="<?= $item_detail->DetailID ?>" name="detailid[]" class="form-control" id="detailid-<?= $number  ?>"></td>
                                            <td style="display: none"><input type="text" value="<?= $item_detail->DeviceNo ?>" name="detaildevno[]" class="form-control" id="detaildevno-<?= $number  ?>"></td>
                                            <td style="display: none"><button class="hapus-item" data-id="<?= $number ?>" type="button" class="btn btn-default"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></button></td>
                                        </tr>

                                        <?php 
                                        $number++;
                                        endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 control-label">
                                <a type="button" onclick="render_row()" style="cursor: pointer">+ Tambah Item Lain</a>
                            </div>
                        </div>

                        <?php 
                        $final_discount = explode("%",$purchase->FinalDiscount);
                        if(count($final_discount)>1) {
                            $persen_selected_final = ' selected="selected" ';
                            $rupiah_selected_final = '';
                        } else {
                            $persen_selected_final = '';
                            $rupiah_selected_final = ' selected="selected" ';
                        }
                        ?>
                        <div class="form-group">
                            <label class="col-md-9 control-label">Diskon Final</label>
                            <div class="col-md-3">
                                <div style="margin-right: -15px;width: calc(100% + 30px);" class="row pull-right">
                                    <div style="padding-right:3px;width: calc(100% - 97px);float:left;margin-left:15px">
                                        <input value="<?= $final_discount[0] ?>" type="number" name="diskon_final" class="form-control" min="0">
                                    </div>
                                    <div style="padding-left:3px;width:67px;float:right;margin-right:15px">
                                        <select class="form-control" name="jenis_diskon_final"><option <?= $persen_selected_final ?> value="%">%</option><option <?= $rupiah_selected_final ?> value="Rp">Rp</option></select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-9 control-label">Grand Total</label>
                            <div class="col-md-3">
                                <input class="form-control" type="text" name="grand-total" disabled="disabled"/>
                                <input class="form-control" type="hidden" name="grand-total2"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Bayar dengan rekening:</label>
                            <div class="col-md-4">
                                <select name="bank_account" class="form-control pull-left">
                                    <?php
                                    if ($getAccount) {
                                        foreach ($getAccount as $key => $bank_account) {
                                            echo '<option value="'.$bank_account->AccountID.'.'.$bank_account->DeviceNo.'">'.$bank_account->BankName.' '.$bank_account->AccountNumber.' '.$bank_account->AccountName.'</option>';
                                        } 
                                    } else{
                                        echo '<option disabled>Belum ada bank akun</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12">
                                <div class="form-actions text-right">
                                    <a href="<?= base_url() ?>pembelian/?outlet=<?= $deviceid ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Cancel</a>
                                    <button type="submit" class="btn btn-primary  has-spinner" id="btn-simpan-single-outlet">
                                        <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>


<?php /*modal add/edit supplier*/ ?>
<div class="modal fade" id="modal_supplier" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="supplier_name" class="form-control" placeholder="Nama...">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="supplier_alamat" class="form-control" placeholder="Alamat..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="supplier_telepon" class="form-control" placeholder="Telepon...">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="supplier_email" class="form-control" placeholder="Email...">
                    </div>
                     <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="supplier_catatan" class="form-control" placeholder="Catatan..."></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="simpan_supplier" type="button" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script> 
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
 
    var interval = setInterval(function () {
        if (document.readyState === 'complete') {
            clearInterval(interval);
            console.log('<?= ($purchase->SupplierID . "." . $purchase->SupplierDeviceNo) ?>');
            load_supplier("<?= ($purchase->SupplierID . "." . $purchase->SupplierDeviceNo) ?>");
            render_rowawal();
        }
    }, 100);
</script>
