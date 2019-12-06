<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
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
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="box-widget widget-module">
                <div class="widget-head clearfix">
                    <span class="h-icon"><i class="fa fa-table"></i></span>
                    <h4>Laporan Feedback Pelanggan</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                    class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <?php $this->load->view('features/filters/filter_form_feedback'); ?>
                        <hr/>
                        <div class="row">
                            <div class="col-md-3 col-sm-6" style="border-right: #FFF 5px solid;">
                                <div style="background-color: #fff;border: #eee 1px solid;">
                                    <div class="stat-w-wrap ca-center number-rotate" style="padding-bottom: 0px">
                                        <span class="stat-w-title">Waktu Menunggu</span>
                                        <a href="#" class="ico-cirlce-widget" style="background-color: #66bb6a">
                                            <span><i class="fa fa-hourglass-o " style="color:#66bb6a"></i></span>
                                        </a>
                                        <div class="w-meta-info">
                                            <span
                                                class="w-meta-value number-animate"
                                                style="padding-top:5px;padding-bottom: 5px"><?= $rekap['Waktu Menunggu']['Good'] + $rekap['Waktu Menunggu']['Bad']; ?></span>
                                            <div style="background-color: #66bb6a !important;margin:0;color:#fff;"
                                                 id="container"
                                                 class="row">
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-smile-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Waktu Menunggu']['Good']; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-frown-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Kualitas']['Bad']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6" style="border-right: #FFF 5px solid;">
                                <div style="background-color: #fff;border: #eee 1px solid;">
                                    <div class="stat-w-wrap ca-center number-rotate" style="padding-bottom: 0px">
                                        <span class="stat-w-title">Kualitas</span>
                                        <a href="#" class="ico-cirlce-widget" style="background-color: #c0ca33">
                                            <span><i class="fa  fa-check-circle-o" style="color:#c0ca33"></i></span>
                                        </a>
                                        <div class="w-meta-info">
                                            <span
                                                class="w-meta-value number-animate"
                                                style="padding-top:5px;padding-bottom: 5px"><?= $rekap['Kualitas']['Good'] + $rekap['Kualitas']['Bad']; ?></span>
                                            <div style="background-color: #c0ca33  !important;margin:0;color:#fff;"
                                                 id="container"
                                                 class="row">
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-smile-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Kualitas']['Good']; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-frown-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Kualitas']['Bad']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 right-light-border col-sm-6" style="border-right: #FFF 5px solid;">
                                <div style="background-color: #fff;border: #eee 1px solid;">
                                    <div class="stat-w-wrap ca-center number-rotate" style="padding-bottom: 0px">
                                        <span class="stat-w-title">Customer Service</span>
                                        <a href="#" class="ico-cirlce-widget" style="background-color: #fb8c00">
                                            <span><i class="fa fa-user " style="color:#fb8c00"></i></span>
                                        </a>
                                        <div class="w-meta-info">
                                            <span
                                                class="w-meta-value number-animate"
                                                style="padding-top:5px;padding-bottom: 5px"><?= $rekap['Customer Service']['Good'] + $rekap['Customer Service']['Bad']; ?></span>
                                            <div style="background-color: #fb8c00   !important;margin:0;color:#fff;"
                                                 id="container"
                                                 class="row">
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-smile-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Customer Service']['Good']; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-frown-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Customer Service']['Bad']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3  col-sm-6" style="border-right: #FFF 5px solid;">
                                <div style="background-color: #fff;border: #eee 1px solid;">
                                    <div class="stat-w-wrap ca-center number-rotate" style="padding-bottom: 0px">
                                        <span class="stat-w-title">Lainya</span>
                                        <a href="#" class="ico-cirlce-widget" style="background-color: #d84315">
                                            <span><i class="fa fa-list-alt " style="color:#d84315"></i></span>
                                        </a>
                                        <div class="w-meta-info">
                                            <span
                                                class="w-meta-value number-animate"
                                                style="padding-top:5px;padding-bottom: 5px"><?= $rekap['Lainnya']['Good'] + $rekap['Lainnya']['Bad']; ?></span>
                                            <div style="background-color: #d84315   !important;margin:0;color:#fff;"
                                                 id="container"
                                                 class="row">
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-smile-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Lainnya']['Good']; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <br/>
                                                    <i class="fa fa-frown-o "
                                                       style="font-size:40px;"></i><br/>
                                                    <?= $rekap['Lainnya']['Bad']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-bordered  table-striped dt-table-export ">
                                <thead>
                                <tr>
                                    <th>Tanggal Jam</th>
                                    <th>Email Pelanggan</th>
                                    <th>Pesan</th>
                                    <th>Kasir</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($datagrid['result'] as $row) { ?>
                                    <tr>
                                        <td><?= $row->TglFeedback; ?></td>
                                        <td><?= $row->Email; ?></td>
                                        <td><?php
                                            if ($row->Response == 'good') {
                                                $icon = '<img src="' . base_url('images/response-smile.png') . '"/>';
                                            } else if ($row->Response == 'bad') {
                                                $icon = '<img src="' . base_url('images/response-sad.png') . '"/>';
                                            }
//                                            $icon . $row->Subject . '<br/>' . $row->Description;
                                            $table = "  <table style='min-height:30px;'>
                                            <tr>
                                                <td rowspan='2' style='padding:5px;'>" . $icon . "</td>
                                                <td style='padding:2px'>" . $row->Subject . "</td>
                                            </tr>
                                            <tr>

                                                <td style='padding:2px'>" . $row->Description . "</td>
                                            </tr>
                                        </table>";
                                            echo $table;
                                            ?></td>
                                        <td><?= $row->Kasir; ?></td>

                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


