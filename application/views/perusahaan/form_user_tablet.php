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
        <li class="active">Tambah User</li>
    </ol>
    <div class="row">
        <form class="form-horizontal" action="<?= base_url() ?>perusahaan/usertabletformprocess?outlet=<?= $selectedoutlet ?>" method="post" id="form-registrasi-user-perusahaan">
            <div class="col-md-5">
                <div class="box-widget widget-module">
                    <div class="widget-container">
                        <div class="widget-head clearfix">
                            <h4>Informasi User</h4>
                        </div>
                        <div class="widget-block">
                            <div class="form-group">
                                <label class="col-md-5 control-label text-left">User</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="username" value="">
                                    <small for="username" class="error"><span class="error-ins"></span></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-5 control-label text-left">Password</label>
                                <div class="col-md-7">
                                    <input type="password" class="form-control" name="password" value="">
                                    <small for="password" class="error"><span class="error-ins"></span></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-5 control-label text-left">Konfirmasi Password</label>
                                <div class="col-md-7">
                                    <input type="password" class="form-control" name="konfirmasi_password" value="">
                                    <small for="konfirmasi_password" class="error"><span class="error-ins"></span></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-5 control-label text-left">Email</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="email" value="">
                                    <small for="email" class="error"><span class="error-ins"></span></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-5 control-label text-left">Jenis User</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" value="Biasa" disabled>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-5 control-label text-left">Jabatan</label>
                                <div class="col-md-7">
                                    <input type="text" class="form-control" name="jabatan" value="">
                                    <small for="jabatan" class="error"><span class="error-ins"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="box-widget widget-module">
                    <div class="widget-container">
                        <div class="widget-head clearfix">
                            <h4>Hak Akses dan Pekerjaan</h4>
                        </div>
                        <div class="widget-block pb-15">
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
                                                                        <input type="hidden" id="hidden-<?= $key ?>" name="allowakses[<?= $key ?>]" <?= $checkval ?>>
                                                                        <input type="checkbox" data-tag="<?= $key ?>" id="<?= $key ?>" class="switch-small switch_access" <?= $checked;
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
                                                                            $class2 = $ii == 0 ? 'class="active" style="margin-top: 0px; margin-bottom: 25px;"' : 'class="" style="margin-top: 25px; margin-bottom: 25px;"';
                                                                            ?>
                                                                    <li role="presentation" <?= $class2 ?>><a href="#hakakses-<?= $i ?>-<?= $ii ?>" class="btn-tab-level2 font-12 text-bold" data-tab-id="#hakakses-<?= $i ?>-<?= $ii ?>" aria-controls="hakakses-<?= $i ?>-<?= $ii ?>" role="tab" data-toggle="tab"><?= $hak_akses2['label'] ?></a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6 px-0">
                                                            <div class="tab-content">
                                                                <?php $no2 = 0; ?>
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
                                                                                        <span style="font-12" class="pull-left progress-label">
                                                                                            <?= $value['akses'] ?>
                                                                                        </span>
                                                                                        <span class="pull-right">
                                                                                            <input type="hidden" id="hidden-<?= $key ?>" name="allowakses[<?= $key ?>]" <?= $checkval ?>>
                                                                                            <input type="checkbox" data-tag="<?= $key ?>" id="<?= $key ?>" class="switch-small switch_access" <?= $checked;
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
            <div class="col-md-12 mb-15">
                <button type="submit" class="btn btn-sm btn-success px-40 pull-right">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>