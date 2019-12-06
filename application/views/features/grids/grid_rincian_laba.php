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
                <h4>Laporan Rincian Laba</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span>
                    </li>
                </ul>
            </div>
            <div class="widget-container">
                <div class=" widget-block">
                    <?php $this->load->view('features/filters/filter_form_penjualan'); ?>
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
                            <?php foreach ($datagrid['result'] as $row) { ?>
                                <tr>
                                    <?php foreach ($datagrid['fields'] as $field) { ?>
                                        <td>
                                            <?php

                                            $fieldname = $field->name;
                                            $lowerfieldname = strtolower($fieldname);
                                            if($lowerfieldname == 'hpp') {
                                                if (isNotEmpty($row->RincianHpp)) {
                                                    echo '<a target="_blank" href="rincianhpp?outlet=' . $outlet . '&detailid=' . $row->RincianHpp . '">' . $this->currencyformatter->format($row->$fieldname) . '</a>';
                                                } else {
                                                    echo "" . $this->currencyformatter->format($row->$fieldname);
                                                }
                                            }
                                            else if (strpos($lowerfieldname, 'total') !== FALSE ||
                                                strpos($lowerfieldname, 'harga') !== FALSE ||
                                                strpos($lowerfieldname, 'laba') !== FALSE ||
                                                $lowerfieldname == 'hpp'
                                            ) {
                                                echo "" . $this->currencyformatter->format($row->$fieldname);
                                            } else if ($lowerfieldname == 'rincianhpp') {

                                                if (isNotEmpty($row->$fieldname)) {
                                                    //echo 'Lihat Rincian dengan DetailID : ' . $row->$fieldname;
                                                    echo '<a target="_blank" href="rincianhpp?outlet=' . $outlet . '&detailid=' . $row->$fieldname . '" class="btn btn-default">Lihat Rincian</a>';
                                                    //echo '<a href="#" data-toggle="modal" class="btn btn-default" data-target="#rincian-hpp-dialog" data-id="' . $row->$fieldname . '" data-name="' . $row->Item . '" data-qty="' . $row->Qty . '">Lihat Rincian</a>';
                                                }
                                            } else {
                                                echo $row->$fieldname;
                                            }
                                            ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php //$this->load->view('features/dialogs/dialog_rincian_hpp');
                ?>
            </div>
        </div>
    </div>
</div>
