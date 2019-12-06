<?php
/**
 * Created by PhpStorm.
 * User: ANDROMEDA
 * Date: 14/06/2017
 * Time: 14.38
 */
?>
<style type="text/css">
    td {
        vertical-align: middle !important;
        padding-top: 5px;
        padding-bottom: 5px;
        padding-left: 2px;
        padding-right: 2px;

    }

    .table-hapus td, .table-hapus th {
        border-top: 0px !important;
    }

    .table-hapus th {
        border-bottom: 1px solid #dddddd;
    }
</style>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <div class="page-breadcrumb-wrap" style="width: 100%">
                    <div class="page-breadcrumb-info">

                        <form class="form-horizontal">
                            <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>

                                <div class="col-md-6">
                                    <a href="<?= base_url('datarekening/index'); ?>" class="btn btn-default">Kembali</a>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group pull-right col-md-12">
                                        <div class="col-md-3">
                                            <label class="control-label">Outlet</label>
                                        </div>

                                        <div class="col-md-9">
                                            <select class="form-control" name="outlet" id="outlet">
                                                <?php foreach ($outlets as $k => $v) { ?>
                                                    <?php if ($k == $selected_outlet) { ?>
                                                        <option value="<?= $k; ?>"
                                                                selected=""><?= str_replace('#$%^', ' ', $v); ?></option>
                                                    <?php } else { ?>
                                                        <option
                                                                value="<?= $k; ?>"><?= str_replace('#$%^', ' ', $v); ?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </select>

                                        </div>

                                    </div>
                                </div>
                            <?php } ?>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-shopping-basket"></i></span>
                    <h4>Rekening</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form class="form-horizontal" method="post" action="<?= base_url('supplier'); ?>" id="form-supplier">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Bank</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt_bankName"
                                                   value="<?= $form['bankName']; ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">No.Rekening</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt_accountNumber"
                                                   value="<?= $form['accountNumber']; ?>" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Atas Nama</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt_accountName" value="<?= htmlentities($form['accountName']); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">&nbsp;</label>
                                <div class="col-md-8">
                                    <div class="form-actions">
                                        <button type="button" class="btn btn-primary has-spinner" id="btn-simpan">
                                            <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>
                                            Simpan
                                        </button>
                                        <a href="<?= base_url('datarekening/index');?>"><input type="button" class="btn btn-default" value="Cancel"/></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
