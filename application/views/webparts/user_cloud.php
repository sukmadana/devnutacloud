
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="box-widget widget-module">
        <div class="widget-container">
          <div class="widget-head clearfix">
            <span class="h-icon"><i class="fa fa-users"></i></span>
            <h4>User NutaCloud</h4>
          </div>
          <div class="widget-block">
            <div class="row">
              <div class="col-md-3">
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
              <div class="col-md-9">
                <form class="form-inline pull-right">
                  <div class="form-group">
                    <input type="text" class="form-control" id="searchBox" placeholder="Pencarian">
                  </div>
                  <div class="form-group">
                    <a href="<?= base_url() ?>perusahaan/addUserCloud" class="btn btn-md btn-success ml-15">Tambah User</a>
                  </div>
                </form>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped table-bordered" id="grid-item" style="width:100%">
                <thead>
                  <tr>
                    <th style="text-align: center">
                      No
                    </th>
                    <th style="text-align: center">
                      User
                    </th>
                    <th style="text-align: center">
                      Email
                    </th>
                    <th style="text-align: center">
                      Tgl User Dibuat
                    </th>
                    <th style="text-align: center">
                      Jenis User
                    </th>
                    <th style="text-align: center">
                      Jabatan
                    </th>
                    <?php if ($visibilityMenu['CustomerEdit'] || $visibilityMenu['CustomerDelete']) { ?>
                      <th style="min-width:110px"></th>
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
