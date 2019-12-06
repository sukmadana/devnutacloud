<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 16/05/2016
 * Time: 17:45
 */
?>
<style type="text/css">
    .hidden {
        display: none;
    }
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
                                    <a href="<?= base_url('extra/index?outlet=' . $selected_outlet); ?>"
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
                    <h4>Pilihan Extra</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <?php
                    if($modeform == "new"):
                ?>
                    <div class="widget-container">
                    <div class=" widget-block">
                        <h4 class="modal-title text-center" id="kategori-modal-title">Tambah Pilihan Ekstra </h4>
                        <form id="form-pilihan-ekstra" class="form">
                            <table align="center" id="nuta-plihan-ekstra-table-form" width="100%">
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
                                <tr style="border: 1px solid #e5e5e5;border-radius: 3px;">
                                    <td colspan="4">Pelanggan bisa menambah jumlah per pilihan</td>
                                    <td align="right"><input type="checkbox" id="checkBisaTambahJumlahPilihan"
                                                             class="switch-small" <?= $pilihan_ekstra['BisaMenambahJumlahPerPilihan'] == 'true' ? 'checked' : ''; ?>
                                                             onchange="changeBisaTambahJumlahPilihan(this)"/></td>
                                </tr>
                                <?php if ($option->StockModifier == 1) { ?>
                                    <tr id="headerBisaTambahJumlahPilihan">
                                        <td>Pilihan</td>
                                        <td>Harga</td>
                                        <td>Bahan</td>
                                        <td colspan="2">Action</td>
                                    </tr>
                                    <tr id="row-tambah-pilihan-ekstra">
                                        <td colspan="2" align="center"><a href="#" id="tambahBaris">Tambah Baris</a></td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <br/>
                            <div class="table-responsive masterbahan hidden" id="masterbahan" style="background-color:#ededed;"
                                 id="container-grid-tambah-bahan">
                                <p style="text-align: center;padding:5px;" id="label-bahan">Bahan yang dibutuhkan
                                    untuk
                                    membuat 1 pcs
                                    :</p>
                                <table style="margin: 0 auto" id="grid-tambah-bahan" cellpadding="50" cellspacing="50" class="tblbahan">
                                    <tr>
                                        <td>Bahan</td>
                                        <td>Qty</td>
                                        <td>Satuan</td>
                                        <td>Harga Beli</td>
                                        <td>Total</td>
                                    </tr>
                                        <tr class="itembahan">
                                            <td><input data-tag="bahan" type="text" class="form-control typeahead" value=""/></td>
                                            <td><input data-name="qty" value="1" type="number" min="0" class="form-control qty"/></td>
                                            <td><input data-tag="satuan" type="text" class="form-control" value="PCS"/></td>
                                            <td><input data-name="hargabeli" type="number" value="0" min="0" class="form-control hargabeli" /></td>
                                            <td>
                                                <!--div class="input-group"-->
                                                <input data-name="total" readonly type="number" min="0" class="form-control" value="0" />
                                                <!--span class="input-group-addon">&nbsp;</span>
                                            </div-->
                                            </td>
                                            <td><a class="btn btn-default btn-hapus-bahan"><span
                                                            class="fa fa-trash"></span></a>
                                            </td>
                                            </td>
                                        </tr>
                                    <tr id="row-tambah" class="row-tambahs">
                                        <td colspan="4" align="center">
                                            <a href="#" id="tambah-baris-bahan1" class="btn btn-xs btn-primary btntambah">Tambah Baris</a>
                                            <a href="#" id="close-baris-bahan1" class="btn btn-xs btn-primary btnselesai">Selesai Edit Bahan</a>
                                        </td>
                                        <td><input type="text" name="totalhpp" readonly="readonly" id="totalhpp" class="form-control" value="<?= $totalhpp ? $totalhpp : '0'?>"></td>
                                    </tr>
                                </table>
                            </div>
                            <div id="copyhere">

                            </div>
                            <center>
                                <input type="button" class="btn btn-primary" data-toggle="modal" data-target="#simpan-item-modal" value="Simpan ke beberapa outlet"/>
                                <button id="btnSimpan" class="btn btn-primary" type="button">Simpan</button>
                            </center>
                        </form>
                    </div>
                </div>
                <?php
                    else:
                        $data = $modifieredit[0];
                ?>
                    <div class="widget-container">
                    <div class=" widget-block">
                        <h4 class="modal-title text-center" id="kategori-modal-title">Tambah Pilihan Ekstra </h4>
                        <form id="form-pilihan-ekstra" class="form">
                            <table align="center" id="nuta-plihan-ekstra-table-form" width="100%">
                                <tr>
                                    <td colspan="5">
                                        <input placeholder="<?= $pilihan_ekstra['PlaceholderPilihan']; ?>" class="form-control"
                                               type="text" id="namaEkstra" value="<?=$data['ModifierName']?>">
                                    </td>
                                </tr>
                                <tr style="border: 1px solid #e5e5e5;border-radius: 3px;">
                                    <td colspan="4">Pelanggan hanya bisa pilih satu ekstra</td>
                                    <td align="right"><input type="checkbox" id="checkPilihSatu"
                                                             class="switch-small" <?= $pilihan_ekstra['HanyaBisaPilihSatu'] == 'true' ? 'checked' : ''; ?>/>
                                    </td>s
                                </tr>
                                <tr style="border: 1px solid #e5e5e5;border-radius: 3px;">
                                    <td colspan="4">Pelanggan bisa menambah jumlah per pilihan</td>
                                    <td align="right"><input type="checkbox" id="checkBisaTambahJumlahPilihan"
                                                             class="switch-small" <?= $pilihan_ekstra['BisaMenambahJumlahPerPilihan'] == 'true' ? 'checked' : ''; ?>
                                                             onchange="changeBisaTambahJumlahPilihan(this)"/></td>
                                </tr>
                                <?php if ($option->StockModifier == 1) { ?>
                                    <tr id="headerBisaTambahJumlahPilihan">
                                        <td>Pilihan</td>
                                        <td>Harga</td>
                                        <td>Bahan</td>
                                        <td colspan="2">Action</td>
                                    </tr>
                                    <?php
                                        if(count($data['detail']) >0):
                                            foreach ($data['detail'] as $ds => $dv):
                                    ?>
                                        <tr class="item-pilihan-ekstra row<?=$ds+1?>" data-op="new">
                                            <td>
                                                <input type="text" class="form-control" placeholder="misal: Kacang" value="<?=$dv->ChoiceName;?>"/>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon">Rp.</span>
                                                    <input type="text" class="form-control" placeholder="0" value="<?=$dv->ChoicePrice;?>"/>
                                                </div>
                                            </td>
                                            <td class="kolomBisaTambahJumlahPilihan">
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" readonly>
                                                    <div class="input-group-addon">
                                                        <a href="javascript:void(0)" id="btnbahan<?=$ds + 1?>" onclick="addBahan(this,'');">Bahan</a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href = "#" class= "btn btn-danger" onclick = "hapusdetail('item<?=$ds+1?>');" > Hapus </a>
                                            </td>
                                        </tr>
                                    <?php
                                        endforeach;
                                        endif;
                                    ?>
                                    <tr id="row-tambah-pilihan-ekstra">
                                        <td colspan="2" align="center"><a href="#" id="tambahBaris">Tambah Baris</a></td>
                                    </tr>
                                <?php } ?>
                            </table>
                            <br/>

                            <div class="table-responsive masterbahan hidden" id="masterbahan" style="background-color:#ededed;"
                                 id="container-grid-tambah-bahan">
                                <p style="text-align: center;padding:5px;" id="label-bahan">Bahan yang dibutuhkan
                                    untuk
                                    membuat 1 pcs
                                    :</p>
                                <table style="margin: 0 auto" id="grid-tambah-bahan" cellpadding="50" cellspacing="50" class="tblbahan">
                                    <tr>
                                        <td>Bahan</td>
                                        <td>Qty</td>
                                        <td>Satuan</td>
                                        <td>Harga Beli</td>
                                        <td>Total</td>
                                    </tr>
                                    <tr class="itembahan">
                                        <td><input data-tag="bahan" type="text" class="form-control typeahead" value=""/></td>
                                        <td><input data-name="qty" value="1" type="number" min="0" class="form-control qty"/></td>
                                        <td><input data-tag="satuan" type="text" class="form-control" value="PCS"/></td>
                                        <td><input data-name="hargabeli" type="number" value="0" min="0" class="form-control hargabeli" /></td>
                                        <td>
                                            <!--div class="input-group"-->
                                            <input data-name="total" readonly type="number" min="0" class="form-control" value="0" />
                                            <!--span class="input-group-addon">&nbsp;</span>
                                        </div-->
                                        </td>
                                        <td><a class="btn btn-default btn-hapus-bahan"><span
                                                        class="fa fa-trash"></span></a>
                                        </td>
                                    </tr>
                                    <tr id="row-tambah" class="row-tambahs">
                                        <td colspan="4" align="center">
                                            <a href="#" id="tambah-baris-bahan1" class="btn btn-xs btn-primary btntambah">Tambah Baris</a>
                                            <a href="#" id="close-baris-bahan1" class="btn btn-xs btn-primary btnselesai">Selesai Edit Bahan</a>
                                        </td>
                                        <td><input type="text" name="totalhpp" readonly="readonly" id="totalhpp" class="form-control" value="<?= $totalhpp ? $totalhpp : '0'?>"></td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                                if(count($data['detail']) >0):
                                foreach ($data['detail'] as $ds => $dv):
//                                    var_dump($dv); exit;
                            ?>

                            <div class="table-responsive masterbahan hidden" id="masterbahan<?=$ds+1?>" style="background-color:#ededed;"
                                 id="container-grid-tambah-bahan">
                                <p style="text-align: center;padding:5px;" id="label-bahan">Bahan yang dibutuhkan
                                    untuk
                                    membuat 1 pcs <b><?=$dv->ChoiceName?></b>
                                    :</p>
                                <table style="margin: 0 auto" id="grid-tambah-bahan<?=$ds + 1?>" cellpadding="50" cellspacing="50" class="tblbahan">
                                    <tr>
                                        <td>Bahan</td>
                                        <td>Qty</td>
                                        <td>Satuan</td>
                                        <td>Harga Beli</td>
                                        <td>Total</td>
                                    </tr>
                                    <?php
                                        if (count($dv->bahan) > 0):
                                            foreach ($dv->bahan as $bk => $vk):
                                    ?>
                                    <tr class="itembahan itembahan<?=$ds.$bk + 1?>">
                                        <td><input data-tag="bahan" type="text" class="form-control typeahead" value="<?=$vk->ItemName?>"/></td>
                                        <td><input data-name="qty" value="<?=$vk->QtyNeed?>" type="number" min="0" class="form-control qty"/></td>
                                        <td><input data-tag="satuan" type="text" class="form-control" value="<?=$vk->Unit?>"/></td>
                                        <td><input data-name="hargabeli" type="number" value="<?=(int)$vk->PurchasePrice?>" min="0" class="form-control hargabeli" /></td>
                                        <td>
                                            <input data-name="total" readonly type="number" min="0" class="form-control" value="<?=(int)$vk->PurchasePrice *$vk->QtyNeed ?>" />
                                        </td>
                                        <td>
                                            <a class="btn btn-default btn-hapus-bahan" href="javascript:void(0)" onclick="hps('itembahan<?=$ds.$bk + 1?>', '<?=$vk->ID?>')">
                                                <span class="fa fa-trash"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                        endforeach;
                                        endif;
                                    ?>
                                    <tr id="row-tambah<?=$ds + 1?>" class="row-tambahs">
                                        <td colspan="4" align="center">
                                            <a href="javascript:void(0)" onclick="tmbhBrs('<?=$ds+1?>')" id="tambahbaris<?=$ds+1?>" class="btn btn-xs btn-primary">Tambah Baris</a>
                                            <a href="javascript:void(0)" onclick="closebaris('<?=$ds+1?>')" id="closebaris<?=$ds+1?>" class="btn btn-xs btn-primary">Selesai Edit Bahan</a>
                                        </td>
                                        <td><input type="text" name="totalhpp" readonly="readonly" id="totalhpp" class="form-control" value="<?= $totalhpp ? $totalhpp : '0'?>"></td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                            endforeach;
                            endif;
                            ?>
                            <div id="copyhere">

                            </div>
                            <button id="btnSimpan" class="btn btn-primary" type="button">Simpan</button>
                        </form>
                    </div>
                </div>
                <?php
                    endif;
                ?>
            </div>
        </div>
        <?php $this->load->view('features/dialogs/dialog_simpan_beberapa_outlet'); ?>
    </div>
</div>


