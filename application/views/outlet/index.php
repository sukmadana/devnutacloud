<div class="container-fluid margin-top20">
    <div class="row">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <strong>
                            <h4>
                                Total Jumlah Outlet: <?= count($daftardevice); ?>
                            </h4>
                        </strong>
                    </div>

                    <div class=" widget-block">
                        <table id="outlet-table" class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Outlet</th>
                                    <th>Alamat Outlet</th>
                                    <th>Kota</th>
                                    <th>Provinsi</th>
                                    <th>Nomor Telepon</th>
                                    <th>Pemilik Outlet</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $user_nuta = 0;
                                foreach ($daftardevice as $k => $d) : ?>
                                    <?php
                                        if (empty($d->TglInstall)) :
                                            $aktif = '<span class="outlet-label label
                                            label-info">Status : Belum Install</span>
                                            </span>';
                                        elseif (strtotime($d->TglExpired) < strtotime('now')) :
                                            $aktif = '<span class="outlet-label label
                                            label-danger">Status : Kadaluarsa</span><span class="label label-danger outlet-label">Masa Aktif
                                            sampai ' . formatdateindonesia($d->TglExpired) . '
                                            </span>';
                                        elseif ($d->IsTrial == 'true') :
                                            $aktif = '<span class="outlet-label label
                                            label-warning">Status : Trial</span><span class="outlet-label label label-warning">Masa Aktif
                                            sampai ' . formatdateindonesia($d->TglExpired) . '
                                            </span>';
                                        else :
                                            $aktif = '<span class="outlet-label label
                                            label-success-inverse">Status : Aktif</span><span class="outlet-label label label-success-inverse">Masa Aktif
                                            sampai ' . formatdateindonesia($d->TglExpired) . '
                                            </span>';
                                        endif;
                                        $user_nuta = $d->user_nuta + 1;
                                        $user = '<span class="label outlet-label label-warning-inverse">Jumlah User Apps : ' . $d->user_tablet . '</span><span class="outlet-label label label-info-inverse">Jumlah User Nutacloud : ' . $user_nuta . '</span>'
                                        ?>
                                    <tr data-child-value='<?= $aktif . $user; ?>
                                '>
                                        <td></td>
                                        <td><?= $d->NamaOutlet; ?></td>
                                        <td><?= $d->AlamatOutlet; ?></td>
                                        <td><?= $d->Kota; ?></td>
                                        <td><?= $d->Propinsi; ?></td>
                                        <td><?= $d->MobilePhone; ?></td>
                                        <td><?= $d->Username; ?></td>
                                        <td>
                                            <div class="dropdown is-action mt-10">
                                                <button class="btn btn-ghost-white btn-action dropdown-toggle" type="button" data-toggle="dropdown">
                                                    Aksi <span class="btn-action-icon"><i class="fa fa-angle-down"></i></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li>
                                                        <a href="<?= base_url('perusahaan/outletdetailinfo/' . $d->OutletID); ?>">Detail</a>
                                                    </li>
                                                    <?php if ($OutletEdit == 1) : ?>
                                                        <li>
                                                            <a href="<?= base_url('perusahaan/outletsetting/' . $d->OutletID); ?>">Kelola Modul</a>
                                                        </li>
                                                    <?php endif ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>