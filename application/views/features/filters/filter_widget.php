<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 
 */
?>
<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-filter"></i></span>
                <h4>Filter</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class="widget-block">
                    <?=$this->load->view('features/filters/filter_form');?>
                </div>
            </div>
        </div>
    </div>
</div>