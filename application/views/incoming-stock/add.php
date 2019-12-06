<div class="row">
    <div class="col-md-6">
        <br/><a href="<?= base_url() ?>stokmasuk/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>" class="btn btn-default">Kembali</a>
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
                        <?php foreach ($outlets as $key => $outlet): ?>
                            <option value="<?= $key ?>" <?= $_GET['outlet'] == $key ? "selected" : "" ?>>
                                <?= str_replace('#$%^', ' ', $outlet); ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </div>
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
                    <h4>Stok Masuk</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class="widget-block">
                        <form class="form-horizontal form-store" method="post"
                              action="<?php echo base_url(); ?>stokmasuk/simpan">
                            <input type="hidden" name="outlet" value="<?= $_GET['outlet'] ?>">
                            <input type="hidden" name="mode" value="new">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <table id="dynamic-table" class="table table-bordered" style="min-width:600px">
                                        <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th style="min-width: 160px">Jumlah</th>
                                            <th>Keterangan</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr data-id="1">
                                            <td>
                                                <select data-show-subtext="true" data-live-search="true"
                                                        class="form-control selectpicker" name="item[]"
                                                        data-placeholder="Pilih item...">
                                                    <option></option>
                                                    <?php foreach ($items as $key => $item): ?>
                                                        <option value="<?= $item->ItemID . "." . $item->DeviceNo ?>"><?= $item->ItemName ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group input-value" value="0" data-id="1">
                                                    <input type="hidden" name="qty-sistem[]" class="form-control"/>
                                                    <input type="hidden" name="qty-aktual[]" class="form-control"/>
                                                    <input type="number" name="qty-selisih[]" class="form-control"/>
                                                    <div class="input-group-addon satuan-aktual">...</div>
                                                </div>
                                            </td>
                                            <td>
                                                <textarea style="height: 38px" class="form-control"
                                                          name="keterangan[]"></textarea>
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
                                        <a href="<?= base_url() ?>stokmasuk/?outlet=<?= $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>"
                                           class="btn btn-default">Cancel</a>
                                        <button type="submit" class="btn btn-primary  has-spinner"
                                                id="btn-simpan-single-outlet">
                                            <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Simpan
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
