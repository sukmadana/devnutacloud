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
                                    <a href="<?= base_url('pilihanekstra/index?outlet=' . $selected_outlet); ?>"
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
                    <h4>Pilihan Ekstra</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <form class="form-horizontal" method="post" action="<?= base_url('pruduk'); ?>"
                              enctype="multipart/form-data" id="form-item">
                            <div class="modal-body">
                                <h4 class="modal-title text-center" id="kategori-modal-title">Tambah Pilihan Ekstra </h4>
                                    <table align="center" id="nuta-plihan-ekstra-table-form">
                                        <tr>
                                            <td colspan="5">
                                                <input placeholder="<?= $pilihan_ekstra['PlaceholderPilihan']; ?>" class="form-control"
                                                       type="text" id="namaEkstra">
                                            </td>
                                        </tr>
                                        <tr style="border: 1px solid #e5e5e5;border-radius: 3px;">
                                            <td colspan="4">Pelanggan hanya bisa pilih satu ekstra</td>
                                            <td align="right"><input type="checkbox" id="checkPilihSatu"
                                                                     class="switch-small" <?= $pilihan_ekstra['HanyaBisaPilihSatu'] == 'true' ? 'checked' : ''; ?>/>
                                            </td>
                                        </tr>
                                        <?php if ($option->StockModifier == 1) { ?>
                                            <tr style="border: 1px solid #e5e5e5;border-radius: 3px;">
                                                <td colspan="4">Pelanggan bisa menambah jumlah per pilihan</td>
                                                <td align="right"><input type="checkbox" id="checkBisaTambahJumlahPilihan"
                                                                         class="switch-small" <?= $pilihan_ekstra['BisaMenambahJumlahPerPilihan'] == 'true' ? 'checked' : ''; ?>
                                                                         onchange="changeBisaTambahJumlahPilihan(this)"/></td>
                                            </tr>
                                        <?php } ?>
                                        <tr id="headerBisaTambahJumlahPilihan">
                                            <td>Pilihan</td>
                                            <td>Harga</td>
                                            <td>Qty Dibutuhkan</td>
                                            <td colspan="2">Satuan</td>
                                        </tr>
                                        <tr id="row-tambah-pilihan-ekstra">
                                            <td colspan="2" align="center"><a href="#" id="tambah-baris-pilihan-ekstra">Tambah Baris</a></td>
                                        </tr>
                                    </table>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">&nbsp;</label>
                                <div class="col-md-8">
                                    <div class="form-actions">
                                        <a href="<?= base_url('pilihanekstra/index?outlet=' . $selected_outlet); ?>"
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

