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
                        <h2 class="breadcrumb-titles">Registrasi Perusahaan</h2>
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
                echo 'style="opacity:0;"';
            } ?>>
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
                        <form class="form-horizontal" action="<?= base_url(); ?>perusahaan/registrasi" method="post"
                              id="form-registrasi-perusahaan">
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="namaperusahaan"><span class="required">Nama Perusahaan</span>
                                </label>

                                <div class="col-md-8">
                                    <input id="namaperusahaan" name="namaperusahaan" class="form-control required"
                                           type="text">
                                    <label for="namaperusahaan" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="namapemilik"><span class="required">Pemilik Perusahaan</span>
                                </label>

                                <div class="col-md-8">
                                    <input id="namapemilik" name="namapemilik" class="form-control required"
                                           type="text">
                                    <label for="namapemilik" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" for="email"><span class="required">Email Perusahaan</span>
                                </label>

                                <div class="col-md-8">
                                    <input id="email" name="email" class="form-control required email" type="text"
                                           value="<?= $email; ?>">
                                    <label for="email" class="error"><span class="error-ins"></span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">
                                </label>

                                <div class="col-md-8">
                                    <input type="submit" class="btn btn-primary" value="Daftar" name="submittriger">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6"></div>
    </div>



