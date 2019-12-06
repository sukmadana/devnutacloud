<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 
 */
?>

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
        <?php
        $grandTotal = 0;

        foreach ($datagrid['result'] as $row) { ?>
            <tr>
                <?php foreach ($datagrid['fields'] as $field) { ?>
                    <td>
                        <?php
                        $fieldname = $field->name;
                        $lowerfieldname = strtolower($fieldname);
                        if (strpos($lowerfieldname, 'total') !== FALSE) {
                            $grandTotal += $row->$fieldname;
                            echo "" . $this->currencyformatter->format($row->$fieldname);
                        } else if ($lowerfieldname === 'saldo') {
                            $grandTotal += $row->$fieldname;
                            echo "" . $this->currencyformatter->format($row->$fieldname);
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
                <td><?= $this->currencyformatter->format($grandTotal); ?></td>
            </tr>
            <!--/total-->
        <?php } ?>
        </tbody>
    </table>
</div>
<br/>
<?php $this->load->view('features/grids/grid_charge_edc'); ?>
