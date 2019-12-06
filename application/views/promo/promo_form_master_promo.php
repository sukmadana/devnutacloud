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

    li.category {
        font-weight:bold;
    }

    li.item {
        padding-left:15px;
    }
    #myProgress {
        width: 100%;
        background-color: #ddd;
    }

    #myBar {
        width: 1%;
        height: 30px;
        background-color: #4CAF50;
    }
</style>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">

                <div class="page-breadcrumb-wrap" style="width: 100%">
                    <div class="page-breadcrumb-info">

                        <form class="form-horizontal">
                            <div class="col-md-6">
                                <a href="<?= base_url('promo/listPromo?outlet=' . $selected_outlet); ?>"
                                   class="btn btn-default">Kembali</a>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group pull-right col-md-12">
                                    <div class="col-md-3">
                                        <label
                                                class="control-label"><?= $modeform == 'new' ? 'Outlet' : 'Ubah promo di Outlet'; ?></label>
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
                    <h4>Promo</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <form class="form-horizontal" method="post" action="<?= base_url('transaksi'); ?>"
                              enctype="multipart/form-data" id="form-promo">

                            <div class="form-group">
                                <label class="col-md-4 control-label">Nama Promo</label>
                                <div class="col-xs-12 col-md-6">
                                    <input type="text" class="form-control" id="nama-promo"
                                           value="<?= $form['namapromo']; ?>"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-4 control-label">Jenis Promo</label>
                                <div class="col-xs-12 col-md-6">
                                    <select class="form-control" id="jenis-promo" value="<?= $form['jenispromo']; ?>">
                                        <?php foreach ($jenispromo as $j => $v) { ?>
                                            <option id="<?= $v; ?>" value="<?= $v; ?>"
                                                    data-tag="<?= $v; ?>"
                                                <?= $v == $form['jenispromo'] ? 'selected' : ''; ?>
                                            ><?= $j; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Tanggal -->
                            <?php $this->load->view('promo/promo_input_date_horizontal', array('date_start' => $form['datestart'], 'date_end' => $form['dateend'])); ?>
                            <!-- End of tanggal -->

                            <div class="form-group">
                                <label class="col-md-4 control-label">Jam Berlaku</label>
                                <div class="col-md-6">
                                    <div class="form-inline">
                                        <input style="width:70px;" type="text" class="form-control timepicker"
                                               id="promo-jam-mulai" placeholder="__:__"
                                               name="promo-jam-mulai"
                                               value="<?= $form['jammulai']; ?>"/>
                                           <label class="control-label">-</label>
                                        <input style="width:70px;" type="text" class="form-control timepicker"
                                               id="promo-jam-selesai" placeholder="__:__"
                                               name="promo-jam-selesai"
                                               value="<?= $form['jamend']; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">Hari</label>
                                <div class="col-xs-12 col-md-6">
                                    <select class="select-hari-promo form-control select2-multiple" id="hari-promo"
                                            multiple="multiple">
                                        <?php
                                        $i = 0;
                                        foreach ($hari as $h => $v) {
                                            if ($v == $form['hari'][$i]) {
                                                echo "<option selected value=\"" . $v . "\">" . $h . "</option>";
                                            } else {
                                                echo "<option value=\"" . $v . "\">" . $h . "</option>";
                                            }
                                            $i++;
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div id="promo-1">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Jika konsumen membeli:</label>
                                    <div class="col-md-6">
                                        <div class="form-inline">
                                            <input style="width:68.75px;" type="text" class="form-control"
                                                   id="p1-jumlah-item" value="1"/>
                                            <span>X</span>
                                            <select multiple="multiple" class="form-control" id="p1-item">
                                                <?php foreach ($category as $c) {
                                                    echo "<option class='category' value='" . $c->CategoryID . "." . $c->DeviceNo . "'>" . $c->CategoryName . "</option>";
                                                    foreach ($item as $i) {
                                                        if ($i->CategoryID == $c->CategoryID && $i->CategoryDeviceNo == $c->DeviceNo) {
                                                            echo "<option class='item' ref='" . $i->CategoryID . "." . $i->CategoryDeviceNo . "' value='item" . $i->ItemID . "." . $i->DeviceNo . "'>" . $i->ItemName . "</option>";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Mendapat diskon:</label>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-inline">
                                            <select class="form-control" id="p1-diskon-promo-tipe">
                                                <option <?= $form['discounttype'] == 1 ? 'selected' : ''; ?> value="1">
                                                    %
                                                </option>
                                                <option <?= $form['discounttype'] == 2 ? 'selected' : ''; ?> value="2">
                                                    Rp
                                                </option>
                                            </select>
                                            <input style="width:75%" type="text" class="form-control"
                                                   id="p1-diskon-promo-value" placeholder="0"
                                                   value="<?= $form['discountvalue']; ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="promo-2">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Jika konsumen membeli:</label>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-inline">
                                            <label for="min-harga" class="control-label">Rp</label>
                                            <input style="width:75%" type="text" class="form-control" id="p2-min-harga"
                                                   placeholder="0" value="<?= $form['termtotal']; ?>"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Mendapat diskon:</label>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-inline">
                                            <select class="form-control" id="p2-diskon-promo-tipe">
                                                <option <?= $form['discounttype'] == 1 ? 'selected' : ''; ?> value="1">
                                                    %
                                                </option>
                                                <option <?= $form['discounttype'] == 2 ? 'selected' : ''; ?> value="2">
                                                    Rp
                                                </option>
                                            </select>
                                            <input style="width:75%" type="text" class="form-control"
                                                   id="p2-diskon-promo-value" placeholder="0"
                                                   value="<?= $form['discountvalue']; ?>"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label"></label>

                                    <div class="col-md-6">
                                        <input type="checkbox"
                                               id="p2-multiply" <?= $form['multiply'] == '1' ? 'checked' : ''; ?>/>
                                        <label for="" style="padding:0px !important; vertical-align:top;"  class="control-label">Berlaku kelipatan</label>
                                    </div>

                                </div>
                            </div>

                            <div id="promo-3">
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Jika konsumen membeli:</label>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-inline">
                                            <input style="width:68.75px;" type="text" class="form-control"
                                                   id="p3-jumlah-item-beli" placeholder="0"
                                                   value="<?= $form['itemqty']; ?>"/>
                                            <label class="control-label">X</label>
                                            <select multiple="multiple" class="form-control" id="p3-item-beli">
                                                <?php foreach ($category as $c) {
                                                    echo "<option class='category' value='" . $c->CategoryID . "." . $c->DeviceNo . "'>" . $c->CategoryName . "</option>";
                                                    foreach ($item as $i) {
                                                        if ($i->CategoryID == $c->CategoryID && $i->CategoryDeviceNo == $c->DeviceNo) {
                                                            echo "<option class='item' ref='" . $i->CategoryID . "." . $i->CategoryDeviceNo . "' value='item" . $i->ItemID . "." . $i->DeviceNo . "'>" . $i->ItemName . "</option>";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Dapat gratis:</label>
                                    <div class="col-xs-12 col-md-6">
                                        <div class="form-inline">
                                            <input style="width:68.75px;" type="text" class="form-control"
                                                   id="p3-free-jumlah-item" placeholder="0"
                                                   value="<?= $form['itemqty']; ?>"/>
                                            <label class="control-label">X</label>
                                            <select class="form-control" id="p3-free-item" value="<?= $form['itemid']; ?>">
                                                <?php foreach ($category as $c) {
                                                    echo "<optgroup label='" . $c->CategoryName . "'>";
                                                    foreach ($item as $i) {
                                                        if ($i->CategoryID == $c->CategoryID && $i->CategoryDeviceNo == $c->DeviceNo) {
                                                            echo "<option value='" . $i->ItemID . "." . $i->DeviceNo . "'>" . $i->ItemName . "</option>";
                                                        }
                                                    }
                                                    echo "</optgroup>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label"></label>
                                    <div class="form-inline">
                                        <div class="col-md-6">
                                            <input type="checkbox"
                                                   id="p3-multiply" <?= $form['multiply'] == '1' ? 'checked' : ''; ?> />
                                            <label for="" style="padding:0px !important;  vertical-align:top;" class="control-label">Berlaku kelipatan</label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label">&nbsp;</label>
                                <div class="col-md-8">
                                    <div class="form-actions">
                                        <a href="<?= base_url('promo/listpromo?outlet=' . $selected_outlet); ?>"
                                           class="btn btn-default">Cancel</a>
                                        <input type="button" class="btn btn-primary"
                                               data-toggle="modal"
                                               data-target="#simpan-promo-modal" value="Simpan ke beberapa outlet"/>
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

        <?php $this->load->view('promo/promo_dialog_simpan_beberapa_outlet'); ?>

    </div>
</div>
