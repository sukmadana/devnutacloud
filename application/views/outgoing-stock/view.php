<div class="row">
    <br/>
    <div class="col-md-12">
        <a href="<?= base_url() ?>stokkeluar/?outlet=<?= $koreksi_stok->DeviceID; ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
        <?php if ($visibilityMenu['StockEdit']) { ?>
        <a class="btn btn-default pull-right"
           href="<?= base_url() ?>stokkeluar/edit/<?= $koreksi_stok->DeviceID; ?>/<?= $koreksi_stok->TransactionID . "." . $koreksi_stok->DeviceNo ?>">Edit</a>
        <?php } ?>
    </div>
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Stok Keluar</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span>
                    </li>
                </ul>
            </div>
            <div class="widget-container">
                <div class="widget-block">
                    <form class="form-horizontal form-store">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Outlet</label>
                            <div class="col-md-10">
                                <input type="text" name="namaoutlet" class="form-control"
                                       value="<?= $nama_alamat_outlet ?>" disabled="disabled">
                                <input type="hidden" class="form-control" value="<?= $koreksi_stok->DeviceID ?>"
                                       disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Number</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="<?= $koreksi_stok->StockOpnameNumber ?>"
                                       disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Tanggal</label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" value="<?php
                                //$tanggal = new Datetime($koreksi_stok->TglJamUpdate);
                                //echo $tanggal->format("d-m-y H:i");
                                echo $koreksi_stok->CreatedDate . " " . $koreksi_stok->CreatedTime;
                                ?>" disabled="disabled">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Item</label>
                            <div class="col-md-10">
                                <table id="dynamic-table" class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Item</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($item_stok as $key => $item): ?>

                                        <tr>
                                            <td><?= $no ?></td>
                                            <td><?= $item->ItemName ?></td>
                                            <td class="text-center"><?= -($item->RealStock - $item->StockByApp) ?></td>
                                            <td><?= $item->Note ?></td>
                                        </tr>
                                        <?php
                                        $no++;
                                    endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>