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
    <li><a href="<?= base_url() ?>perusahaan/usercloud">NutaCloud</a></li>
    <li class="active">Kelola User <?= $form['username'] ?></li>
  </ol>
  <div class="row">
    <div class="col-md-6">
      <div class="box-widget widget-module">
        <div class="widget-container">
          <div class="widget-head clearfix">
            <h4>Informasi User</h4>
          </div>
          <div class="widget-block">
            <form class="form-horizontal" action="<?= base_url() ?>perusahaan/usercloudformprocess" method="post" id="form-edit-user-perusahaan">
              <input type="hidden" name="selecteduser" value="<?= $selectedusername ?>">
              <div class="form-group">
                <label class="col-md-4 control-label text-left">User</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="username" value="<?= $form['username'] ?>">
                  <small for="username" class="error"><span class="error-ins"></span></small>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label text-left">Password</label>
                <div class="col-md-8">
                  <div class="input-group">
                    <input type="password" class="form-control" name="password" value="<?= $form['password'] ?>">
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
                <label class="col-md-4 control-label text-left">Email</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="email" value="<?= $form['email'] ?>">
                  <small for="email" class="error"><span class="error-ins"></span></small>
                </div>
              </div>
              <!-- <div class="form-group">
                <label class="col-md-4 control-label text-left">Tanggal User Dibuat</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" value="<?= formatdateindonesia($form['TglUserDibuat']) ?>" disabled>
                </div>
              </div> -->
              <div class="form-group">
                <label class="col-md-4 control-label text-left">Jenis User</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" value="<?= $form['jenis_user'] ?>" disabled>
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-4 control-label text-left">Jabatan</label>
                <div class="col-md-8">
                  <input type="text" class="form-control" name="jabatan" value="<?= $form['jabatan'] ?>">
                  <small for="jabatan" class="error"><span class="error-ins"></span></small>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12">
                  <button type="submit" class="btn btn-sm btn-success pull-right">
                    Simpan Perubahan
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
      <?php if ($form['IsOwner'] != "1") { ?>
        <div class="box-widget widget-module">
          <div class="widget-container">
            <div class="widget-head clearfix">
              <h4>Akses Outlet yang dimiliki</h4>
            </div>
            <div class="widget-block">
              <div class="row">
                <div class="col-md-12 my-15">
                  <form class="form-inline pull-right">
                    <div class="form-group">
                      <input type="text" class="form-control" id="searchBox" placeholder="Cari">
                    </div>
                  </form>
                </div>
              </div>
              <table class="table table-borderless" style="width:100%" id="grid-item">
                <thead class="font-12 text-bold">
                  <tr>
                    <th width="5%"><b>No</b></th>
                    <th><b>Nama Outlet - Kota</b></th>
                    <th><b>Pemilik Outlet</b></th>
                    <th width="10%"></th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($daftardevice as $i => $cabang) { ?>
                    <tr>
                      <td><?= ($i + 1) ?></td>
                      <td>
                        <?= $cabang['namacabang'] ?><br />
                        <b><?= $cabang['alamat'] ?></b>
                      </td>
                      <td><?= $cabang['pemilik'] ?></td>
                      <td>
                        <input type="checkbox" class="switch-small" data-tag="<?= $cabang['outletid'] ?>" onchange="switchUserCabangOnOffChanged(this)" <?= $cabang['allow'] ? 'checked' : ''; ?>>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
    <div class="col-md-6">
      <div class="box-widget widget-module">
        <div class="widget-container">
          <div class="widget-head clearfix">
            <h4>Hak Akses dan Pekerjaan</h4>
          </div>
          <div class="widget-block">
            <div class="row">
              <div class="col-md-4">
                <ul class="nav nav-pills nav-green nav-stacked text-center" role="tablist" id="parent-tabs">
                  <?php foreach ($rs_hak_akses as $i => $hak_akses) {
                    $class = $i == 0 ? 'class="active font-12 text-bold"' : 'class="font-12 text-bold"';
                    ?>
                    <li role="presentation" <?= $class ?>><a href="#hakakses-<?= $i ?>" aria-controls="hakakses-<?= $i ?>" role="tab" data-toggle="tab"><?= $hak_akses['label'] ?></a></li>
                  <?php } ?>
                </ul>
              </div>
              <div class="col-md-8" style="padding-left: 0px">
                <div class="tab-content">
                  <?php foreach ($rs_hak_akses as $i => $hak_akses) {
                    $class = $i == 0 ? 'class="tab-pane active"' : 'class="tab-pane"';
                    ?>
                    <div role="tabpanel" <?= $class ?> id="hakakses-<?= $i ?>" <?php if ($hak_akses["level"] == "1") { ?> style="padding-left: 140px" <?php } ?>>
                      <?php if ($hak_akses['level'] == "1") { ?>
                        <?php foreach ($hak_akses['detail'] as $key => $value) {
                              $checked = $value['allow'] == 1 ? 'checked=""' : '';
                              ?>
                          <div class="w-info-chart-meta">
                            <div class="progress-wrap">
                              <div class="clearfix progress-meta">
                                <span class="pull-left progress-label font-12">
                                  <?= $value['akses'] ?>
                                </span>
                                <span class="pull-right">
                                  <input type="checkbox" data-tag="<?= $key ?>" id="<?= $key ?>" class="switch-small" onchange="switchUserAksesOnOffChanged(this)" <?= $checked ?>>
                                </span>
                              </div>
                            </div>
                          </div>
                        <?php } ?>
                      <?php } else { ?>
                        <div class="row">
                          <div class="col-md-6">
                            <ul class="nav nav-pills nav-green nav-stacked text-center" role="tablist">
                              <?php foreach ($hak_akses['detail'] as $ii => $hak_akses2) {
                                    $class2 = $ii == 0 ? 'class="active font-12 text-bold"' : 'class="font-12 text-bold"';
                                    ?>
                                <li role="presentation" <?= $class2 ?>><a href="#hakakses-<?= $i ?>-<?= $ii ?>" class="btn-tab-level2 px-5" data-tab-id="#hakakses-<?= $i ?>-<?= $ii ?>" aria-controls="hakakses-<?= $i ?>-<?= $ii ?>" role="tab" data-toggle="tab"><?= $hak_akses2['label'] ?></a></li>
                              <?php } ?>
                            </ul>
                          </div>
                          <div class="col-md-6 px-0">
                            <div class="tab-content">
                              <?php foreach ($hak_akses['detail'] as $ii => $hak_akses2) {
                                    $class2 = $ii == 0 ? 'class="tab-pane tab-pane-level2 active"' : 'class="tab-pane tab-pane-level2"';
                                    $style2 = $ii == 0 ? 'style="display: block"' : 'style="display: none"';
                                    ?>
                                <div role="tabpanel" <?= $class2 ?> id="hakakses-<?= $i ?>-<?= $ii ?>" <?= $style2 ?>>
                                  <?php foreach ($hak_akses2['detail'] as $key2 => $value2) {
                                          $checked2 = $value2['allow'] == 1 ? 'checked=""' : '';
                                          ?>
                                    <div class="w-info-chart-meta">
                                      <div class="progress-wrap">
                                        <div class="progress-meta row mb-15">
                                          <span class="col-sm-8 progress-label font-11 pr-0">
                                            <?= $value2['akses'] ?>
                                          </span>
                                          <span class="col-sm-4" style="padding-left: 10px">
                                            <input type="checkbox" data-tag="<?= $key2 ?>" id="<?= $key2 ?>" class="switch-small" onchange="switchUserAksesOnOffChanged(this)" <?= $checked2 ?>>
                                          </span>
                                        </div>
                                      </div>
                                    </div>
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
      <div class="box-widget widget-module">
        <div class="widget-container">
          <div class="widget-head clearfix">
            <h4>Daily Report</h4>
          </div>
          <div class="widget-block">
            <div class="row">
              <div class="col-md-10">
                <span>Terima Daily Report</span><br>
                <small class="text-muted" style="font-size: 10px">Nyalakan pengaturan ini bila ingin menerima email daily report setiap hari</small>
              </div>
              <div class="col-md-2">
                <input type="checkbox" onchange="switchUserDailyReportOnOffChanged(this)" data-tag="LaporanPenjualan" class="switch-small" <?= $allowDailyRerport; ?> />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>