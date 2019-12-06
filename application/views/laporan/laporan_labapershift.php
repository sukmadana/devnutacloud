<?php
/*
 * This file created by fach 
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
					<h4>Laporan Laba Per Shift</h4>
					<ul class="widget-action-bar pull-right">
						<li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span></li>
					</ul>
				</div>
				<div class="widget-container">
					<div class=" widget-block">		
						<?php $this->load->view('features/filters/filter_form_penjualan'); ?>
						<hr>
						
						<?php $this->load->view('features/grids/grid_laba_per_shift');?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


