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
                    <h4><?= $title; ?></h4>
                    <div class="navbar-form navbar-right mr-10 search " role="search">
                        <div class="form-group">
                            <div id="searchbox"></div>
                        </div>                        
                        <span class="mt-0" id="tableAction"></span>

                    </div>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">
                        <?php $this->load->view($filter_webpart); ?>
                        <?php if (isset($tbody)) {
                            $this->load->view($grid_webpart, array('tbody' => $tbody));
                        } else {
                            $this->load->view($grid_webpart);
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="table-panel-foo-fix" id="footerTotal">
    <footer class="footer-container">
        <div class="row">
            
            <div class="col-md-6 col-sm-6 col-md-offset-6">
                <div class="footer-right">
                <div class="col-md-6">
                        <h4>Grand Total</h4>
                </div>
                <div class="col-md-6">
                    <h4 id="grandTotal"></h4>
                </div>
                    
                </div>
            </div>
        </div>
    </footer>
</div>
