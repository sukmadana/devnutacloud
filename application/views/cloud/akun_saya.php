<div class="container-fluid margin-top20 ">
    <?php if ($mode == 2) { ?>
        <div class="alert-fixed">
            <div class="alert alert-success" role="alert">
                Perubahan berhasil disimpan.
            </div>
        </div>
    <?php }
    if ($mode == 1) {
        ?>
        <div class="alert-fixed">
            <div class="alert alert-danger" role="alert">
                Terjadi kesalahan saat menyimpan.
            </div>
        </div>
    <?php }
    if ($mode == 3) {
        ?>
        <div class="alert-fixed">
            <div class="alert alert-danger" role="alert">
                <?= $error; ?>
            </div>
        </div>
    <?php }
    if ($mode == 4) {
        ?>
        <div class="alert-fixed">
            <div class="alert alert-danger" role="alert">
                Email sudah dipakai.
            </div>
        </div>
    <?php } ?>
    <div class="row">
        <div class="col-sm-12 col-md-9 col-xl-9 center-block no-float">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <strong>
                            <h4>Akun Saya</h4>
                        </strong>
                    </div>
                    <div class=" widget-block">
                        <form class="form-horizontal" method="post" action="<?= base_url('cloud/accountpost'); ?>" enctype="multipart/form-data">
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 control-label px-20">Foto Profil</label>
                                <div class="col-xs-12 col-sm-8 px-40">
                                    <div class="media profile-wrapper">
                                        <div class="media-left">
                                            <div class="profile-picture <?= (strpos($urlfoto, 'user.png') ? '':'fill');?>" id="profile-photo" style="background-image: url(<?= str_replace(
                                            'www.nutacloud.com/uploaded_photos/', 'nutacloud.com/uploaded_photos/', $urlfoto);?>)"></div>
                                            <input type="file" name="foto" id="photo-field" class="hide">
                                            <input type="hidden" name="ext" id="extPhoto"/>
                                        </div>
                                        <div class="media-body">
                                            <p class="text-accent f-12">Untuk hasil yang baik, <br> silahkan upload foto dengan resolusi tinggi</p>
                                            <button type="button" class="btn btn-ghost-green btn-fixed margin-bottom10" id="trigger-file">Upload Foto</button>
                                            <?php if(!strpos($urlfoto, 'user.png')) : ?>
                                            <p>
                                                <input type="checkbox" name="hapus_foto" class="status_hapus_foto" value="1" style="display:none" />
                                                <a href="#" id="btn-hapusfoto">Hapus Foto</a>
                                            </p>
                                            <?php endif;?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 control-label px-20">Nama Perusahaan</label>
                                <div class="col-xs-12 col-sm-8 px-40">
                                    <input type="text" disabled="disabled" name="nama_perusahaan" class="form-control" value="<?= getLoggedInNamaPerusahaan(); ?>">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 control-label px-20">Akses Outlet Yang Dimiliki</label>
                                <div class="col-xs-12 col-sm-8 px-40">
                                    <div class="form-relative">
                                        <input type="text" class="form-control form-readonly" name="" value="<?= $outlet_count; ?> Outlet" readonly>
                                        <div class="form-absolute-right">
                                            <a href="<?= base_url('perusahaan/outlet'); ?>">Lihat Semua Outlet</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 control-label px-20">Username</label>
                                <div class="col-xs-12 col-sm-8 px-40">
                                    <input type="text" disabled="disabled"  name="username" class="form-control" value="<?= getLoggedInUsername(); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 control-label px-20">Password</label>
                                <div class="col-xs-12 col-sm-8 px-40">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-8">
                                            <input type="password" class="form-control form-readonly" name="" value="<?=$password['password']?>" readonly>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <button type="button" class="btn btn-default btn-full-lg" data-toggle="modal" data-target="#modalChangePassword">Ubah Password</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 control-label px-20">Email</label>
                                <div class="col-xs-12 col-sm-8 px-40">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-8">
                                            <input type="text" class="form-control form-readonly" value="<?= $email; ?>" readonly>
                                        </div>
                                        <div class="col-xs-12 col-sm-4">
                                            <button type="button" class="btn btn-default btn-full-lg" data-toggle="modal" data-target="#modalChangeEmail">Ubah Email</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right margin-top20">
                                <div class="col-xs-12 px-40">
                                    <button type="submit" class="btn-sm px-20 btn btn-green">Simpan Perubahan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-medium fade" id="modalChangePassword" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalChangePassword">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="formChangePassword">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Ubah Password</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-6 control-label">Password Lama</label>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-relative">
                                <input type="password" class="form-control" value="<?=$password['password']?>" name="oldpassword">
                                <div class="form-absolute-right-icon">
                                    <button type="button" class="form-btn-icon js-password"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-6 control-label">Password Baru</label>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-relative">
                                <input type="password" class="form-control" name="newpassword">
                                <div class="form-absolute-right-icon">
                                    <button type="button" class="form-btn-icon js-password"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-6 control-label">Konfirmasi Password Baru</label>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form-relative">
                                <input type="password" class="form-control" name="confirmpassword">
                                <div class="form-absolute-right-icon">
                                    <button type="button" class="form-btn-icon js-password"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-min-size">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal modal-medium fade" id="modalChangeEmail" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalChangeEmail">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" id="formChangeEmail">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Ubah Email</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="col-xs-12 col-sm-5 control-label">Password</label>
                        <div class="col-xs-12 col-sm-7">
                            <div class="form-relative">
                                <input type="password" value="<?=$password['password']?>" class="form-control" name="oldpassword">
                                <div class="form-absolute-right-icon">
                                    <button type="button" class="form-btn-icon js-password"><i class="fa fa-eye"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-5 control-label">Email Saat Ini</label>
                        <div class="col-xs-12 col-sm-7">
                            <input type="email" class="form-control" name="oldemail" value="<?= $email; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-xs-12 col-sm-5 control-label">Email Baru</label>
                        <div class="col-xs-12 col-sm-7">
                            <input type="email" class="form-control" name="newemail">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-min-size">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
