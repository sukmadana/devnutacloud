<?php
$str = '<table class="table table-bordered table-striped ">
    <thead>
    <tr>';
foreach ($datagrid['fields'] as $field) {
    $str .= '<th>';
    $str .= CamelToWords($field->name);
    $str .= '</th>';
}
$str .= '<th>Total Qty per Kategori</th>';
$str .= '<th>Total Rp per Kategori</th>';
$str .= '<th>Total HPP per Kategori</th>';
$str .= '<th>Total Laba per Kategori</th>';
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
    $str_export .= '<th>';
    $str_export .= CamelToWords($field->name);
    $str_export .= '</th>';
}
$str_export .= '<th>Total Qty per Kategori</th>';
$str_export .= '<th>Total Rp per Kategori</th>';
$str_export .= '<th>Total HPP per Kategori</th>';
$str_export .= '<th>Total Laba per Kategori</th>';
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
        <form method="post" action="<?= base_url(); ?>test" class="margin-bottom10">
            <input type="hidden" name="table" value="<?= htmlspecialchars($str_export); ?>"/>
            <button class="btn btn-default" type="submit">Export Excel</button>
        </form>
    </div>
</div>

<div class="table-responsive">
    <?= $str; ?>
</div>

