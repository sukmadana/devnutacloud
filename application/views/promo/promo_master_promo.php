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
                                                <option value="-999" <?=$selected_outlet==-999?'selected':''?>>&nbsp;</option>
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
                                    <?php if ($visibilityMenu['PromoNew']) { ?>
                                        <input type="button" value="Tambah Promo"
                                            class="btn btn-primary" onclick="redirectTonewPromo()"/>
                                    <?php } ?>
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
                <h4>Promo</h4>
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
                                        if ($lowerfieldname == 'promoid') {
                                            continue;
                                        } else {?>
                                        <th>
                                            <?= CamelToWords($field->name); ?>
                                        </th>
                                    <?php }
                                } ?>
                                    <th></th>
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
                                        if ($lowerfieldname == 'promoid') {
                                            continue;
                                        } else {
                                            if ($lowerfieldname == 'judul') {
                                                $id = $row->$fieldname;
                                            }?>
                                            <?php
                                                $hari = explode(" ",$row->HariBerlaku);
                                                $a = 0;
                                                $c = array();
                                                if ($fieldname == "HariBerlaku") {
                                                    for ($i=0; $i < 7; $i++) {
                                                        if ($hari[$i] != '0') {
                                                            $c[] = $hari[$i];
                                                        }
                                                    }

                                                    if (count($c) == 7) {
                                                        echo "<td>Setiap Hari</td>";
                                                    } else {
                                                        echo "<td><ul>";
                                                        for ($i=0; $i < count($c); $i++) {
                                                            echo "<li>".$c[$i]."</li>";
                                                        }
                                                        echo "</ul></td>";
                                                    }

                                                } else if ($fieldname == 'PeriodeBerlaku') {
                                                    $tanggal = explode(" - ",$row->PeriodeBerlaku);
                                                    $date1 = formatdateindonesia($tanggal[0]);
                                                    $date2 = formatdateindonesia($tanggal[1]);
                                                    echo "<td>".$date1." - ".$date2."</td>";
                                                } else {
                                                    echo "<td>".$row->$fieldname."</td>";
                                                }
                                            }?>
                                    <?php } ?>
                                     <td>
                                         <a href="<?= base_url('promo/promoform?promoid=' . urlencode($id) . '&outlet=' . $selected_outlet); ?>"
                                            class="btn btn-default">Edit</a>
                                         <a href="#" class="btn btn-default" data-toggle="modal" data-id="<?= $id; ?>"
                                            data-target="#hapus-promo-modal">Hapus</a>
                                     </td>
                                     <?php } ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view('features/dialogs/dialog_hapus_promo'); ?>
    </div>
</div>
