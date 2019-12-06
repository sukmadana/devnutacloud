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
                                    <a href="<?= base_url('bahan/index?outlet=' . $selected_outlet); ?>"
                                       class="btn btn-default">Kembali</a>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group pull-right col-md-12">
                                        <div class="col-md-3">
                                            <label
                                                class="control-label"><?= $modeform == 'new' ? 'Outlet' : 'Ubah item di Outlet'; ?></label>
                                        </div>

                                        <div class="col-md-9">
                                            <select class="form-control" name="outlet" id="outlet">
                                                <?php foreach ($outlets_by_item as $k => $v) { ?>
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
                    <span class="h-icon"><i class="fa fa-form"></i></span>
                    <h4>Bahan</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <form class="form-horizontal" method="post" action="<?= base_url('bahan'); ?>"
                              enctype="multipart/form-data" id="form-item">
                            <div class="form-group">
                                <label class="col-md-4 control-label">&nbsp;</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-2">
                                            <img src="<?= $urlfoto; ?>"
                                                 style="width:102px;height:102px;margin-bottom:5px" id="prevImage"/>
                                        </div>
                                        <div class="col-xs-12 col-md-2">
                                            <div class="row">
                                                <div class="col-xs-12 col-md-1">
                                                    <input type="file" class="filestyle"
                                                           data-input="false"
                                                           name="foto"
                                                           data-iconName="fa fa-paperclip"
                                                           data-buttontext="Pilih Gambar" id="foto">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-12 col-md-1">
                                                    <button class="btn btn-default" type="button" id="btn-hapus-gambar"
                                                            style="margin-top:5px;">
                                                        <span class="fa fa-trash"></span>Hapus

                                                    </button>
                                                    <input type="hidden" name="ext" id="extfoto"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label" id="label-nama-item">Nama</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-item"
                                                   value="<?= $form['nama item']; ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Kategori</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <select class="form-control" id="list-kategori">
                                                <option value=""></option>
                                                <?php foreach ($kategories as $k => $v) { ?>
                                                    <option value="<?= $v; ?>"
                                                            data-tag="<?= $v; ?>"
                                                        <?= $k == $form['kategori'] ? 'selected' : ''; ?>
                                                    ><?= $v; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-xs-12 col-md-6">

                                            <input type="button"
                                                   class="<?= $modeform == 'new' ? 'btn btn-default' : 'btn btn-primary'; ?>"
                                                   data-toggle="modal"
                                                   data-target="#hapus-kategori-modal" data-mode="Hapus"
                                                   id="btn-hapus-kategori"
                                                   value="Hapus" <?= $modeform == 'new' ? 'disabled' : ''; ?>/>
                                            <input type="button"
                                                   class="<?= $modeform == 'new' ? 'btn btn-default' : 'btn btn-primary'; ?>"
                                                   data-toggle="modal"
                                                   data-target="#kategori-modal" data-mode="Edit" id="btn-edit-kategori"
                                                   value="Edit" <?= $modeform == 'new' ? 'disabled' : ''; ?>/>

                                            <input type="button" class="btn btn-primary" data-toggle="modal"
                                                   data-target="#kategori-modal" data-mode="Tambah"
                                                   id="btn-tambah-kategori" value="Tambah"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label"id="label-satuan">Satuan</label>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-xs-12 col-md-6">
                                            <input type="text" class="form-control" id="txt-satuan"
                                                   value="<?= $form['satuan']; ?>"/>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php if ($option->PurchaseModule == 'true') { ?>
                                <div class="form-group" id="container-harga-beli">
                                    <label class="col-md-4 control-label">Harga Beli</label>
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-xs-12 col-md-6">
                                                <input type="number" class="form-control" id="txt-harga-beli"
                                                       min="0"
                                                       onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))"
                                                       value="<?= $form['harga beli']; ?>"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group" style="display: none">
                                <label class="col-md-4 control-label">Item ini termasuk jenis produk atau
                                    bahan</label>
                                <div class="col-md-8">
                                    <div class="radio" style="display: inline-block !important;">
                                        <label>
                                            <input type="radio" name="jenisproduk" value="true">Produk
                                        </label>
                                    </div>
                                    <div class="radio" style="display: inline-block !important;margin-left:10px;">
                                        <label>
                                            <input type="radio" name="jenisproduk" value="false" checked>Bahan
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">&nbsp;</label>
                                <div class="col-md-8">
                                    <div class="form-actions">
                                        <a href="<?= base_url('bahan/index?outlet=' . $selected_outlet); ?>"
                                           class="btn btn-default">Cancel</a>
                                        <input type="button" class="btn btn-primary"
                                               data-toggle="modal"
                                               data-target="#simpan-item-modal" value="Simpan ke beberapa outlet"/>
                                        <button type="button" class="btn btn-primary  has-spinner"
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

        <?php $this->load->view('features/dialogs/dialog_kategori'); ?>
        <?php $this->load->view('features/dialogs/dialog_satuan'); ?>
        <?php $this->load->view('features/dialogs/dialog_hapus_kategori'); ?>
        <?php $this->load->view('features/dialogs/dialog_hapus_satuan'); ?>
        <?php $this->load->view('features/dialogs/dialog_simpan_beberapa_outlet'); ?>
        <?php $this->load->view('features/dialogs/dialog_variasi_harga', array('harga' => $form['harga jual'], 'variasi_harga' => $variasi_harga)); ?>
        <?php $this->load->view('features/dialogs/dialog_pilihan_ekstra', array('pilihan_ekstra' => $pilihan_ekstra, 'satuans' => $satuans, 'option' => $option)); ?>

    </div>
</div>

