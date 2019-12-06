<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 16/05/2016
 * Time: 17:45
 */
?>
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

                        <form class="form-horizontal pull-right">
                            <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                                <div class="form-group col-md-12">
                                    <div class="col-md-2">
                                        <label class="control-label">Outlet</label>
                                    </div>

                                    <div class="col-md-6">
                                        <select class="form-control" name="outlet" id="outlet">
                                            <?php
                                            if (count($outlets) > 1) { ?>
                                                <option value="-999" <?= $selected_outlet == -999 ? 'selected' : '' ?>>
                                                    &nbsp;
                                                </option>
                                            <?php }
                                            foreach ($outlets as $k => $v) { ?>
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

                                    <div class="col-md-4">
                                        <input type="button" value="Tambah item"
                                            <?php if ($visibilityMenu['ItemAdd']) { ?>
                                                class="btn btn-primary"
                                            <?php } else { ?>
                                                class="btn btn-default"
                                            <?php } ?>
                                               onclick="redirectTonewItem()"/>
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
                <h4>Item</h4>
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
                                    if ($lowerfieldname == 'itemid') {
                                        continue;
                                    }
                                    if ($field->name === "Foto") {
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
                                <?php if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemDelete']) { ?>
                                    <th></th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($datagrid['result'] as $row) {
                            $id = -1;
                            ?>
                            <tr>
                                <?php foreach ($datagrid['fields'] as $field) { ?>
                                    <?php
                                    $fieldname = $field->name;
                                    $lowerfieldname = strtolower($fieldname);
                                    if ($lowerfieldname == 'itemid') {
                                        continue;
                                    }
                                    if ($lowerfieldname == 'nama') {
                                        $id = $row->$fieldname;
                                    }

                                    if ($lowerfieldname === 'foto') { ?>
                                        <td align="center" width="80">
                                    <span class="user-thumb">
                                        <img
                                                src="
                                            <?php
                                                if (isNotEmpty($row->$fieldname)) {
                                                    echo $this->config->item('ws_base_url') . $row->$fieldname;
                                                } else {
                                                    echo base_url('images/no-image.png');
                                                }
                                                ?>"
                                                alt="foto" align="center" style="width:40px;height:40px"/>
                                    </span>
                                        </td>
                                    <?php } else { ?>
                                        <td><?php
                                            if ($lowerfieldname == 'hargajual') {
                                                if ($row->ProdukAtauBahan == 'Produk') {
                                                    echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                                                } else {
                                                    echo "-";
                                                }
                                            } else {
                                                echo $row->$fieldname;
                                            } ?></td>

                                    <?php }
                                } ?>
                                <?php if ($visibilityMenu['ItemEdit'] || $visibilityMenu['ItemDelete']) { ?>
                                    <td>
                                    <?php if ($visibilityMenu['ItemEdit']) { ?>
                                        <a href="<?= base_url('transaksi/itemform?id=' . urlencode($id) . '&outlet=' . $selected_outlet); ?>"
                                           class="btn btn-default">Edit</a>
                                    <?php } if ($visibilityMenu['ItemDelete']) { ?>
                                        <a href="#" class="btn btn-default" data-toggle="modal" data-id="<?= $id; ?>"
                                           data-target="#hapus-item-modal">Hapus</a>
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
        <?php $this->load->view('webparts/parts/dialog_hapus_item'); ?>
    </div>
    <?php $this->load->view('webparts/parts/dialog_kategori'); ?>
</div>
