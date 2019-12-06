<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 
 */
$alias['Pelanggan'] = 'Supplier';
$alias['Diskon'] = 'Jumlah';
$alias['SubTotal'] = 'Total';
?>
<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4>Laporan Pembelian</h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span>
                    </li>
                </ul>
            </div>
            <div class="widget-container">
                <div class=" widget-block">
                    <?php $this->load->view('features/filters/filter_form_pembelian'); ?>
                    <?php
                    $str = '<table class="table table-bordered table-striped">
                            <thead>
                            <tr>';

                                foreach ($datagrid['fields'] as $field) {
                                $str.='<th>';
                                       
                                        if (isset($alias[$field->name])) {
                                            $str.= $alias[$field->name];
                                        } else {
                                            $str.= CamelToWords($field->name);
                                        }
                                       
                                    $str.='</th>';
                                } 
                            
                    $str .= '</tr>
</thead>
<tbody>';
                    $str .= $tbody;
                    $str .= '</tbody>
</table>
';
                        $str_export = '<table class="table table-bordered table-striped">
                            <thead>
                            <tr>';

                                foreach ($datagrid['fields'] as $field) {
                                $str_export.='<th>';
                                       
                                        if (isset($alias[$field->name])) {
                                            $str_export.= $alias[$field->name];
                                        } else {
                                            $str_export.= CamelToWords($field->name);
                                        }
                                       
                                    $str_export.='</th>';
                                } 
                            
                    $str_export .= '</tr>
                            </thead>
                            <tbody>';
                    $str_export .= $tbody_export;
                    $str_export .= '</tbody>
                            </table>
                            ';

                    ?>
                     <div class="row">
                        <div class="col-md-10"></div>
                        <div class="col-md-2">
                            <form method="post" action="<?= base_url(); ?>test" style="margin-bottom: 10px;">
                                <input type="hidden" name="table" value="<?= htmlspecialchars($str_export); ?>"/>
                                <button class="btn btn-default" type="submit">Export Excel</button>
                            </form>
                        </div>
                    </div>
                    <div class="table-responsive">
                       <?=$str;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
