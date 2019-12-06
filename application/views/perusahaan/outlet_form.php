<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <a href="<?= base_url(); ?>perusahaan/outlet" class="btn btn-default"><i
                                class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box-widget widget-module" <?php if (count($error) == 0) {

            } ?>>
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-user"></i></span>
                    <h4><?= $form_mode; ?></h4>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <?php if (count($error) > 0) {
                            foreach ($error as $e) {
                                ?><p style="color:red;">
                                <?= $e; ?>
                                </p><?php
                            }
                            ?>

                        <?php } ?>
                        <form class="form-horizontal" action="<?= $action_url; ?>" method="post"
                              id="form-detail-outlet">
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="namaoutlet"><span class="required">Nama Outlet</span>
                                </label>

                                <div class="col-md-8">
                                    <input id="namaperusahaan" name="namaoutlet" class="form-control required"
                                           type="text" value="<?= $nama_outlet; ?>">
                                    <label for="namaperusahaan" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="alamatoutlet"><span class="required">Alamat Outlet</span>
                                </label>

                                <div class="col-md-8">
                                    <input id="alamatoutlet" name="alamatoutlet" class="form-control required"
                                           type="text" value="<?= $alamat_outlet; ?>">
                                    <label for="alamatoutlet" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-8 control-label" for="bisadownload"><span class="required">Data Master bisa diambil</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="bisadownload"
                                           class="switch-small" <?= $bisadownload == 1 ? 'checked' : ''; ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-8 control-label" for="modulpembelian"><span class="required">Modul Pembelian</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="modulpembelian"
                                           class="switch-small" <?= $PurchaseModule == 1 ? 'checked' : ''; ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-8 control-label" for="modulstok"><span class="required">Modul Stok</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="modulstok"
                                           class="switch-small" <?= $StockModule == 1 ? 'checked' : ''; ?>
                                           onchange="stokChange(this);"
                                    />
                                </div>
                            </div>
                            <div class="form-group" id="divstokbahan"
                                <?php if ($StockModule == 0) echo 'style="display: none"'; ?>>
                                <label class="col-md-8 control-label" for="modulstokbahan"><span class="required">Stok Bahan</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="modulstokbahan"
                                           class="switch-small" <?= $MenuRacikan == 1 ? 'checked' : ''; ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-8 control-label" for="modulvariasiharga"><span class="required">Variasi Item dan Harga</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="modulvariasiharga" id="modulvariasiharga"
                                           class="switch-small" <?= $PriceVariation == 1 ? 'checked' : ''; ?>
                                           onchange="variasiChange(this);"
                                    />
                                </div>
                            </div>
                            <div class="form-group" id="divstokmodifier"
                                <?php if ($PriceVariation == 1) echo '';
                                else echo 'style="display: none"'; ?>>
                                <label class="col-md-8 control-label" for="modulstokmodifier"><span class="required">Stok Pilihan Ekstra</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="modulstokmodifier"
                                           class="switch-small" <?= $StockModifier == 1 ? 'checked' : ''; ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-8 control-label" for="strukviaemail"><span class="required">Kirim Struk via Email/SMS</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="strukviaemail"
                                           class="switch-small" <?= $SendReceiptToCustomerViaEmail == 1 ? 'checked' : ''; ?>
                                    />
                                </div>
                            </div>
                            <div class="form-group" style="display: none">
                                <label class="col-md-8 control-label" for="fiturmeja"><span class="required">Meja</span>
                                </label>

                                <div class="col-md-4">
                                    <input type="checkbox" name="fiturmeja"
                                           class="switch-small" <?= $DiningTable == 1 ? 'checked' : ''; ?>
                                    />
                                </div>
                            </div>
                            <input type="hidden" name="id" value="<?= $id_outlet; ?>"/>
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                </label>

                                <div class="col-md-8">
                                    <input type="submit" class="btn btn-primary" value="Simpan" name="submittriger">
                                </div>
                            </div>
                            <script>
                                function stokChange(checkbox) {
                                    if(checkbox.checked == true){
                                        document.getElementById("divstokbahan").removeAttribute("style");
                                        if(document.getElementById("modulvariasiharga").checked == true) {
                                            document.getElementById("divstokmodifier").removeAttribute("style");
                                        }
                                    }else{
                                        document.getElementById("divstokbahan").setAttribute("style", "display: none;");
                                        document.getElementById("divstokmodifier").setAttribute("style", "display: none;");
                                    }
                                }
                                function variasiChange(checkbox) {
                                    if(checkbox.checked == true){
                                        if(document.getElementById("modulvariasiharga").checked == true) {
                                            document.getElementById("divstokmodifier").removeAttribute("style");
                                        }
                                    }else{
                                        document.getElementById("divstokmodifier").setAttribute("style", "display: none;");
                                    }
                                }
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>



