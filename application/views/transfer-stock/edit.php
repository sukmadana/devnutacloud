<?php 
// detail transfer stok
?>
<div class="row">
    <div class="col-md-6">
        <br/><a href="<?= base_url() ?>transferstok/?outlet=<?= $deviceid ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
</div>
<div class="row">
    <div class="alert" style="text-align: center;background-color: #fff" id="loading-content">Loading Data...</div>
    <div style="display: none;" id="main-content">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-plus"></i></span>
                    <h4>Edit Transfer Stok</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form method="post" action="<?php echo base_url(); ?>transferstok/store" class="form-horizontal form-store" >	
                            <input type="hidden" name="mode" value="edit">
                            <input type="hidden" name="from_outlet" value="<?= $deviceid ?>">
                            <input type="hidden" name="id_transfer" value="<?= $transfer->TransactionID ?>">

                        	<div class="form-group">
                                <label class="col-sm-2 control-label" style="text-align: left">Nomor</label>
                                <div class="col-sm-4">
                                    <input class="form-control" type="text" value="<?= $transfer->TransferNumber ?>" readonly="readonly"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="text-align: left">Tanggal</label>
                                <div class="col-sm-4">
                                    <input class="form-control" type="text" value="<?= date_format(date_create($transfer->TransferDate), "j M Y").' , '.$transfer->TransferTime ?>" readonly="readonly"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="text-align: left">Lokasi Awal</label>
                                <div class="col-sm-4">
                                	<?php 
                                    $lokasi_awal = '';
                                    foreach ($outlets as $key => $outlet){
                                        if ($key == $deviceid) {
                                            $lokasi_awal = str_replace('#$%^', ' ', $outlet);
                                        }
                                    }
                                    ?>
                                    <input class="form-control" type="text" value="<?= $lokasi_awal ?>" readonly="readonly" required="required"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="text-align: left">Lokasi Tujuan</label>
                                <div class="col-sm-4">
                                    <select name="to_outlet" class="form-control" required="required">
                                        <?php 
                                        $lokasi_tujuan = "";
                                        foreach ($outlets as $key => $outlet){
                                            if ($deviceid != $key){
                                                echo '<option '; 
                                                if ($key == $outlet_tujuan) {
                                                    echo ' selected=selected" "';
                                                    $lokasi_tujuan=str_replace('#$%^', ' ', $outlet); 
                                                }
                                                echo ' value="'.$key.'">'.str_replace('#$%^', ' ', $outlet).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <?php if ($items): ?>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <table id="dynamic-table" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Jumlah</th>
                                                <th>Keterangan</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                                

                                                <?php foreach ($transfer_detail as $key => $value): ?>
                                                	<tr data-id="1">
                                                        <td>
                                                            <select data-show-subtext="true" data-live-search="true"
                                                                    class="form-control selectpicker" name="item-name[]"
                                                                    data-placeholder="Pilih item..." required="required">
                                                                <option></option>
                                                                <?php foreach ($items as $key => $item): ?>
                                                                    <option <?php
                                                                    if ($item['ItemID']==$value['ItemID']) {
                                                                        echo ' selected="selected" ';
                                                                    } 
                                                                    ?> value="<?= $item['ItemID'] ?>"><?= $item['ItemName'] ?></option>
                                                                <?php endforeach ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <div class="input-group input-value" value="0" data-id="1">
                                                                <input required="required" min="0" oninput="validity.valid||(value='');" type="number" value="<?= $value['Quantity'] ?>" name="item-total[]" class="form-control"/>
                                                                <div class="input-group-addon satuan-sistem"><?= $value['Unit'] ?></div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <textarea style="height: 38px" class="form-control"
                                                                      name="note[]"><?= $value['Note'] ?></textarea>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="#" class="btn btn-default hapus-item"><i
                                                                        class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>

                                                <?php endforeach ?>
                                            </tbody>

                                        </table>

                                        <br/>
                                        <div id="tambah-item" style="cursor: pointer;"><i class="fa fa-plus"></i> Tambah
                                            Item Lain
                                        </div>
                                        <br/>

                                        <div class="form-actions text-right">
                                            <a href="<?= base_url() ?>transferstok/?outlet=<?= $deviceid ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>"
                                               class="btn btn-default">Cancel</a>
                                            <button type="submit" class="btn btn-primary  has-spinner"
                                                    id="btn-simpan-single-outlet">
                                                <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Simpan
                                            </button>
                                        </div>
                                       
                                    </div>
                                </div>

                            <?php else: ?>
                                <div class="alert alert-default">Tidak ada item yang bisa ditransfer dari Lokasi Awal (<b><?= $lokasi_awal ?></b>) ke Lokasi Tujuan (<b><?= $lokasi_tujuan ?></b>), karena semua itemnya berbeda, tidak ada yang sama.</div>


                            <?php endif ?>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
