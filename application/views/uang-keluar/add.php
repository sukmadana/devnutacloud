<div class="row">
    <div class="col-md-6">
        <br/><a href="<?= base_url() ?>uangkeluar/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
    <div class="col-md-6">
        <form class="form-horizontal" id="form-outlet" style="margin-top: 20px">
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
        </form>
    </div>
</div>

<div class="row">
    <div class="alert" style="text-align: center;background-color: #fff" id="loading-content">Loading Data...</div>
    <div style="display: none;" id="main-content">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-plus"></i></span>
                    <h4>Transaksi Uang Keluar</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form class="form-horizontal form-store" method="post"
                              action="<?php echo base_url(); ?>uangkeluar/simpan">
                            <input type="hidden" name="outlet" value="<?= $_GET['outlet'] ?>">
                            <input type="hidden" name="mode" value="new">
                            <input type="hidden" name="date_start" value="<?= $date_start ?>">
                            <input type="hidden" name="date_end" value="<?= $date_end ?>">
                            <input type="hidden" name="iscloud" value="1">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Ambil dari</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <select class="form-control" id="list-account" name="accountid"
                                                    onchange="accountChange()">
                                                <option value=""></option>
                                                <?php $i = 0;
                                                foreach ($items as $v) { ?>
                                                    <option value="<?= $v->AccountID . "." . $v->DeviceNo; ?>"
                                                        <?= $i == 0 ? 'selected' : ''; ?>
                                                    ><?= $v->AccountName; ?></option>
                                                    <?php
                                                    $i++;
                                                } ?>
                                            </select>
                                            <input type="hidden" id="cashbankaccountname" name="cashbankaccountname"
                                                   value="<?php
                                                   $i = 0;
                                                   foreach ($items as $v) {
                                                       echo str_replace('"', '&quot;', $v->AccountName);
                                                       break;
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
                                <label class="col-md-4 control-label">Bayar Ke</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-dari" name="paidto"
                                                   value="">
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
                                                   value="" min="0"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Keterangan</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <textarea type="text" class="form-control" id="txt-keterangan" name="note" value=""></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Jenis</label>
                                <div class="col-md-8">
                                    <div class="radio" style="display: inline-block !important">
                                        <label>
                                            <input type="radio" name="jenis" value="1" checked>Biaya
                                        </label>
                                    </div>
                                    <div class="radio" style="display: inline-block !important;margin-left:15px;">
                                        <label>
                                            <input type="radio" name="jenis" value="2">Non Biaya
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-actions text-right">
                                    <a href="<?= base_url() ?>uangkeluar/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>"
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
