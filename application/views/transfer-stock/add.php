<?php 
// add transfer stok 
?>

<div class="row">
    <div class="col-md-6">
        <br/><a href="<?= base_url() ?>transferstok/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
    </div>
    <div class="col-md-6">
        <form class="form-horizontal" id="form-outlet" style="margin-top: 20px">
            <div class="form-group pull-right col-md-12">
                <div class="col-md-3">
                    <label class="control-label">Outlet</label>
                </div>
                <div class="col-md-9">
                    <select class="form-control" name="outlet" id="outlet"
                            onchange="document.getElementById('form-outlet').submit()">
                        <?php 
                        $lokasi_awal = "";
                        foreach ($outlets as $key => $outlet) {
                                if ( $key != $outlet_tujuan ) {?>

                                    <option value="<?= $key ?>" 

                                    <?php
                                    if ($_GET['outlet'] == $key) {
                                        echo "selected";
                                        $lokasi_awal = str_replace('#$%^', ' ', $outlet);
                                    } else {
                                        echo "";
                                    }
                                    ?>
                                ><?= str_replace('#$%^', ' ', $outlet); ?></option>

                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <input type="hidden" name="ds" value="<?= $date_start ?>"/>
            <input type="hidden" name="de" value="<?= $date_end ?>"/>
            <?php
            //if (!empty($_GET['outlet_tujuan'])) {
            echo '<input type="hidden" name="outlet_tujuan" value="' . $outlet_tujuan . '"/>';
            //}


            $lokasi_tujuan = '';
            foreach ($outlets as $key => $outlet) {
                if ($key == $outlet_tujuan) {
                    $lokasi_tujuan = str_replace('#$%^', ' ', $outlet);
                }
            }
            ?>
        </form>
    </div>
</div>

<div class="row">
    <div class="alert" style="text-align: center;background-color: #fff" id="loading-content">Loading Data...</div>
    <div style="display: none;" id="main-content">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-plus"></i></span>
                    <h4>Tambah Transfer Stok</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">

                        <form class="form-horizontal form-store" method="post"
                              action="<?php echo base_url(); ?>transferstok/store">
                            <input type="hidden" name="from_outlet" value="<?= $_GET['outlet'] ?>">
                            <input type="hidden" name="mode" value="new">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="text-align: left">Lokasi Awal</label>
                                <div class="col-sm-4">
                                    <?php
                                    $selected_outlet_name = '';
                                    foreach ($outlets as $key => $outlet) {
                                        if ($_GET['outlet'] == $key) {
                                            $selected_outlet_name = str_replace('#$%^', ' ', $outlet);
                                        }
                                    }
                                    ?>
                                    <input class="form-control" type="text" value="<?= $selected_outlet_name ?>"
                                           readonly="readonly"/>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label" style="text-align: left">Lokasi Tujuan</label>
                                <div class="col-sm-4">
                                    <select name="to_outlet" class="form-control" required="required">
                                        <?php
                                        foreach ($outlets as $key => $outlet) {
                                            if ($_GET['outlet'] != $key) {
                                                echo '<option ';
                                                if ($outlet_tujuan == $key) {
                                                    echo 'selected="true"';
                                                }
                                                echo ' value="' . $key . '">' . str_replace('#$%^', ' ', $outlet) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <?php if ($items): ?>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <table id="dynamic-table" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Jumlah</th>
                                                <th>Keterangan</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            <tr data-id="1">
                                                <td>
                                                    <select data-show-subtext="true" data-live-search="true"
                                                            class="form-control selectpicker" name="item-name[]"
                                                            data-placeholder="Pilih item..." required="required">
                                                        <option></option>
                                                        <?php foreach ($items as $key => $item): ?>
                                                            <option value="<?= $item['ItemID'] ?>"><?= $item['ItemName'] ?></option>
                                                        <?php endforeach ?>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group input-value" value="0" data-id="1">
                                                        <input type="number" min="0"
                                                               oninput="validity.valid||(value='');" name="item-total[]"
                                                               class="form-control" required="required"/>
                                                        <div class="input-group-addon satuan-sistem">...</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <textarea style="height: 38px" class="form-control"
                                                              name="note[]"></textarea>
                                                </td>
                                                <td class="text-center">
                                                    <a href="#" class="btn btn-default hapus-item"><i
                                                                class="fa fa-trash"></i></a>
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>

                                        <br/>
                                        <div id="tambah-item" style="cursor: pointer;"><i class="fa fa-plus"></i> Tambah
                                            Item Lain
                                        </div>
                                        <br/>

                                        <div class="form-actions text-right">
                                            <a href="<?= base_url() ?>transferstok/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>"
                                               class="btn btn-default">Cancel</a>
                                            <button type="submit" class="btn btn-primary  has-spinner"
                                                    id="btn-simpan-single-outlet">
                                                <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Simpan
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            <?php else: ?>

                                <div class="alert alert-default">Tidak ada item yang bisa ditransfer dari Lokasi Awal
                                    (<b><?= $lokasi_awal ?></b>) ke Lokasi Tujuan (<b><?= $lokasi_tujuan ?></b>), karena
                                    semua itemnya berbeda, tidak ada yang sama.
                                </div>

                            <?php endif ?>
                        </form>


                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
