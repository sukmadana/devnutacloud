<style>
    .switchery-small {
        border-radius: 15px;
        height: 15px;
        width: 28px;
    }

    .switchery-small>small {
        height: 15px;
        width: 15px;
    }
</style>
<div class="container-fluid">
    <ol class="breadcrumb">
        <li><a href="<?= base_url() ?>perusahaan/user">User</a></li>
        <li><a href="<?= base_url() ?>perusahaan/usertablet?outlet=<?= $selectedoutlet ?>">NutaApps / tablet</a></li>
        <li class="active">Kelola User <?= $form->Username ?></li>
    </ol>
    <div class="row">
        <div class="col-md-5">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <h4 class="text-bolder">Informasi User</h4>
                    </div>
                    <div class="widget-block pt-10" <?php if ($form->Level != 999) {
                                                        echo 'style="padding-bottom: 68px"';
                                                    } ?>>
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-md-4 control-label pt-7 text-left">User</label>
                                <div class="col-md-8">
                                    <p class="form-control-static"><?= $form->Username ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label pt-7 text-left">Password</label>
                                <div class="col-md-3">
                                    <p class="form-control-static"><?= str_pad('', strlen($form->Password), '*', STR_PAD_RIGHT) ?></p>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-block btn-white btn-default" name="button" data-toggle="modal" data-target="#modalChangePassword">
                                        Ubah Password
                                    </button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label pt-7 text-left">Email</label>
                                <div class="col-md-8">
                                    <p class="form-control-static"><?= $form->Email ?></p>
                                </div>
                            </div>
                            <?php if ($form->Level == 999) { ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label pt-7 text-left">Tgl Install Nuta</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static"><?= formatdateindonesia($options->TglJamUpdate) ?></p>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label pt-7 text-left">Tgl User dibuat</label>
                                    <div class="col-md-8">
                                        <p class="form-control-static"><?= formatdateindonesia($form->TglJamUpdate) ?></p>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <label class="col-md-4 control-label pt-7 text-left">Jenis User</label>
                                <div class="col-md-8">
                                    <?php if ($form->Level == 999) { ?>
                                        <label class="label label-danger">Administrator</label>
                                        <p class="form-control-static" style="line-height:0.9;">
                                            <small class="text-muted font-10">Jenis user ini mampu mengelola jenis user "Biasa".
                                                User Administrator dibuat OTOMATIS oleh sistem di Nuta, saat pertama kali Nuta di instal.</small>
                                        </p>
                                    <?php } else { ?>
                                        <label class="label label-warning">Biasa</label>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-4 control-label pt-7 text-left">Jabatan</label>
                                <div class="col-md-8">
                                    <p class="form-control-static"><?= $form->Jabatan ?></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <h4 class="text-bolder">Hak Akses dan Pekerjaan</h4>
                    </div>
                    <div class="widget-block">
                        <div class="row">
                            <div class="col-md-4">
                                <ul class="nav nav-pills nav-green nav-stacked text-center" role="tablist" id="parent-tabs">
                                    <?php foreach ($rs_hak_akses as $i => $hak_akses) {
                                        $class = $i == 0 ? 'class="active" style="margin-top: 0px; margin-bottom: 25px;"' : 'class="" style="margin-top: 25px; margin-bottom: 25px;"';
                                        ?>
                                        <li role="presentation" <?= $class ?>><a href="#hakakses-<?= $i ?>" class="font-12 text-bold" aria-controls="hakakses-<?= $i ?>" role="tab" data-toggle="tab"><?= $hak_akses['label'] ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="col-md-8" style="padding-left: 140px">
                                <div class="tab-content">
                                    <?php foreach ($rs_hak_akses as $i => $hak_akses) {
                                        $class = $i == 0 ? 'class="tab-pane active"' : 'class="tab-pane"';
                                        ?>
                                        <div role="tabpanel" <?= $class ?> id="hakakses-<?= $i ?>">
                                            <?php if ($hak_akses['level'] == "1") { ?>
                                                <?php $no = 0; ?>
                                                <?php foreach ($hak_akses['detail'] as $key => $value) {
                                                            $checked = $value['allow'] == 1 ? 'checked=""' : '';
                                                            $checkval = $value['allow'] == 1 ? 'value="on"' : 'value="off"';
                                                            ?>
                                                    <div class="w-info-chart-meta <?php if ($no > 1) {
                                                                                                    echo 'mt-15 mb-15';
                                                                                                } else {
                                                                                                    echo 'mb-15';
                                                                                                } ?>">
                                                        <div class="progress-wrap">
                                                            <div class="clearfix progress-meta">
                                                                <span class="pull-left progress-label font-12">
                                                                    <?= $value['akses'] ?>
                                                                </span>
                                                                <span class="pull-right">
                                                                    <input type="checkbox" data-tag="<?= $key ?>" id="<?= $key ?>" onchange="switchUserAksesOnOffChanged(this)" class="switch-small" <?= $checked;
                                                                                                                                                                                                                    $checkval ?>>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php $no++; ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <ul class="nav nav-pills nav-green nav-stacked text-center" role="tablist">
                                                            <?php foreach ($hak_akses['detail'] as $ii => $hak_akses2) {
                                                                        $class2 = $ii == 0 ? 'class="active"' : '';
                                                                        ?>
                                                                <li role="presentation" <?= $class2 ?>><a href="#hakakses-<?= $i ?>-<?= $ii ?>" class="btn-tab-level2 font-12 text-bold" data-tab-id="#hakakses-<?= $i ?>-<?= $ii ?>" aria-controls="hakakses-<?= $i ?>-<?= $ii ?>" role="tab" data-toggle="tab"><?= $hak_akses2['label'] ?></a></li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6 px-0">
                                                        <?php $no2 = 0; ?>
                                                        <div class="tab-content">
                                                            <?php foreach ($hak_akses['detail'] as $ii => $hak_akses2) {
                                                                        $class2 = $ii == 0 ? 'class="tab-pane tab-pane-level2 active"' : 'class="tab-pane tab-pane-level2"';
                                                                        ?>
                                                                <div role="tabpanel" <?= $class2 ?> id="hakakses-<?= $i ?>-<?= $ii ?>">
                                                                    <?php foreach ($hak_akses2['detail'] as $key => $value) {
                                                                                    $checked = $value['allow'] == 1 ? 'checked=""' : '';
                                                                                    $checkval = $value['allow'] == 1 ? 'value="on"' : 'value="off"';
                                                                                    ?>
                                                                        <div class="w-info-chart-meta <?php if ($no2 > 1) {
                                                                                                                            echo 'mt-15 mb-15';
                                                                                                                        } else {
                                                                                                                            echo 'mb-15';
                                                                                                                        } ?>">
                                                                            <div class="progress-wrap">
                                                                                <div class="clearfix progress-meta">
                                                                                    <span class="pull-left progress-label font-12">
                                                                                        <?= $value['akses'] ?>
                                                                                    </span>
                                                                                    <span class="pull-right">
                                                                                        <input type="checkbox" data-tag="<?= $key ?>" id="<?= $key ?>" class="switch-small" onchange="switchUserAksesOnOffChanged(this)" <?= $checked;
                                                                                                                                                                                                                                            $checkval ?>>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <?php $no2++; ?>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($form->Level != 999) { ?>
            <div class="col-md-12  mb-15">
                <button type="button" data-tag="<?= $form->Username ?>" class="btn btn-md pull-right btn-white px-20 text-danger konfirmasihapus">
                    Hapus User <?= $form->Username ?>
                </button>
            </div>
        <?php } ?>
    </div>
</div>
<div id="modalChangePassword" class="modal fade">
    <div class="modal-dialog modal-confirm modal-medium">
        <div class="modal-content">
            <div class="alert-fixed alert-fixed-danger" style="position: fixed; top: -70px; z-index: 999; display: none;">
                <div class="alert alert-danger text-center" role="alert">
                    <b>Gagal Update Password : </b>
                </div>
            </div>
            <div class="alert-fixed alert-fixed-success" style="position: fixed; top: -70px; z-index: 999; display: none;">
                <div class="alert alert-success text-center" role="alert">
                    <p class="text-center">Penyimpanan Berhasil</p>
                </div>
            </div>
            <div class="modal-header">
                <h4 class="modal-title">Ubah Password</h4>
            </div>
            <form class="form-horizontal" id="formChangePassword">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-md-5 control-label pt-7 text-left">Password Lama</label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="password" class="form-control" name="old_password" value="<?= $form->Password ?>">
                                <span class="input-group-btn">
                                    <button class="btn btn-default show-old-password" type="button">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </span>
                            </div>
                            <small for="password" class="error"><span class="error-ins"></span></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label pt-7 text-left">Password Baru</label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="password" class="form-control" name="password" value="">
                                <span class="input-group-btn">
                                    <button class="btn btn-default show-password" type="button">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </span>
                            </div>
                            <small for="password" class="error"><span class="error-ins"></span></small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label pt-7 text-left">Konfirmasi Password Baru</label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="password" class="form-control" name="konfirmasi_password" value="">
                                <span class="input-group-btn">
                                    <button class="btn btn-default show-konfirmasi-password" type="button">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </span>
                            </div>
                            <small for="password" class="error"><span class="error-ins"></span></small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-update-password" class="btn btn-success px-40">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="modalHapus" class="modal fade">
    <div class="modal-dialog modal-confirm modal-sm-2">
        <div class="modal-content">
            <div class="modal-body text-center p-25">
                <input type="hidden" id="modal-parameter" value="">
                <p class="mt-10 text-muted">Anda yakin menghapus User <span id="delete-username"></span> ? </p>
            </div>
            <div class="modal-footer pb-15">
                <button type="button" class="btn btn-sm btn-white no-border px-20" data-dismiss="modal">Batal</button>
                <button type="submit" id="btn-delete" class="btn btn-sm btn-success px-20">Yakin</button>
            </div>
        </div>
    </div>
</div>
<div class="alert-fixed" id="alert-deleteuser" style="position: fixed; top: 30px; z-index: 999; display: none;">
    <div class="alert alert-success text-center" role="alert">
        Hapus User Berhasil
    </div>
</div>