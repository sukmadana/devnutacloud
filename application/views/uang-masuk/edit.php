<div class="row">
    <div class="col-md-6">
        <br/><a href="<?= base_url() ?>uangmasuk/?outlet=<?= $uang_masuk->DeviceID; ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>"
                class="btn btn-default">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="alert" style="text-align: center;background-color: #fff" id="loading-content">Loading Data...</div>
    <div style="display: none;" id="main-content">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-pencil"></i></span>
                    <h4>Edit Uang Masuk</h4>
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
                        <?php if ($iscloud === 0) { ?>
                            <input type="hidden" name="deviceno" value="<?= $uang_masuk->DeviceNo ?>"/>
                        <?php } ?>
                            <input type="hidden" name="date_start" value="<?= $date_start; ?>"/>
                            <input type="hidden" name="date_end" value="<?= $date_end; ?>"/>
                            <input type="hidden" name="iscloud" value="<?=$iscloud?>" />
                            <div class="form-group">
                                <label class="col-md-4 control-label">Outlet</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" name="namaoutlet" class="form-control"
                                                   value="<?= $nama_alamat_outlet ?>" readonly>
                                            <input type="hidden" name="outlet" class="form-control"
                                                   value="<?= $uang_masuk->DeviceID; ?>" readonly>
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
                                                   readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Masuk ke</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <select class="form-control" id="list-account" name="accountid"
                                                    onchange="accountChange()">
                                                <option value=""></option>
                                                <?php $i = 0;
                                                foreach ($items as $v) { ?>
                                                    <option value="<?= $v->AccountID . "." . $v->DeviceNo; ?>"
                                                        <?= ($v->AccountID . "." . $v->DeviceNo) == ($uang_masuk->AccountID  . "." . $uang_masuk->AccountDeviceNo) ? 'selected' : ''; ?>
                                                    ><?= $v->AccountName; ?></option>
                                                    <?php
                                                    $i++;
                                                } ?>
                                            </select>
                                            <input type="hidden" id="cashbankaccountname" name="cashbankaccountname"
                                                   value="<?php
                                                   $i = 0;
                                                   foreach ($items as $v) {
                                                       if ($v->AccountID == $uang_masuk->AccountID) {
                                                           echo str_replace('"', '&quot;', $v->AccountName);
                                                           break;
                                                       }
                                                   } ?>">
                                            <span id="demo"></span>

                                            <script>
                                                function accountChange() {
                                                    var x = document.getElementById("list-account")[document.getElementById("list-account").selectedIndex].innerHTML;
                                                    //alert(x.length);
                                                    document.getElementById("cashbankaccountname").value = x.trim();
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Dari</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-dari" name="receivedfrom"
                                                   value="<?= $uang_masuk->ReceivedFrom; ?>">
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
                                                   value="<?= $uang_masuk->Amount; ?>" min="0"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Keterangan</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <textarea type="text" class="form-control" id="txt-keterangan" name="note"><?= $uang_masuk->Note; ?></textarea>
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
                                                   value="1" <?= $uang_masuk->IncomeType == 1 ? 'checked' : ''; ?>>Pendapatan
                                        </label>
                                    </div>
                                    <div class="radio" style="display: inline-block !important;margin-left:15px;">
                                        <label>
                                            <input type="radio" name="jenis"
                                                   value="2" <?= $uang_masuk->IncomeType == 2 ? 'checked' : ''; ?>>Non Pendapatan
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-actions text-right">
                                    <a href="<?= base_url() ?>uangmasuk/?outlet=<?= $uang_masuk->DeviceID; ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>"
                                       class="btn btn-default">Cancel</a>
                                    <button type="submit" class="btn btn-primary  has-spinner"
                                            id="btn-simpan-single-outlet">
                                        <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Simpan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
