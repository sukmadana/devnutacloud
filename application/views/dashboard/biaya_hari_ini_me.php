<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>

<div class="mini-stats-widget full-block-mini-chart">
    <div class="loadmask" id="loadmask-total-biaya-hari-ini">
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
    <div class="mini-stats-top">
        <span class="mini-stats-value" id="caption-biaya-hari-ini">&nbsp;</span>
    </div>
    <a class="ico-cirlce-widget widget-bg-orange" target="_BLANK" href="<?= base_url() ?>laporan/pengeluaran?date_start=<?= $selected_datestart ?>&date_end=<?= $selected_dateend ?>&outlet=<?= $selected_outlet ?>">
        <span><i class="fa fa-money " style="color:#ffa454"></i></span>
    </a>
    <div class="mini-stats-top">
        <span class="mini-stats-value" id="total-biaya-hari-ini">&nbsp;</span>
    </div>

    <div class="mini-stats-bottom widget-bg-orange" id="footer-biaya-hari-ini">
        <span id="caption-footer-biaya-hari-ini">&nbsp;</span>
    </div>


</div>
