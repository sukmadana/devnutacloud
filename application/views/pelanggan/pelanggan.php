<style type="text/css">
    .row {
        margin: 0;
    }

    .center {
        text-align: center;
    }

    .tb5 {
        margin-top: 5px;
        margin-bottom:10px;
    }

    .dt-buttons {
        text-align: right;
    }

    .dt-button {
        padding: 5px 8px;
        border: 1px solid #999;
        border-radius: 2px;
        -webkit-box-shadow: 1px 1px 3px #ccc;
        -moz-box-shadow: 1px 1px 3px #ccc;
        -ms-box-shadow: 1px 1px 3px #ccc;
        -o-box-shadow: 1px 1px 3px #ccc;
        box-shadow: 1px 1px 3px #ccc;
        background: -webkit-linear-gradient(top,#fff 0,#f3f3f3 89%,#f9f9f9 100%);
    }

    td {

        vertical-align: middle !important;

    }

    .table-hapus td, .table-hapus th {
        border-top: 0px !important;
    }

    .table-hapus th {
        border-bottom: 1px solid #dddddd;
    }

    table.table-bordered.dataTable {
        border-collapse: collapse !important;
    }
</style>

<?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <div class="page-breadcrumb-wrap" style="width: 100%">
                    <div class="page-breadcrumb-info">
                        <form class="form-horizontal pull-right" id="pelanggan-add" method="get" action="<?= base_url() ?>pelanggan/form">
                            <div class="form-group col-md-12">

                                <div class="col-md-2">
                                    <label class="control-label">Outlet</label>
                                </div>

                                <div class="col-md-6">
                                    <select class="form-control" name="outlet" id="outlet" required>
                                        <option value=""></option>
                                        <?php 
                                        foreach ($outlets as $k => $v) { ?>
                                            <option value="<?= $k ?>" <?= $k == $selected_outlet ? "selected" : "" ?>>
                                                <?= str_replace('#$%^', ' ', $v); ?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button id="btnTambah" type="button" value="Tambah"
                                        <?php if ($visibilityMenu['CustomerAdd']) { ?>
                                            class="btn btn-primary"
                                        <?php } else { ?>
                                            class="btn btn-default"
                                        <?php } ?>/>
                                    Tambah Pelanggan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Daftar Pelanggan</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i
                                class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class="widget-block">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="grid-item">
                            <thead>
                                <tr>
                                    <th style="text-align: center">
                                        Nama
                                    </th>
                                    <th style="text-align: center">
                                        Email
                                    </th>
                                    <th style="text-align: center">
                                        No. HP
                                    </th>
                                    <th style="text-align: center">
                                        Tgl Lahir
                                    </th>
                                    <th style="text-align: center">
                                        Alamat
                                    </th>
                                    <?php if ($visibilityMenu['CustomerEdit'] || $visibilityMenu['CustomerDelete']) { ?>
                                    <th style="min-width:110px"></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
