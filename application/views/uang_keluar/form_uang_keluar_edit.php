<?php
/**
 * Created by Sublime.
 * User: zymWorks
 * Date: 10/05/2017
 * Time: 17:45
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

                        <form class="form-horizontal" id="form-outlet">
                            <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>

                            <div class="col-md-6">
                                <a href="<?= base_url('Uang_keluar/data?outlet=' . $selected_outlet); ?>"
                                   class="btn btn-default">Kembali</a>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pull-right col-md-12">
                                    <div class="col-md-3">
                                        <label
                                                class="control-label"> </label>
                                    </div>
                        </form>

                        <div class="col-md-9">
                            <select class="form-control" name="outlet" id="outlet"
                                    onchange="document.getElementById('form-outlet').submit()">
                                <?php foreach ($outlets as $key => $outlet): ?>
                                    <option value="<?= $key ?>" <?= $_GET['outlet'] == $key ? "selected" : "" ?>>
                                        <?= str_replace('#$%^', ' ', $outlet); ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>

                    </div>
                </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <div>
        <?php
        $jum = "";
        $bay = "";
        $ket = "";
        foreach ($uangKeluar->result() as $row):
            $id = $row->TransactionID;
            $jum = $row->Amount;
            $bay = $row->PaidTo;
            $ket = $row->Note;

        endforeach;
        ?>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-form"></i></span>
                    <h4>Edit Uang Keluar</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">

                        <form class="form-horizontal" id="Form" method="post"
                              action="<?= base_url('uang_keluar/simpanData?edit=' . $id); ?>"
                              enctype="multipart/form-data" id="form-item">
                            <div class="form-group">
                                <label class="col-md-4 control-label">Ambil Dari</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <select class="form-control" name="ambilDari" id="list-satuan">
                                                <option value="Kasir" data-tag="">Kasir</option>


                                            </select>
                                        </div>

                                    </div>

                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-4 control-label">Dibayar Ke</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-item"
                                                   name="dibayarke" value="<?= $bay; ?>" required/>

                                            <input type="hidden" name="idOutlet" value="<?= $selected_outlet; ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Jumlah</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="number" class="form-control" id="txt-item"
                                                   name="jumlah" value="<?= $jum; ?>" required/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Keterangan</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-item"
                                                   name="keterangan" value="<?= $ket; ?>" required/>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Jenis</label>
                                <div class="col-md-8">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="optionsJenis" id="optionsRadios1" value="Biaya"
                                                   checked>
                                            Biaya
                                        </label> <br/>

                                        <label>
                                            <input type="radio" name="optionsJenis" id="optionsRadios1"
                                                   value="Non Biaya">
                                            Non Biaya
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">&nbsp;</label>
                                <div class="col-md-2">
                                </div>

                                <div class="col-md-4">
                                    <div class="form-actions">
                                        <a href="<?= base_url('Uang_keluar/data?outlet=' . $selected_outlet); ?>"
                                           class="btn btn-default">Cancel</a>

                                        <button type="submit" class="btn btn-primary  has-spinner"
                                                id="btn-simpan-single-outlet">
                                            <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>
                                            Simpan
                                        </button>

                                    </div>


                                </div>
                            </div>
                        </form>
                    </div>


                </div>
            </div>
        </div>


    </div>
</div>

