<?php
$str = '<table class="table table-bordered table-striped ">
<thead>
    <tr>';
                    foreach ($datagrid['fields'] as $field) {
                        $str .= '<th>';
                        if ($field->name === 'Diskon') {
                            $str .= "Jumlah";
                        } else if ($field->name === 'SubTotal') {
                            $str .= "Sub Total Batal";
                        } else if ($field->name === 'Qty') {
                            $str .= "Quantity Batal";
                        } else {
                            $str .= CamelToWords($field->name);
                        }
                        $str .= '</th>';
                    }
                    $str .= '</tr>
</thead>
<tbody>';
                    $str .= $tbody;
                    $str .= '</tbody>
</table>
';

$str_export = '<table class="table table-bordered table-striped ">
<thead>
    <tr>';
                    foreach ($datagrid['fields'] as $field) {
                        $str .= '<th>';
                        if ($field->name === 'Diskon') {
                            $str .= "Jumlah";
                        } else if ($field->name === 'SubTotal') {
                            $str .= "Sub Total Batal";
                        } else if ($field->name === 'Qty') {
                            $str .= "Quantity Batal";
                        } else {
                            $str .= CamelToWords($field->name);
                        }
                        $str .= '</th>';
                    }
                    $str .= '</tr>
</thead>
<tbody>';
                    $str .= $tbody_export;
                    $str .= '</tbody>
</table>
';

                    ?>
                    <div class="row">
                        <div class="col-md-10"></div>
                        <div class="col-md-2">
                            <form method="post" action="<?= base_url(); ?>test" class="margin-bottom10">
                                <input type="hidden" name="table" value="<?= htmlspecialchars($str_export); ?>"/>
                                <button class="btn btn-default" type="submit">Export Excel</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <?= $str; ?>
                    </div>

