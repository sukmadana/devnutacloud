<div class="container-fluid">
    <div class="row">
        <!-- <div class="col-md-12">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>perusahaan/user">User</a></li>
                <li class="active">User NutaCloud</li>
            </ol>
        </div> -->
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <h4 class="text-bold">Total Jumlah User NutaCloud : <?= $totaluser ?></h4>
                    </div>
                    <div class="widget-block">
                        <div class="row">
                            <div class="col-md-3 col-xs-3">
                                <form class="form-inline">
                                    <div class="form-group">
                                        <select class="form-control" name="length_change" id="length_change">
                                            <option value='10'>10</option>
                                            <option value='25'>25</option>
                                            <option value='100'>100</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                            <div class="col-md-9 col-xs-9">
                                <form class="form-inline pull-right">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="searchBox" placeholder="Cari">
                                    </div>
                                    <?php if ($visibilityMenu['UserNew']) { ?>
                                        <div class="form-group">
                                            <a href="<?= base_url() ?>perusahaan/usercloudform" class="btn btn-md btn-success ml-15 px-40">Tambah User</a>
                                        </div>
                                    <?php } ?>
                                </form>
                            </div>
                        </div>
                        <div class="mt-15">
                            <table class="table table-borderless" id="grid-item" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center text-bold" width="5%">No</th>
                                        <th class="text-left text-bold" width="15%">User</th>
                                        <th class="text-left text-bold" width="20%">Email</th>
                                        <th class="text-left text-bold" width="15%">Tgl User Dibuat</th>
                                        <th class="text-left text-bold" width="12%">Jenis User</th>
                                        <th class="text-left text-bold" width="10%">Jabatan</th>
                                        <?php if ($visibilityMenu['UserEdit'] || $visibilityMenu['UserDelete']) { ?>
                                            <th class="text-left" width="15%"></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalHapus" class="modal fade">
    <div class="modal-dialog modal-confirm  modal-sm-2">
        <div class="modal-content" style="margin-top: 45% !important">
            <div class="modal-body text-center p-25">
                <input type="hidden" id="modal-parameter" value="">
                <p class="mt-20 text-muted">Anda yakin menghapus User <span id="delete-username"></span> ? </p>
            </div>
            <div class="modal-footer pb-15">
                <button type="button" class="btn btn-white no-border" data-dismiss="modal">Batal</button>
                <button type="submit" id="btn-delete" class="btn btn-success">Yakin</button>
            </div>
        </div>
    </div>
</div>
<div class="alert-fixed" id="alert-deleteuser" style="position: fixed; top: 30px; z-index: 999; display: none;">
    <div class="alert alert-success text-center" role="alert">
        Hapus User Berhasil
    </div>
</div>