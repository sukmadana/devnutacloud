<style type="text/css">
    td {

        vertical-align: middle !important;

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

                        <form class="form-horizontal pull-right" id="datarekening-add" method="get"
                              action="<?= base_url() ?>datarekening/form">
                            <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                                <div class="form-group col-md-12">
                                    <div class="col-md-2">
                                        <label class="control-label">Outlet</label>
                                    </div>

                                    <div class="col-md-6">
                                        <select class="form-control" name="outlet" id="outlet" onchange="selectinge()"
                                                required>
                                            <option></option>
                                            <?php foreach ($outlets as $k => $v) { ?>
                                                <option value="<?= $k ?>" <?= $k == $selected_outlet ? "selected" : "" ?>>
                                                    <?= str_replace('#$%^', ' ', $v); ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" value="Tambah"
                                            <?php if ($visibilityMenu['DataRekeningAdd']) { ?>
                                                class="btn btn-primary"
                                            <?php } else { ?>
                                                class="btn btn-default"
                                            <?php } ?> onclick="validaten()"/>
                                        Tambah Rekening</button>
                                    </div>

                                </div>
                            <?php } ?>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Rekening</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class=" widget-block">
                    <input type="text" placeholder="Cari Item" class="form-control"
                           id="search-item"/>
                    <div class="table-responsive">
                        <table class="table table-bordered  table-striped " id="grid-item">
                            <thead>
                            <tr>
                                <?php foreach ($datagrid['fields'] as $field) {
                                    $fieldname = $field->name;
                                    $lowerfieldname = strtolower($fieldname);
                                    if ($lowerfieldname == 'accountid' || $lowerfieldname == 'deviceno') {
                                        continue;
                                    }
                                    if ($field->name == "Nama") {
                                        ?>
                                        <th style="text-align: center">
                                            <?= CamelToWords($field->name); ?>
                                        </th>
                                    <?php } else {
                                        ?>

                                        <th>
                                            <?= CamelToWords($field->name); ?>
                                        </th>
                                    <?php }
                                } ?>
                                <?php if ($visibilityMenu['DataRekeningEdit'] || $visibilityMenu['DataRekeningDelete']) { ?>
                                    <th></th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($datagrid['result'] as $key => $row) {
                            $id = -1;
                            $devno = 0;
                            ?>
                            <tr>
                                <?php
                                foreach ($datagrid['fields'] as $field) { ?>

                                    <?php
                                    $fieldname = $field->name;
                                    $lowerfieldname = strtolower($fieldname);
                                    if ($lowerfieldname == 'accountid') {
                                        $id = $row->$fieldname;
                                    } else if($lowerfieldname == 'deviceno') {
                                        $devno = $row->$fieldname;
                                    } else { ?>
                                        <td>
                                            <?php
                                            echo $row->$fieldname;
                                            ?>
                                        </td>
                                        <?php
                                    }
                                } ?>

                                <?php if ($visibilityMenu['DataRekeningEdit'] || $visibilityMenu['DataRekeningDelete']) { ?>
                                    <td>
                                        <?php if ($visibilityMenu['DataRekeningEdit'] && ($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98)) { ?>
                                            <a href="<?= base_url('datarekening/form?id=' . urlencode($id) . '&outlet=' . $selected_outlet. '&devno=' . $devno); ?>"
                                               class="btn btn-default">Edit</a>
                                        <?php }
                                        if ($visibilityMenu['DataRekeningDelete'] && ($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98)) { ?>
                                            <a class="btn btn-default delete-button"
                                               data-message="Hapus Rekening ini? jika ya dihapus, jika tidak biarkan."
                                               data-target=".delete-form-<?= $row->AccountID . "-" . $row->DeviceNo ?>">Hapus</a>
                                            <form action="<?php echo base_url('datarekening/destroy'); ?>" method="post"
                                                  class="hidden delete-form-<?= $row->AccountID. "-" . $row->DeviceNo ?>">
                                                <input type="number" name="outlet" value="<?= $selected_outlet ?>">
                                                <input type="number" name="id" value="<?= $row->AccountID ?>">
                                                <input type="number" name="devno" value="<?= $row->DeviceNo ?>">
                                            </form>
                                        <?php } ?>
                                    </td>
                                <?php } ?>
                                <?php } ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>