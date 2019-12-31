<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 
 */
?>
<div class="table-responsive">
    <table class="table table-bordered  table-striped dt-export " id="table" >
        <thead>
        <tr>
            <?php foreach ($datagrid['fields'] as $field) { ?>
                <th>
                    <?= CamelToWords($field->name); ?>
                </th>
            <?php } ?>
        </tr>
        </thead>
        <tbody >
        <?php

        foreach ($datagrid['result'] as $row) { ?>
            <tr>
                <?php foreach ($datagrid['fields'] as $field) { ?>
                    <td>
                        <?php
                        $fieldname = $field->name;
                        $lowerfieldname = strtolower($fieldname);

                        if (strpos($lowerfieldname, 'taxvalue') !== FALSE) {
                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                        } else if (strpos($lowerfieldname, 'nominal') !== FALSE) {
                            $grandTotal += $row->$fieldname;
                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                        } else if (strpos($lowerfieldname, 'pajak') !== FALSE) {
                            $grandTotal += $row->$fieldname;
                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                        }  else if (strpos($lowerfieldname, 'total') !== FALSE) {
                            $grandTotal += $row->$fieldname;
                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                        } else if ($lowerfieldname === 'saldo') {
                            $grandTotal += $row->$fieldname;
                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                        } else {
                            echo $row->$fieldname;
                        }
                        
                        ?>
                    </td>
                <?php } ?>
            </tr>
            
        <?php } ?>
        <?php $method = $this->uri->segment(2);
        if ($method != "laba" && $method != "rekapmutasistok" && $method != "saldokasrekening"
            && $method != "rekappembayaran"
        ) { ?>
            <!--total-->
            <tr>
                <td colspan="<?= count($datagrid['fields']) - 1; ?>">
                    Grand Total
                </td>
                <td id="grandTotalText">Rp. <?= $this->currencyformatter->format($grandTotal); ?></td>
            </tr>
            <!--/total-->
        <?php } ?>
        </tbody>
    </table>
</div>
