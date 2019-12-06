<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 
 */
?>

<div class="box-widget widget-module">
    <div class="loadmask" id="loadmask-chart-outlet">
        <div class="loadmask-msg" style=" left: 50%;
    top: 50%;
    transform: translate(-50%,-50%);">
            <div class="clearfix">
                <div class="w-loader"></div>
                <span class="w-mask-label">Loading..<span></span>
                </span>
            </div>
        </div>
    </div>
    <div class="widget-head clearfix">
        <span class="h-icon"><i class="fa fa-pie-chart"></i></span>
        <h4 id="caption-chart-outlet">&nbsp;</h4>
    </div>
    <div class="widget-container">
        <div class="widget-block">
            <div id="outlet-terlaris-pie-chart" style="height:300px;width:100%">
            </div>
            <div class="no-data" id="nodataoutletterlaris" style="display: none;">
                Tidak ada data untuk ditampilkan
            </div>
        </div>
    </div>
</div>