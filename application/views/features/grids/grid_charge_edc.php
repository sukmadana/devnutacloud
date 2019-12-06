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
                <h4>Laporan Charge EDC</h4>
                <ul class="widget-action-bar pull-right">
                    <li id="colapse">
                        <span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-left"></i></span>
                    </li>
                </ul>
            </div>
            <div class="widget-container" style="display:none ">
                <div class=" widget-block">
                    <div class="table-responsive">
                        <div class="table-responsive">
                            <table class="table table-bordered  table-striped dt-table-export ">
                                <thead>
                                <tr>
                                    <?php foreach ($datagrid['fields_charge'] as $field) { ?>
                                        <th>
                                            <?php
                                            if ($field->name === 'Charge') {
                                                echo 'Charge %';
                                            } else {
                                                echo CamelToWords($field->name);
                                            } ?>
                                        </th>
                                    <?php } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $grandTotalCharge = 0;
                                foreach ($datagrid['result_charge'] as $row) { ?>
                                    <tr>
                                        <?php foreach ($datagrid['fields_charge'] as $field) { ?>
                                            <td>
                                                <?php
                                                $fieldname = $field->name;
                                                $lowerfieldname = strtolower($fieldname);
                                                if (strpos($lowerfieldname, 'chargerp') !== FALSE) {
                                                    $grandTotalCharge += $row->$fieldname;
                                                    echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                                                } else if ($lowerfieldname === 'charge') {
                                                    echo $this->currencyformatter->format($row->$fieldname);
                                                } else {
                                                    echo $row->$fieldname;
                                                }
                                                ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>
                                <!--total-->
                                <tr>
                                    <td colspan="<?= count($datagrid['fields_charge']) - 1; ?>">
                                        Grand Total
                                    </td>
                                    <td>Rp. <?= $this->currencyformatter->format($grandTotalCharge); ?></td>
                                </tr>
                                <!--/total-->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br/><br/><br/><br/><br/>
<script type="text/javascript">
    //jQuery(document).ready(function(){
//       $('#colapse').click();
//        alert(1);
    //});
</script>