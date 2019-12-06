<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <ol class="breadcrumb">
                            <li>
                                <a href="<?= base_url('perusahaan/outlet')?>">Outlet</a>
                            </li>
                            <li>
                                <a href="<?= base_url('perusahaan/outletdetailinfo/');?>/<?= $detail_outlet->OutletID;?>">
                                    <?= $detail_outlet->NamaOutlet.' '.$detail_outlet->AlamatOutlet; ?>
                                </a>
                            </li>
                            <li class="active">
                                Edit Outlet <?= $detail_outlet->NamaOutlet.' '.$detail_outlet->AlamatOutlet; ?>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>

    <div class="row">
        <form action="<?= base_url('perusahaan/editoutlet')?>" method="POST">
        	<input type="hidden" name="outlet_id" value="<?=$detail_outlet->OutletID;?>">
            <div class="col-md-8 center-block no-float">
                                <div class="box-widget widget-module">
                    <div class="widget-container">
                        <div class="widget-head clearfix">
                            <h4>Data Outlet</h4>
                        </div>

                        <div class="widget-block form-horizontal">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Nama</label>
                                <div class="col-xs-12 col-sm-7">
                                    <input type="text" value="<?=$detail_outlet->NamaOutlet;?>" class="form-control" name="namaoutlet">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Alamat</label>
                                <div class="col-xs-12 col-sm-7">
                                    <input type="text" value="<?=$detail_outlet->AlamatOutlet;?>" class="form-control" name="alamatoutlet">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Provinsi</label>
                                <div class="col-xs-12 col-sm-7">
                                    <select class="form-control" onchange="myFunction(this)" name="provinsioutlet" id="provinsi">
                                    	
                                    </select>
                                    <!-- <input type="text" class="form-control" name="provinsioutlet"> -->
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Kota</label>
                                <div class="col-xs-12 col-sm-7">
                                    <!-- <input type="text" class="form-control" id="kota" name="kotaoutlet"> -->
                                    <select class="form-control" id="kota" name="kotaoutlet">
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">No Telepon</label>
                                <div class="col-xs-12 col-sm-7">
                                    <input type="text" value="<?=$detail_outlet->MobilePhone;?>" class="form-control" name="notelpoutlet">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Pemilik Outlet</label>
                                <div class="col-xs-12 col-sm-7">
                                    <select class="form-control" name="pemilikoutlet">
                                    	<option value="">Pemilik Outlet</option>
                                        <?php foreach ($userperusahaan as $userperusahaan_item): ?>
                                           <option <?php if($detail_outlet->PemilikOutlet == $userperusahaan_item->iduserperusahaan): echo 'selected="selected"'; endif;?> value="<?=$userperusahaan_item->iduserperusahaan;?>">
                                        		<?=$userperusahaan_item->username;?>
                                           </option>;
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row text-right">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>