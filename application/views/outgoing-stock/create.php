<?php
/**
 * @author <yustiko404@gmail.com>
 * User: Yustiko
 * Date: 4/19/2017
 * Time: 8:05 PM
 */
?>

<form class="form-horizontal" id="form-outlet" style="margin-top: 20px">
    <div class="col-md-6">
        <a href="<?= base_url('stokkeluar?outlet='.$_GET['outlet']) ?>" class="btn btn-default">Kembali</a>
    </div>
    <div class="col-md-6">
        <div class="form-group pull-right col-md-12">
            <div class="col-md-3">
                <label class="control-label">Outlet</label>
            </div>
            <div class="col-md-9">
                <select class="form-control" name="outlet" id="outlet" onchange="document.getElementById('form-outlet').submit()">
                    <?php foreach ($outlets as $key => $outlet): ?>
                        <option value="<?= $key ?>" <?= $_GET['outlet'] == $key ? "selected" : "" ?>>
                            <?= str_replace('#$%^', ' ', $outlet); ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>
</form>

<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Tambah Stok keluar</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i
                                class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class="widget-block">

                    <form class="form-horizontal" method="post" action="/stokkeluar/store" id="form-table">
                        <input type="hidden" name="outlet" value="<?= $_GET['outlet'] ?>">
                        <div class="form-group">
                            <label class="col-md-4 control-label">Tanggal</label>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <div class="input-group">
                                            <input type="text" name="datetime" class="form-control datetime" required value="<?=date('Y-m-d H:i');?>">
                                            <span class="input-group-addon onFocus">
                                                <i class="glyphicon glyphicon-th"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label">Catatan</label>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-xs-12 col-md-6">
                                        <textarea name="note" class="form-control" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-2"></div>
                            <div class="col-md-7">
                                <table class="table table-bordered  table-striped " id="grid-item">
                                    <thead>
                                    <tr>
                                        <th>Nama Item</th>
                                        <th>Jumlah</th>
                                        <td></td>
                                    </tr>
                                    </thead>
                                    <tbody id="compiling-form">

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-4 control-label">
                                <a type="button" onclick="render_row()" style="cursor: pointer">+ Tambah Item Lain</a>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-4 control-label"></label>
                            <div class="col-md-8">
                                <div class="form-actions">
                                    <a href="<?= base_url('stokkeluar?outlet='.$_GET['outlet']) ?>" class="btn btn-default">Cancel</a>
                                    <button type="submit" class="btn btn-primary  has-spinner" id="btn-simpan-single-outlet">
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

<script type="text/javascript">
    var interval = setInterval(function() {
        if(document.readyState === 'complete') {
            clearInterval(interval);
            render_row();
        }
    }, 100);
</script>