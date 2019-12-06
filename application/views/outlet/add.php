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
                            <li class="active-page"> Tambah Outlet</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>

    <div class="row">
        <form id="form-registrasi-outlet" method="POST">
            <div class="col-md-6">
                <div class="box-widget widget-module">
                    <div class="widget-container">
                        <div class="widget-head clearfix">
                            <h4>Data Outlet</h4>
                        </div>

                        <div class="widget-block form-horizontal">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Nama</label>
                                <div class="col-xs-12 col-sm-7">
                                    <input type="text" class="form-control" name="namaoutlet">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Alamat</label>
                                <div class="col-xs-12 col-sm-7">
                                    <input type="text" class="form-control" name="alamatoutlet">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Provinsi</label>
                                <div class="col-xs-12 col-sm-7">
                                    <select class="form-control" id="provinsi" name="provinsioutlet">
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
                                    <input type="text" class="form-control" name="notelpoutlet">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-5 control-label">Pemilik Outlet</label>
                                <div class="col-xs-12 col-sm-7">
                                    <select class="form-control" name="pemilikoutlet">
                                        <?php
                                        foreach ($userperusahaan as $userperusahaan_item) : ?>
                                            <option
                                            <?php if (getLoggedInUsername() == strtolower($userperusahaan_item->username)): ?>
                                                <?php echo 'selected="selected"' ?>
                                            <?php endif ?>
                                            value='<?=$userperusahaan_item->iduserperusahaan?>'><?=$userperusahaan_item->username?></option>';
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

            <div class="col-md-6">
                <div class="box-widget widget-module">
                    <div class="widget-container">
                        <div class="widget-head clearfix">
                            <h4>Kelola Modul</h4>
                        </div>

                        <div class="widget-block form-horizontal">
                            <ul class="list-group setting-module">
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Data Master Bisa dicopy</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin data master bisa dicopy ke outlet lainnya.<br>ini sangat berguna bila ada outlet baru agar tidak perlu input data produk dari awal.</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="bisadownload" class="switch-small" />
                                        </div>
                                    </div>
                                </li>

                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Pembelian</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin meng input transaksi pembelian dari nutacloud</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="modulpembelian" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Stok</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin meng input transaksi stok dari nutacloud</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="modulstok" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Stok Bahan</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin mengelola stok bahan dari nutacloud</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="modulstokbahan" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Variasi Item dan Harga</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin mengelola variasi item dan Harga dari nutacloud</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="modulvariasiharga" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Stock Pilihan Extra</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin mengelola stok pilihan ekstra dari nutacloud</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="modulstokmodifier" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Kirim Struk Via Email dan SMS</p>
                                            <p class="text-mute">Nyalakan pengaturan agar aplikasi Nuta di tablet bisa kirim struk via email dan SMS</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="strukviaemail" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Barcode</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin bisa scan barcode</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" name="supportbarcode" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>