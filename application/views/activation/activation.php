<?php
/*
 * This file created by Em Husnan
 * Copyright 2015
 */

function get_preview_html($fitur, $info, $until)
{
    ?>
    <span style="font-weight: normal">Outlet: </span><?= $info->nama; ?><br/>
    <span style="font-weight: normal">Alamat: </span><?= $info->alamat; ?><br/>
    <?php if ($fitur == 1 | $fitur == 3) { ?>
    <hr/>
    <span class="label label-primary" style="font-size:12px">Fitur Basic</span>
    <br/>
    <span style="font-weight: normal"><?= $info->istrial=="true"? 'Trial ' : 'Aktif ' ?>sampai dengan: </span><?= formatdateindonesia($info->exp); ?>
    <br/>
    <span style="font-weight: normal"><?= $info->istrial=="true"? 'Akan diaktifkan ' : 'Akan diperpanjang ' ?>sampai dengan: </span><?= formatdateindonesia($until); ?>
    <br/>
    <span style="font-weight: normal" id="TidakBisaAktivasi"></span>
    <br/>

<?php }
    if ($fitur == 2 | $fitur == 3) { ?>
        <hr/>
        <span class="label label-danger">Fitur Meja</span> <br/>
        Aktif sampai : <?= formatdateindonesia($info->FiturMejaAktifSampai); ?><br/>
        Akan diaktifkan sampai dengan : <?= formatdateindonesia($info->exp); ?><br/>
    <?php }
} ?>
<script type="text/javascript" src="https://app.midtrans.com/snap/snap.js"
        data-client-key="VT-client-I7Ryvct1KEGNXdzw"></script>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-7">
                <div class="page-breadcrumb-wrap">
                    <div class="page-breadcrumb-info">
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            </div>
        </div>

        <form class="form-horizontal form-store" method="post" style="display: none;"
              action="<?php echo base_url(); ?>Activation/pushfb">
            <div class="form-group">
                <label class="col-md-4 control-label">Outlet ID</label>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <input type="number" class="form-control" id="txt-jumlah" name="outletid"
                                   value="2676" min="0"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label">Aktif Sampai</label>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-xs-12 col-md-6">
                            <input type="text" class="form-control" id="txt-keterangan" name="aktifsampai"
                                   value="<?= date('Y-m-d') ?>"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="form-actions text-right">
                    <button type="submit" class="btn btn-primary  has-spinner"
                            id="btn-simpan-single-outlet">
                        <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Coba Push Aktivasi</button>
                </div>
            </div>
        </form>
    </div>
    <div class="row">

        <div class="col-md-12">

            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class="widget-head clearfix">
                        <span class="h-icon"><i class="fa fa-slack"></i></span>
                        <h4>Aktivasi Nutacloud</h4>
                    </div>
                    <div class=" widget-block">
                        <form class="form-horizontal" id="myWizard">
                            <section class="step" data-step-title="Permintaan Aktivasi">
                                <input type="hidden" id="alamat" value="<?= $option->CompanyAddress; ?>"/>
                                <input type="hidden" id="email" value="<?= $option->CompanyEmail; ?>"/>
                                <div class="<?= !$has_new_aktivasi ? 'col-md-6' : ''; ?>">
                                    <?php if (!$has_new_aktivasi) { ?>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Outlet</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="outlet" id="outlet">
                                                    <?php

                                                    foreach ($outlets as $k => $v) { ?>
                                                        <?php if ($k == $selected_outlet) { ?>
                                                            <option value="<?= $k; ?>"
                                                                    selected=""><?= str_replace('#$%^', ' ', $v); ?></option>
                                                        <?php } else { ?>
                                                            <option
                                                                    value="<?= $k; ?>"><?= str_replace('#$%^', ' ', $v); ?></option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Fitur</label>
                                            <div class="col-md-8">
                                                <select class="form-control" id="fitur">
                                                    <option value="1" <?= $fitur == 1 ? 'selected' : ''; ?>>Basic
                                                    </option>
                                                    <option value="2" <?= $fitur == 2 ? 'selected' : ''; ?>>Meja
                                                    </option>
                                                    <option value="3" <?= $fitur == 3 ? 'selected' : ''; ?>>Basic + Meja
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Masa Aktif</label>
                                            <div class="col-md-8">
                                                <select class="form-control" id="masaaktif">
                                                    <option value="1 Bulan" <?= $amount == 1 ? 'selected' : ''; ?>>1 Bulan
                                                    </option>
                                                    <option value="12 Bulan" <?= $amount == 12 ? 'selected' : ''; ?>>12 Bulan
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">Kode Voucher</label>
                                            <div class="col-md-8">
                                                <input type="text" class="form-control" id="voucher"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-4 control-label">&nbsp;</label>
                                            <div class="col-md-8">


                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="<?= $has_new_aktivasi ? 'col-md-12' : 'col-md-6'; ?>">
                                    <div class="alert alert-warning">
                                        <?php if (!$has_new_aktivasi) {
                                            get_preview_html($fitur, $info, $until);

                                        } else {
                                            if ($existing_aktivasi['status'] === 'New') { ?>
                                                Sepertinya Anda belum menyelesaikan proses pembayaran aktivasi di outlet ini.
                                            <?php } else if ($existing_aktivasi['status'] === 'Payment Successful') { ?>
                                                Pembayaran Berhasil.
                                            <?php }
                                        } ?>
                                        <br/><br/>

                                        <div class="form-actions">
                                            <input type="button" class="btn btn-primary"
                                                   value="<?= !$has_new_aktivasi ? 'Kirim' : 'Pembayaran'; ?>"
                                                   id="kirimAktivasi"/>
                                        </div>
                                    </div>
                                </div>

                            </section>
                            <section class="step" data-step-title="Pembayaran">
                                <div class="form-group">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                        <div class="alert alert-warning">
                                            <?php get_preview_html($fitur, $info, $until); ?>
                                            <hr/>
                                            <span style="font-weight: normal">Kode Aktivasi: </span><span
                                                    id="kodeaktivasi"><?= $existing_aktivasi['kode']; ?></span><br/>
                                            <span style="font-weight: normal">Total: Rp.
                                            </span>
                                            <span id="totalharga"><?php if (isNotEmpty($existing_aktivasi['total'])) {
                                                    echo formatcurrency($existing_aktivasi['total']);
                                                }
                                                ?></span><br/>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>
                                <input type="hidden" id="hiddentoken" value="<?= $existing_aktivasi['token']; ?>"/>
                                <input type="hidden" id="hiddenkodeaktivasi"/>
                                <input type="hidden" id="hiddentotal"/>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">&nbsp;</label>
                                    <div class="col-md-8">
                                        <div class="form-actions">

                                            <input type="button" class="btn btn-default" value="Kembali"
                                                   id="kembaliKeKirimAktivasi"/>
                                            <input type="button" class="btn btn-primary" value="Pembayaran"
                                                   id="pembayaran"/>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
