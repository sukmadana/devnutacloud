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
        <form action="<?= base_url('perusahaan/update_outlet_module') ?>" method="POST">
            <div class="col-md-8 center-block no-float">
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
                                            <input type="checkbox" <?php if ($detail_outlet->DataMasterBisaDiambil == '1') : echo "checked='checked'"; ?><?php endif ?> name="bisadownload" class="switch-small" />
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
                                            <input type="hidden" name="OutletID" value="<?= $outlet_options['OutletID'] ?>">
                                            <input type="checkbox" <?php if ($outlet_options['PurchaseModule'] == 'true') : echo "checked='checked'"; ?><?php endif ?> name="modulpembelian" class="switch-small" />
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
                                            <input id="stock-parent" type="checkbox" <?php if ($outlet_options['StockModule'] == 'true') : echo "checked='checked'"; ?><?php endif ?> name="modulstok" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="stock-bahan list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Stok Bahan</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin mengelola stok bahan dari nutacloud</p>
                                        </div>
                                        <div class="media-right">
                                            <input id="stock-bahan" type="checkbox" <?php if ($outlet_options['MenuRacikan'] == 'true') : echo "checked='checked'"; ?><?php endif ?> name="modulstokbahan" class="switch-small" />
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
                                            <input id="stock-variation" type="checkbox" <?php if ($outlet_options['PriceVariation'] == "1") : echo "checked='checked'"; ?><?php endif ?> name="modulvariasiharga" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                                <li class="stock-extra list-group-item">
                                    <div class="media">
                                        <div class="media-body">
                                            <p>Stok Pilihan Ekstra</p>
                                            <p class="text-mute">Nyalakan pengaturan ini bila ingin mengelola stok pilihan ekstra dari nutacloud</p>
                                        </div>
                                        <div class="media-right">
                                            <input type="checkbox" <?php if ($outlet_options['StockModifier'] == "1") : echo "checked='checked'"; ?><?php endif ?> name="modulstokmodifier" class="switch-small" />
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
                                            <input type="checkbox" <?php if ($outlet_options['SendReceiptToCustomerViaEmail'] == "1") : echo "checked='checked'"; ?><?php endif ?> name="strukviaemail" class="switch-small" />
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
                                            <input type="checkbox" <?php if ($outlet_options['SupportBarcode'] == "true") : echo "checked='checked'"; ?><?php endif ?> name="supportbarcode" class="switch-small" />
                                        </div>
                                    </div>
                                </li>
                            </ul>
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