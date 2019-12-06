<form class="form-horizontal" id="form-outlet" style="margin-top: 20px">
    <div class="col-md-6">
        <a href="<?= base_url() ?>pembelian/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
    <div class="col-md-6">
        <div class="form-group pull-right col-md-12">
            <div class="col-md-3">
                <label class="control-label">Outlet</label>
            </div>
            <div class="col-md-9">
                <select class="form-control" name="outlet" id="outlet"
                        onchange="document.getElementById('form-outlet').submit()">
                    <?php foreach ($outlets as $key => $outlet): ?>
                        <option value="<?= $key ?>" <?= $_GET['outlet'] == $key ? "selected" : "" ?>>
                            <?= str_replace('#$%^', ' ', $outlet); ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-plus"></i></span>
                <h4>Transaksi Pembelian</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class="widget-block">
                    <form class="form-horizontal form-store" method="post" action="<?php echo base_url(); ?>pembelian/store">
                        <input type="hidden" name="outlet" value="<?= $_GET['outlet'] ?>">
                        <!--div class="form-group">
                            <label class="col-md-2 control-label">Tanggal</label>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <div class="input-group">
                                            <input type="text" name="datetime" class="form-control datetime"/>
                                            <span class="input-group-addon button-focus">
                                                <i class="glyphicon glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div-->
                        <div class="form-group">
                            <label class="col-md-2 control-label">Supplier</label>
                            <div class="col-md-10">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <select name="supplier" class="form-control">
                                            <option></option>
                                            <?php /*foreach ($suppliers as $key => $value): ?>
                                                <option value="<?= $value->SupplierID ?>"><?= $value->Nama ?></option>
                                            <?php endforeach */?>
                                        </select>
                                    </div>
                                    <?php if($visibilityMenu['SupplierDelete']) {?>
                                    <a href="#" id="hapus_supplier" class="btn btn-default" disabled>Hapus</a>
                                    <?php } if($visibilityMenu['SupplierEdit']) {?>
                                    <a href="#" id="edit_supplier" class="btn btn-default" disabled>Edit</a>
                                    <?php } if($visibilityMenu['SupplierAdd']) {?>
                                    <a href="#" id="tambah_supplier" class="btn btn-info">Tambah</a>
                                    <?php } ?>
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
                                        <th style="min-width:60px">Qty</th>
                                        <th>Harga Satuan</th>
                                        <th>Diskon</th>
                                        <th>Total</th>
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
                            <label class="col-md-9 control-label">Diskon Final</label>
                            <div class="col-md-3">
                                <div style="margin-right: -15px;width: calc(100% + 30px);" class="row pull-right">
                                    <div style="padding-right:3px;width: calc(100% - 97px);float:left;margin-left:15px">
                                        <input type="number" name="diskon_final" class="form-control" min="0">
                                    </div>
                                    <div style="padding-left:3px;width:67px;float:right;margin-right:15px">
                                        <select class="form-control" name="jenis_diskon_final"><option value="%">%</option><option value="Rp">Rp</option></select>
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
                                    <a href="<?= base_url() ?>pembelian/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Cancel</a>
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
$(".selectpicker").select2();
    var interval = setInterval(function () {
        if (document.readyState === 'complete') {
            clearInterval(interval);
            load_supplier();
            render_row();
        }
    }, 100);
</script>