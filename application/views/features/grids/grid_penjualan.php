<?php
$str = '<table class="table table-bordered table-striped ">
<thead>
    <tr>';
                    foreach ($datagrid['fields'] as $field) {
                        $str .= '<th>';
                        if ($field->name === 'Diskon') {
                            $str .= "Jumlah";
                        } else if ($field->name === 'SubTotal') {
                            $str .= "Total";
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
$str_hidden = '<table class="table table-bordered table-striped ">
<thead>
    <tr>';
                    foreach ($datagrid['fields'] as $field) {
                        $str_hidden .= '<th>';
                        if ($field->name === 'Diskon') {
                            $str_hidden .= "Jumlah";
                        } else if ($field->name === 'SubTotal') {
                            $str_hidden .= "Total";
                        } else {
                            $str_hidden .= CamelToWords($field->name);
                        }
                        $str_hidden .= '</th>';
                    }
                    $str_hidden .= '</tr>
</thead>
<tbody>';
                    $str_hidden .= $tbody_hidden;
                    $str_hidden .= '</tbody>
</table>
';

                    ?>
                    <div class="row">
                        <div class="col-md-10"></div>
                        <div class="col-md-2">
                            <form method="post" action="<?= base_url(); ?>test" class="margin-bottom10">
                                <input type="hidden" name="table" value="<?= htmlspecialchars($str_hidden); ?>"/>
                                <button class="btn btn-default" type="submit">Export Excel</button>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <?= $str; ?>
                    </div>

