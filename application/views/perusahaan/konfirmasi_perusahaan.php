<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="box-widget widget-module">
                <div class="widget-container">
                    <div class=" widget-block">
                        <div class="well">
                        <h4 style="text-align: center;">Selamat, anda berhasil mendapatkan ID Perusahaan :</h4>
                        <h3 style="font-weight: 400;letter-spacing: 5px;text-align:center"><?= $idperusahaan; ?></h3></div>
                        <br/>
                        <b>&ast;</b> Gunakan ID Perusahaan pada setiap tablet Anda untuk menggabungkan laporan semua
                        cabang/outlet Anda dalam satu akun nutacloud dengan cara:<br/><br/>
                        <ul id="panduanIdPerSlider" style="text-align:center">
                            <li>1. Ketuk menu Lainnya - Pengaturan<br/><br/>
                                <img class="img-responsive" src="<?= base_url(); ?>/images/paduanidper1.png"
                                     style="margin:0 auto;"/>
                            </li>
                            <li>2. Ketuk Multioutlet<br/><br/>
                                <img class="img-responsive" style="margin:0 auto;"
                                     src="<?= base_url(); ?>images/paduanidper2.png" style="margin:0 auto;"/>
                            </li>
                            <li>3. Hubungkan laporan di tablet ini dengan laporan tablet anda lainnya di awan
                                <br/><br/>
                                <img class="img-responsive" style="margin:0 auto;"
                                     src="<?= base_url(); ?>images/paduanidper3.png" style="margin:0 auto;"/></li>
                            <li>4. Masukkan ID Perusahaan anda dan ketuk Simpan
                                <br/><br/>
                                <img class="img-responsive" style="margin:0 auto;"
                                     src="<?= base_url(); ?>images/paduanidper4.png" style="margin:0 auto;"/></li>
                        </ul>
                        <br/>
                        <b>&ast;</b> Silahkan <a
                            href="<?= base_url(); ?>authentication/loginv2?t=1&v=<?= $idperusahaan; ?>"
                            class="btn btn-primary">&nbsp;Login Multioutlet&nbsp;</a> menggunakan ID Perusahaan Anda
                        dengan username dan password sama dengan single outlet.

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>

</div>



