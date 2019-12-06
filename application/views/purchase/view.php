<div class="row">
    <br/>
    <div class="col-md-6">
        <a href="<?= base_url() ?>pembelian/?outlet=<?= $deviceid; ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Detail Pembelian</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>

            <div class="widget-container">
                <div class="widget-block">
                    <form class="form-horizontal form-store" method="post" action="<?php echo base_url(); ?>pembelian/store">
                       	<div class="form-group">
                            <label class="col-md-2 control-label">Outlet</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="<?= str_replace('#$%^', ' ', $outlets[$deviceid]); ?>" disabled="disabled">
                            </div>
                        </div>

                       	<div class="form-group">
                            <label class="col-md-2 control-label">Tanggal</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="<?= $transaction_data[0]->CreatedDate; ?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Supplier</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="<?= $transaction_data[0]->SupplierName; ?>" disabled="disabled">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-2 control-label">Alamat Supplier</label>
                            <div class="col-md-10">
                                <textarea class="form-control" disabled="disabled"><?= $supplier_single->SupplierAddress ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2 control-label">Item yang dibeli:</div>
                            <div class="col-md-10">
                                <table class="table table-bordered  table-striped " id="grid-item" style="min-width:800px">
                                    <thead>
                                        <tr class="info">
                                            <th>No</th>
                                            <th>Item</th>
                                            <th>Qty</th>
                                            <th>Harga Satuan</th>
                                            <th>Diskon</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="compiling-form">
                                        <?php 
                                        $no_urut_item = 1; 
                                        $total = 0;
                                        ?>
                                        <?php foreach ($purchase_detail as $key => $item): ?>

                                            <?php 
                                            //cek diskon
                                            if (strpos($item->Discount, '%')) {
                                                $diskon_value = explode("%", $item->Discount)[0];
                                                $harga_new = $item->UnitPrice - ($item->UnitPrice * $diskon_value / 100);
                                            }  else {
                                                $harga_new = $item->UnitPrice - $item->Discount;
                                            }
                                            ?>
                                            <tr>
                                                <td class="text-center"><?= $no_urut_item ?></td>
                                                <td><?= $item->ItemName ?></td>
                                                <td class="text-center"><?= $item->Quantity ?></td>
                                                <td class="text-right"><?= $item->UnitPrice ?></td>
                                                <td class="text-center"><?= $item->Discount ?></td>
                                                <td class="text-right"><?= ( $item->Quantity * $harga_new ) ?></td>
                                            </tr>
                                            <?php 
                                            $no_urut_item++; 
                                            $total+=$item->Quantity * $harga_new;
                                            ?>
                                        <?php endforeach ?>

                                            <tfoot>
                                                <tr>
                                                    <th style="text-align: right;" colspan="5">Total</th>
                                                    <th style="text-align: right;"><?= $total ?></th>
                                                </tr>
                                            </tfoot>
                                    </tbody>
                                </table>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-9 control-label">Diskon Final</label>
                            <div class="col-md-3">
                                <input class="form-control" type="text" value="<?= $transaction_data[0]->FinalDiscount; ?>" name="grand-total" disabled="disabled"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-9 control-label">Grand Total</label>
                            <div class="col-md-3">
                                <input class="form-control" type="text" value="Rp <?= $transaction_data[0]->TotalPayment; ?>" name="grand-total" disabled="disabled"/>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-md-9 control-label">Bayar dengan rekening</label>
                            <div class="col-md-3">
                                <input class="form-control" type="text" value="<?= $getAccount[0]->BankName.' ' . $getAccount[0]->AccountNumber . ' ' . str_replace('"',"&quot;", $getAccount[0]->AccountName); ?>" name="bank_account" disabled="disabled"/>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>