<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                        <ol class="breadcrumb">
                            <li>
                                <a href="<?= base_url('perusahaan/outlet')?>">Outlet</a>
                            </li>
                            <li class="active">
                                <strong>
                                    <?= $detail_outlet->NamaOutlet.' '.$detail_outlet->AlamatOutlet; ?>
                                </strong>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 nuta-widget">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <strong>
                            <h4>Informasi Outlet</h4>
                        </strong>
                    </div>

                    <div class="widget-block">
                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-5">
                                Nama
                            </div>
                            <div class="col-xs-12 col-sm-7">
                                <?= $detail_outlet->NamaOutlet; ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-5">Alamat</div>
                            <div class="col-xs-12 col-sm-7">
                                <?= $detail_outlet->AlamatOutlet; ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-5">Provinsi</div>
                            <div class="col-xs-12 col-sm-7">
                                <?= $detail_outlet->Propinsi; ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-5">Kota</div>
                            <div class="col-xs-12 col-sm-7">
                                <?= $detail_outlet->Kota; ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-5">No Telepon</div>
                            <div class="col-xs-12 col-sm-7">
                                <?= $detail_outlet->MobilePhone; ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-5">Pemilik Outlet</div>
                            <div class="col-xs-12 col-sm-7">
                                <?= $detail_outlet->Username; ?>
                            </div>
                        </div>
                        <div class="row mb-20">
                            <div class="col-md-12 text-right">
                                <?php if ($OutletEdit == 1): ?>
                                <a href="<?= base_url('perusahaan/editoutlet/'.$detail_outlet->OutletID);?>" class="btn mb-5 btn-primary">
                                    Edit Outlet
                                </a>
                                <?php endif?>
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
                        <strong>
                            <h4>Informasi Akun Nuta</h4>
                        </strong>
                    </div>

                    <div class="widget-block">
                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-4">
                                Tanggal Install
                            </div>
                            <div class="col-xs-12 col-sm-8">
                                <?= formatdateindonesia($detail_options->TglInstall); ?>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-4">
                                Jumlah User Apps
                                <p class="text-mute">adalah jumlah user yang bisa log in ke aplikasi Nuta di tablet android</p>
                            </div>
                            <div class="col-xs-12 col-sm-8">
                                <div class="media">
                                    <div class="media-body"><?= count($detail_user); ?></div>
                                    <div class="media-right">
                                        <button type="button" class="btn btn-with-icon" data-toggle="modal" data-target="#modalUserApps">
                                            Lihat User <i class="fa fa-angle-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-4">
                                Jumlah User Nutacloud
                                <p class="text-mute">adalah jumlah user yang bisa log in ke nutacloud</p>
                            </div>
                            <div class="col-xs-12 col-sm-8">
                                <div class="media">
                                    <div class="media-body"><?=$count_user_nuta; ?></div>
                                    <div class="media-right">
                                        <button type="button" class="btn btn-with-icon" data-toggle="modal" data-target="#modalUserNuta">
                                            Lihat User <i class="fa fa-angle-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-20">
                            <div class="col-xs-12 col-sm-4">
                                Status
                            </div>
                            <div class="col-xs-12 col-sm-8">
                                <?php
                                    $sisa_hari =  ceil(( strtotime($detail_options->TglExpired) - strtotime(date('Y-m-d'))) / (24 * 60 * 60 ));

                                    if(empty($detail_options->TglInstall)){
                                        echo '<div class="label label-large label-info margin-bottom10">Belum Install</div>
                                            <p></p>';
                                    }
                                    elseif ($sisa_hari <= 0){
                                        echo '<div class="label label-large label-red margin-bottom10">Kadaluarsa</div>
                                            <p>Berakhir pada tanggal '.formatdateindonesia($detail_options->TglExpired).'</p>
                                            <a href="'.base_url('activation').'"><button class="btn mb-5 btn-primary">Berlangganan Lagi</button></a>';
                                    }
                                    else{
                                        if ( $detail_options->IsTrial == 'true'){
                                            echo '<div class="label label-large label-orange margin-bottom10">Trial</div>
                                                <p>Sisa Trial '.$sisa_hari.' Hari Lagi <br> Berakhir pada tanggal '.formatdateindonesia($detail_options->TglExpired).'</p>
                                                <a href="'.base_url('activation').'"><button class="btn mb-5 btn-primary">Berlangganan Sekarang</button></a>';
                                        }else{
                                            echo '<div class="label label-large label-blue margin-bottom10">Aktif</div>
                                                <p>Berakhir pada tanggal '.formatdateindonesia($detail_options->TglExpired).'</p>';
                                        }
                                    }

                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($OutletDelete == 1): ?>
        <div class="col-md-12 text-right">
            <form action="<?=base_url('perusahaan/hapusoutlet')?>" method="post">
                <input type="hidden" name="i" value="<?=$detail_outlet->OutletID?>">
                <button class="btn btn-md pull-right btn-white text-danger " type="submit" id="delete-outlet" name="yesdelete" onclick="deleteDataFunction()" value="delete">Hapus Outlet</button>
            </form>
        </div>
        <?php endif?>
    </div>
</div>

<div class="modal modal-medium-2 fade" id="modalUserApps" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalUserApps">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Total Jumlah User Nuta Apps / Tablet : <?= count($detail_user); ?></h4>
            </div>
            <div class="modal-body modal-scrollable pt-30">
                <div class="table-responsive">
                    <table class="table table-border-bottom">
                        <thead>
                            <tr>
                                <th class="text-center">User</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Jabatan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail_user as $du): ?>
                                <tr>
                                    <td><?= $du->Username; ?></td>
                                    <td><?= $du->Email; ?></td>
                                    <td><?= $du->Jabatan; ?></td>
                                    <td class="text-right">
                                        <a href="<?=base_url('perusahaan/usertabletdetail?user='.$du->Username.'&outlet='.$detail_outlet->OutletID)?>" class="btn btn-ghost-default btn-small">Kelola User</a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-medium-2 fade" id="modalUserNuta" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalUserNuta">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Total Jumlah User Nutacloud : <?= $count_user_nuta; ?></h4>
            </div>
            <div class="modal-body modal-scrollable pt-30">
                <div class="table-responsive">
                    <table class="table table-border-bottom">
                        <thead>
                            <tr>
                                <th width="150px">User</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detail_user_nuta as $dun): ?>
                                <tr>
                                    <td><?php $username = isset($dun['Username']) ? $dun['Username'] : $dun['username']; echo $username;?></td>
                                    <td class="text-right">
                                        <a href="<?=base_url('perusahaan/userclouddetail?user='.$username)?>" class="btn btn-ghost-default btn-small">Kelola User</a>
                                    </td>
                                </tr>
                            <?php endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
