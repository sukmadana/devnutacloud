<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <ol class="breadcrumb">
                <li><a href="<?= base_url() ?>perusahaan/user">User</a></li>
                <li class="active">User NutaApps / Tablet</li>
            </ol>
        </div>
        <div class="col-md-7">
            <form class="form-horizontal" action="<?= base_url() ?>perusahaan/usertablet" method="get">
                <div class="form-group row">
                    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                        <label class="control-label col-md-2">Outlet</label>
                        <div class="col-md-7 mb-5">
                            <select class="form-control" name="outlet" id="outlet" onchange="selectinge()" required style="width: 100%">
                                <option></option>
                                <?php foreach ($outlets as $k => $v) { ?>
                                    <option value="<?= $k ?>" <?= $k == $selected_outlet ? "selected" : "" ?>>
                                        <?= str_replace('#$%^', ' ', $v); ?>
                                    </option>
                                <?php
                                    }
                                    ?>
                            </select>
                        </div>
                        <?php if ($visibilityMenu['UserNew']) { ?>
                            <div class="col-md-3">
                                <a href="<?= base_url() ?>perusahaan/usertabletform?outlet=<?= $selected_outlet ?>" class="btn btn-md btn-success px-20" <?php if ($selected_outlet == "" || empty($selected_outlet) || intval($selected_outlet) < 1) { ?> disabled="" <?php } ?>>Tambah User</a>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <?php if ($selected_outlet == "" || empty($selected_outlet) || intval($selected_outlet) < 1) { ?>
            <div class="col-md-12 text-center pt-12p">
                <img src="<?= base_url() ?>images/icon-laporan-kosong.png" alt="Lap Kosong" width="120px">
                <h4 class="text-bolder">Belum dapat menampilkan User</h4>
                <small>Silahkan pilih Outlet pada pilihan di atas</small>
            </div>
        <?php } else { ?>
            <?php if (count($daftaruser) > 0) { ?>
                <!-- Table List User -->
                <div class="col-md-12">
                    <div class="box-widget widget-module">
                        <div class="widget-container">
                            <div class="widget-head clearfix">
                                <h4 class="text-bolder"> Total Jumlah User NutaApps / Tablet : <?= count($daftaruser) ?></h4>
                            </div>
                            <div class="widget-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <form class="form-inline pull-right">
                                            <div class="form-group">
                                                <input type="text" class="form-control" id="searchBox" placeholder="Cari">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="mt-20">
                                    <table class="table table-striped table-borderless" id="grid-item" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="text-left" width="5%">No</th>
                                                <th class="text-left" width="15%">User</th>
                                                <th class="text-left" width="15%">Email</th>
                                                <th class="text-left" width="15%">Tgl User Dibuat</th>
                                                <th class="text-left" width="15%">Jenis User</th>
                                                <th class="text-left" width="10%">Jabatan</th>
                                                <?php if ($visibilityMenu['UserEdit'] || $visibilityMenu['UserDelete']) { ?>
                                                    <th class="text-enter" width="15%"></th>
                                                <?php } ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($daftaruser as $i => $user) { ?>
                                                <tr>
                                                    <td class="text-left"><?= ($i + 1) ?></td>
                                                    <td class="text-left"><?= $user->Username ?></td>
                                                    <td class="text-left"><?= $user->Email ?></td>
                                                    <td class="text-left"><?= formatdateindonesia($user->TglJamUpdate) ?></td>
                                                    <td class="text-left">
                                                        <?php if ($user->Level == 999) { ?>
                                                            <label class="label label-danger label-table">Administrator</label>
                                                        <?php } else { ?>
                                                            Biasa
                                                        <?php } ?>
                                                    </td>
                                                    <td class="text-left"><?= $user->Jabatan ?></td>
                                                    <?php if ($visibilityMenu['UserEdit'] || $visibilityMenu['UserDelete']) { ?>
                                                        <td>
                                                            <?php if ($user->Level == 999 && $visibilityMenu['UserEdit']) { ?>
                                                                <a href="<?= base_url() ?>perusahaan/usertabletdetail?user=<?= $user->Username ?>&outlet=<?= $selected_outlet ?>" class="btn btn-block btn-md btn-white">Kelola Akses</a>
                                                            <?php } else { ?>
                                                                <div class="dropdown dropdown-inherit">
                                                                    <button class="btn btn-md btn-block btn-white dropdown-toggle" type="button" data-toggle="dropdown">Detail<span class="fa fa-chevron-down pull-right mt-3"></span></button>
                                                                    <ul class="dropdown-menu">
                                                                        <?php if ($visibilityMenu['UserEdit']) { ?>
                                                                            <li><a href="<?= base_url() ?>perusahaan/usertabletdetail?user=<?= $user->Username ?>&outlet=<?= $selected_outlet ?>" class="text-center py-10">Kelola Akses</a></li>
                                                                        <?php } ?>
                                                                        <?php if ($visibilityMenu['UserDelete']) { ?>
                                                                            <li class="divider my-0"></li>
                                                                            <li><a href="#modalHapus" class="konfirmasihapus text-center py-10" data-tag="<?= $user->Username ?>">Hapus</a></li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                    <?php } ?>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End off table list user -->
            <?php } else { ?>
                <div class="col-md-12 text-center pt-12p">
                    <img src="<?= base_url() ?>images/icon-laporan-kosong.png" alt="Lap Kosong" width="120px">
                    <h4 class="text-bolder">Belum ada User di Outlet Ini</h4>
                    <small>Silahkan klik tombol Tambah User sekarang</small>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<div id="modalHapus" class="modal fade">
    <div class="modal-dialog modal-confirm modal-sm-2">
        <div class="modal-content" style="margin-top: 45% !important">
            <div class="modal-body text-center p-25">
                <input type="hidden" id="modal-parameter" value="">
                <p class="mt-10 text-muted">Anda yakin menghapus User <span id="delete-username"></span> ? </p>
            </div>
            <div class="modal-footer pb-15">
                <button type="button" class="btn btn-sm btn-white no-border px-20 font-14" data-dismiss="modal">Batal</button>
                <button type="submit" id="btn-delete" class="btn btn-sm btn-success px-20 font-14">Yakin</button>
            </div>
        </div>
    </div>
</div>
<div class="alert-fixed" id="alert-deleteuser" style="position: fixed; top: 30px; z-index: 999; display: none;">
    <div class="alert alert-success text-center" role="alert">
        Hapus User Berhasil
    </div>
</div>