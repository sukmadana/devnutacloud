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
        $grandJumlah = 0;
        foreach ($datagrid['result'] as $row) { ?>
            <tr>
                <?php foreach ($datagrid['fields'] as $field) { ?>
                    <td>
                        <?php
                        $fieldname = $field->name;
                        $lowerfieldname = strtolower($fieldname);

                        if (strpos($lowerfieldname, 'total') !== FALSE) {
                            $grandTotal += $row->$fieldname;
                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                        } else if ($lowerfieldname === 'rata-rata') {
                            echo "Rp. " . $this->currencyformatter->format($row->$fieldname);
                        } else {
                            echo $row->$fieldname;
                        }
                        if ($fieldname === 'JumlahTransaksi') {
                            $grandJumlah += $row->$fieldname;
                        }
                        ?>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>

        <tr>
            <td colspan="<?= count($datagrid['fields']) - 1; ?>">
                Total Rata-rata
            </td>
            <td>Rp. <?php if($grandJumlah>0){echo $this->currencyformatter->format($grandTotal / $grandJumlah);}else{echo  0;} ?></td>
        </tr>

        </tbody>
    </table>
</div>

