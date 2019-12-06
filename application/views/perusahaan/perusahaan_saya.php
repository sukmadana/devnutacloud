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
                        <!--                        <h2 class="breadcrumb-titles">Perusahaan -->
                        <? //= getLoggedInNamaPerusahaan(); ?><!--</h2>-->
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">

            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <span class="h-icon"><i class="fa fa-slack"></i></span>
                        <h4>ID Perusahaan</h4>
                    </div>
                    <div class=" widget-block">
                        <h1><?= getLoggedInUserID(); ?>
                            <small></small>
                        </h1>
                        <br/><br/>
                    </div>
                </div>
            </div>


            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <span class="h-icon"><i class="fa fa-share-alt"></i></span>
                        <h4>Outlet</h4>
                    </div>
                    <div class=" widget-block">
                        <div class="w-info-chart-meta">

                            <?php foreach ($daftardevice as $k => $d) { ?>
                                <div class="progress-wrap">
                                    <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label"><p>
                                                <?= ($k + 1) . '. '; ?>
                                                <strong><?= $d->NamaOutlet; ?></strong> <?= $d->AlamatOutlet; ?>
                                            </p></span>
                                        <span class="pull-right">
<?php if (trim($d->DeviceIDAktif) !== '') { ?>
    <input type="checkbox" class="switch-small"
           onchange="setOutletNonAktif(<?= $d->OutletID . ",'" . $d->NamaOutlet . "'"; ?>,this)"
           checked
    />
<?php } ?>
                                            <a class="btn btn-default"
                                               href="<?= base_url(); ?>perusahaan/outletdetail?x=<?= $d->OutletID; ?>">Edit</a>
                                            <form method="post" action="<?= base_url('perusahaan/hapusoutlet'); ?>"
                                                  class="form-inline" style="float:right;margin-left:1px;">
                                                <input type="hidden" value="<?= $d->OutletID; ?>" name="i"/>
                                                <button type="submit"
                                                        class="btn btn-default"
                                                        onclick="deleteOutlet(this)">Hapus
                                                </button>
                                            </form>
                                        </span>
                                    </div>

                                    <div class="progress">
                                        <div style="width: 100%;" class="progress-bar progress-bar-danger">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <a href="<?= base_url(); ?>perusahaan/newoutlet" class="btn btn-primary">Tambah Outlet</a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <span class="h-icon"><i class="fa fa-users"></i></span>
                        <h4>Staf</h4>
                    </div>
                    <div class=" widget-block">
                        <div class="w-info-chart-meta">
                            <?php foreach ($daftaruser as $k => $d) { ?>
                                <div class="progress-wrap">
                                    <div class="clearfix progress-meta">
                                        <span style="font-size:14px;" class="pull-left progress-label">

                                                <?= ($k + 1) . '. '; ?>
                                            <strong><?= $d->username; ?></strong> <br/><?= $d->email; ?>

                                        </span>
                                        <?php if ($d->IsOwner == 1) { ?>
                                            <span class="pull-right progress-percent label label-info">Owner</span>
                                        <?php } else { ?>
                                            <span class="pull-right">
                                                <input type="checkbox" onchange="swichOnOffChanged(this)"
                                                       data-tag="<?= $d->username; ?>"
                                                       class="switch-small" <?= $d->IsAktif == 1 ? 'checked' : ''; ?> />
                                                <a href="<?= base_url() . 'perusahaan/userdetail?x=' . $d->username; ?>"
                                                   class="btn btn-default">Edit</a>
                                            </span>
                                        <?php } ?>
                                    </div>

                                    <div class="progress">
                                        <div style="width: 100%;" class="progress-bar progress-bar-success">
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <a href="<?= base_url(); ?>perusahaan/userform" class="btn btn-primary">Tambah User
                                Perusahaan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
