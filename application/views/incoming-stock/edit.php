<style>
    .deleted {
        display: none;
    }
</style>

<div class="row">
    <div class="col-md-6">
        <br/><a href="<?= base_url() ?>stokmasuk/?outlet=<?= $koreksi_stok->DeviceID; ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
    <div class="col-md-12">
        <div class="alert" style="text-align: center;background-color: #fff" id="loading-content">Loading Data...</div>
        <div style="display: none;" id="main-content">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-pencil"></i></span>
                    <h4>Edit Stok Masuk</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form class="form-horizontal form-store" method="post"
                              action="<?php echo base_url(); ?>stokmasuk/simpan">
                            <input type="hidden" name="mode" value="edit">
                            <input type="hidden" name="transaction_id" value="<?= $koreksi_stok->TransactionID ?>">
                            <input type="hidden" name="devno" value="<?= $koreksi_stok->DeviceNo ?>">
                            <input type="hidden" name="date_start" value="<?= $date_start ?>">
                            <input type="hidden" name="date_end" value="<?= $date_end ?>">
                            <div class="form-group">
                                <label class="col-md-2 control-label">Outlet</label>
                                <div class="col-md-10">
                                    <input type="text" name="namaoutlet" class="form-control"
                                           value="<?= $nama_alamat_outlet ?>" readonly>
                                    <input type="hidden" name="outlet" class="form-control"
                                           value="<?= $koreksi_stok->DeviceID ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Number</label>
                                <div class="col-md-10">
                                    <input type="text" class="form-control"
                                           value="<?= $koreksi_stok->StockOpnameNumber ?>" disabled="disabled">
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
                                    <table id="dynamic-table" class="table table-bordered" style="min-width:600px">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th style="min-width: 160px">Jumlah</th>
                                            <th>Keterangan</th>
                                            <!-- <th></th> -->
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr data-id="-1" class="deleted">
                                                <td>
                                                    <input type="hidden" name="detail_id[]"
                                                           value=""/>
                                                    <input type="hidden" name="detail_devno[]"
                                                           value=""/>
                                                    <input type="hidden" name="detail_deleted[]"
                                                           value="0"/>
                                                    <input type="hidden" name="detail_added[]"
                                                           value="0"/>
                                                           
                                                    <select data-show-subtext="true" data-live-search="true"
                                                            class="form-control selectpicker" name="item[]"
                                                            data-placeholder="Pilih item...">
                                                        <option></option>
                                                        <?php foreach ($items as $key => $item_data): ?>
                                                            <option value="<?= $item_data->ItemID . "." . $item_data->DeviceNo ?>"><?= $item_data->ItemName ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                                <input type="hidden" value=""
                                                       name="qty-sistem[]" class="form-control"/>
                                                <input type="hidden" name="qty-aktual[]"
                                                       value="" class="form-control"/>
                                                <td>
                                                    <div class="input-group input-value">
                                                        <input type="number" name="qty-selisih[]"
                                                               value=""
                                                               class="form-control"/>
                                                        <div class="input-group-addon satuan-selisih">...</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea style="height: 38px" class="form-control"
                                                              name="keterangan[]"></textarea>
                                                </td>
                                                <!-- <td class="text-center">
                                                    <a data-id="-1"
                                                       class="btn btn-default hapus-item"><i
                                                                class="fa fa-trash"></i></a>
                                                </td> -->
                                            </tr>
                                        <?php
                                        $no = 1;
                                        foreach ($item_stok as $key => $item): ?>

                                            <tr data-id="<?= $item->DetailID; ?>">
                                                <td>
                                                    <input type="hidden" name="detail_id[]"
                                                           value="<?= $item->DetailID; ?>"/>
                                                    <input type="hidden" name="detail_devno[]"
                                                           value="<?= $item->DetailDeviceNo; ?>"/>
                                                    <input type="hidden" name="detail_deleted[]"
                                                           value="0"/>
                                                    <input type="hidden" name="detail_added[]"
                                                           value="0"/>
                                                    <select data-show-subtext="true" data-live-search="true"
                                                            class="form-control selectpicker" name="item[]"
                                                            data-placeholder="Pilih item...">
                                                        <option></option>
                                                        <?php foreach ($items as $key => $item_data): ?>
                                                            <option
                                                                <?php if ($item->ItemID == $item_data->ItemID && $item->ItemDeviceNo == $item_data->DeviceNo) {
                                                                    echo ' selected="selected" ';
                                                                } ?>
                                                                    value="<?= $item_data->ItemID . "." . $item->ItemDeviceNo ?>"><?= $item_data->ItemName ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                                <input type="hidden" value="<?= $item->StockByApp ?>"
                                                       name="qty-sistem[]" class="form-control"/>
                                                <input type="hidden" name="qty-aktual[]"
                                                       value="<?= $item->RealStock ?>" class="form-control"/>
                                                <td>
                                                    <div class="input-group input-value">
                                                        <input type="number" name="qty-selisih[]"
                                                               value="<?= $item->RealStock - $item->StockByApp ?>"
                                                               class="form-control"/>
                                                        <div class="input-group-addon satuan-selisih"><?= $item->Unit; ?></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea style="height: 38px" class="form-control"
                                                              name="keterangan[]"><?= $item->Note ?></textarea>
                                                </td>
                                                <!-- <td class="text-center">
                                                    <a data-id="<?= $item->DetailID; ?>"
                                                       class="btn btn-default hapus-item"><i
                                                                class="fa fa-trash"></i></a>
                                                </td> -->
                                            </tr>
                                            <?php
                                            $no++;
                                        endforeach ?>
                                        </tbody>
                                    </table>
                                    <br/>
                                    <div id="tambah-item" style="cursor: pointer;"><i class="fa fa-plus"></i> Tambah
                                        Item Lain
                                    </div>
                                    <br/>
                                    <div class="form-actions text-right">
                                        <a href="<?= base_url() ?>stokmasuk/?outlet=<?= $koreksi_stok->DeviceID ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>"
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
</div>
