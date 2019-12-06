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
                                               class="btn btn-primary" onclick="redirectTonewItem()"/>
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
                <h4>Uang Keluar</h4>
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
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Ambil Dari</th>
                                <th>Dibayar Ke</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th></th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($datagrid['result'] as $row) {
                            $jam = "";
                            $time = "";
                            $no = "";
                            $id = -1;
                            ?>
                            <tr>
                                <?php foreach ($datagrid['fields'] as $field) { ?>
                                    <?php
                                    $fieldname = $field->name;
                                    $lowerfieldname = strtolower($fieldname);
                                    if ($lowerfieldname == 'transactionid') {

                                        $jam = $row->$fieldname;
                                        continue;
                                    }
                                    if ($lowerfieldname == 'transactiontime') {

                                        $time = $row->$fieldname;
                                        continue;
                                    }
                                    if ($lowerfieldname == 'transactionnumber') { ?>
                                        <td>
                                            <?php
                                            $no = $row->$fieldname;
                                            echo $row->$fieldname;

                                            ?>
                                        </td>
                                        <?php
                                    }


                                    if ($lowerfieldname == 'transactiondate') { ?>
                                        <td>
                                            <?php
                                            $th = substr($row->$fieldname, 0, 4);
                                            $bl = substr($row->$fieldname, 5, 2);
                                            if ($bl == '01') {
                                                $bl = "Jan";

                                            } else if ($bl == '02') {
                                                $bl = "Feb";

                                            } else if ($bl == '03') {
                                                $bl = "Mar";

                                            } else if ($bl == '04') {
                                                $bl = "Apr";

                                            } else if ($bl == '05') {
                                                $bl = "Mei";

                                            } else if ($bl == '06') {
                                                $bl = "Jun";

                                            } else if ($bl == '07') {
                                                $bl = "Jul";

                                            } else if ($bl == '08') {
                                                $bl = "Agu";

                                            } else if ($bl == '09') {
                                                $bl = "Sep";

                                            } else if ($bl == '10') {
                                                $bl = "Okt";

                                            } else if ($bl == '11') {
                                                $bl = "Nov";

                                            } else if ($bl == '12') {
                                                $bl = "Des";

                                            }
                                            $tg = substr($row->$fieldname, 8, 2);

                                            echo $tg . ' ' . $bl . ' ' . $th . ',<br/> ';
                                            echo $time;


                                            ?>

                                        </td>
                                    <?php }
                                    if ($lowerfieldname == 'accountid') { ?>
                                        <td>
                                            <?php

                                            echo "Kasir";


                                            ?>
                                        </td>
                                        <?php
                                    }
                                    if ($lowerfieldname == 'paidto') { ?>
                                        <td>
                                            <?php

                                            echo $row->$fieldname;


                                            ?>
                                        </td>
                                        <?php
                                    }
                                    if ($lowerfieldname == 'note') { ?>
                                        <td>
                                            <?php

                                            echo $row->$fieldname;


                                            ?>
                                        </td>
                                        <?php
                                    }
                                    if ($lowerfieldname == 'amount') { ?>
                                        <td>
                                            <?php

                                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);


                                            ?>
                                        </td>
                                        <?php
                                    }

                                } ?>
                                <td>
                                    <a href="<?= base_url('Uang_keluar/editData?no=' . $no.'&outlet='.$selected_outlet); ?>"
                                       class="btn btn-default">Edit</a>
                                    <a href="<?= base_url('Uang_keluar/hapusData?no=' . $no.'&outlet='.$selected_outlet); ?>"
                                       class="btn btn-default" data-id="<?= $id; ?>"
                                       onClick="return confirm('Yakin Ingin Menghapus?')">Hapus</a>
                                </td>
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
