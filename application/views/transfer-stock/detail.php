<?php 
// detail transfer stok
?>
<div class="row">
    <div class="col-md-6">
        <br/><a href="<?= base_url() ?>transferstok/?outlet=<?= $deviceid ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
</div>
<div class="row">
    <div id="main-content">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-plus"></i></span>
                    <h4>Lihat Transfer Stok</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form class="form-horizontal form-store" >	

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
                                    <input class="form-control" type="text" value="<?= $lokasi_awal ?>" readonly="readonly"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="text-align: left">Lokasi Tujuan</label>
                                <div class="col-sm-4">
                                	<?php 
                                    $lokasi_tujuan = '';
                                    foreach ($outlets as $key => $outlet){
                                        if ($key == $transfer->TransferToDeviceID) {
                                            $lokasi_tujuan = str_replace('#$%^', ' ', $outlet);
                                        }
                                    }
                                    ?>
                                    <input class="form-control" type="text" value="<?= $lokasi_tujuan ?>" readonly="readonly"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <table id="dynamic-table" class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th style="text-align: center;">No</th>
                                            <th>Item</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $no_urut_item=1;
                                            foreach ($transfer_detail as $key => $value): ?>
                                            	<tr>
	                                            	<td class="text-center"><?= $no_urut_item; ?></td>
	                                            	<td><?= $value['ItemName']; ?></td>
	                                            	<td><?= $value['Quantity']; ?> <?= $value['Unit']; ?></td>
	                                            	<td><?= $value['Note']; ?></td>
                                            	</tr>
                                            <?php $no_urut_item++; 
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
</div>
