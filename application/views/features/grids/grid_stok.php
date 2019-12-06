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
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Laporan Stok</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class=" widget-block">
                    <?php $this->load->view('features/filters/filter_stok_form'); ?>
                    <hr/>
                    <div class="table-responsive">
                        <table class="table table-bordered  table-striped dt-table-export ">
                            <thead>
                                <tr>
                                    <?php foreach ($datagrid['fields'] as $field) { ?>
                                        <th>
                                            <?= CamelToWords($field->name); ?>
                                        </th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($datagrid['result']as $row) { ?>
                                    <tr>
                                        <?php foreach ($datagrid['fields'] as $field) { ?>
                                            <td>
                                                <?php
                                                $fieldname = $field->name;
                                                $lowerfieldname=strtolower($fieldname);
                                                echo $row->$fieldname;
                                                ?>
                                            </td>
                                    <?php } ?>
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
