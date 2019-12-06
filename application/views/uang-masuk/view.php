<div class="row">
    <div class="col-md-12">
        <a href="<?= base_url() ?>uangmasuk/?outlet=<?= $uang_masuk->DeviceID; ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
        <?php if ($visibilityMenu['MoneyEdit']) { ?>
        <a class="btn btn-default pull-right"
           href="<?= base_url() ?>uangmasuk/edit/<?= $uang_masuk->DeviceID; ?>/<?= $uang_masuk->TransactionID ?>">Edit</a>
        <?php } ?>
    </div>
</div>

<div class="row">
    <div id="main-content">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-table"></i></span>
                    <h4>Transaksi Uang Masuk</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form class="form-horizontal form-store" method="post"
                              action="<?php echo base_url(); ?>uangmasuk/simpan">
                            <input type="hidden" name="mode" value="edit">
                            <input type="hidden" name="transaction_id" value="<?= $uang_masuk->TransactionID ?>">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Outlet</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" name="namaoutlet" class="form-control"
                                                   value="<?= $nama_alamat_outlet ?>" disabled="disabled">
                                            <input type="hidden" name="outlet" class="form-control"
                                                   value="<?= $uang_masuk->DeviceID; ?>" disabled="disabled">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Tanggal</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" name="transactiondatetime" class="form-control"
                                                   value="<?php
                                                   echo formatdateindonesia($uang_masuk->TransactionDate) . ", " . $uang_masuk->TransactionTime; ?>"
                                                   disabled="disabled">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Ambil dari</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" id="cashbankaccountname" class="form-control"
                                                   name="cashbankaccountname"
                                                   value="<?php
                                                   $i = 0;
                                                   foreach ($items as $v) {
                                                       if ($v->AccountID . "." . $v->DeviceNo == $uang_masuk->AccountID . "." . $uang_masuk->AccountDeviceNo) {
                                                           echo str_replace('"', '&quot;', $v->AccountName);
                                                           break;
                                                       }
                                                   } ?>" disabled="disabled">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Bayar Ke</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-dari" name="receivedfrom"
                                                   value="<?= $uang_masuk->ReceivedFrom; ?>" disabled="disabled">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Jumlah</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="number" class="form-control" id="txt-jumlah" name="amount"
                                                   value="<?= $uang_masuk->Amount; ?>" min="0" disabled="disabled"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Keterangan</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-keterangan" name="note"
                                                   value="<?= $uang_masuk->Note; ?>" disabled="disabled"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Jenis</label>
                                <div class="col-md-8">
                                    <div class="radio" style="display: inline-block !important">
                                        <label>
                                            <input type="radio" name="jenis"
                                                   value="1" <?= $uang_masuk->IncomeType == 1 ? 'checked' : ''; ?>
                                                   disabled="disabled">Pendapatan
                                        </label>
                                    </div>
                                    <div class="radio" style="display: inline-block !important;margin-left:15px;">
                                        <label>
                                            <input type="radio" name="jenis"
                                                   value="2" <?= $uang_masuk->IncomeType == 2 ? 'checked' : ''; ?>
                                                   disabled="disabled">Non Pendapatan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
