<?php
    for ($i=0; $i<count($result); $i++) {
        $r = $result[$i];
        printShift($r->OpenDate, $r->OpenTime, $r->CloseDate, $r->CloseTime, $r->Details, $outlet, $this);
    }

    function printShift($dateStart, $timeStart, $dateEnd, $timeEnd, $result, $outlet, $ctx) {
        if (!is_null($dateStart) && !is_null($dateEnd) && !is_null($outlet)) {
            $header = "Shift " . str_replace(" " . substr($dateStart, 0, 4), "", formatdateindonesia($dateStart)) . ", " . $timeStart . " - " . str_replace(" " . substr($dateEnd, 0, 4), "", formatdateindonesia($dateEnd))  . ", " . $timeEnd;

        $listOpsiMakan = array();
        for ($i = 0; $i < count($result); $i++) {
            $om = $result[$i]->NamaOpsiMakan;
            if (!in_array($om, $listOpsiMakan)) {
                array_push($listOpsiMakan, $om);
            }
        }

        $grandTotalQty = 0;
        $grandTotalRp = 0;
        $grandTotalMarkup = 0;

        $rows = array_fill(0, count($listOpsiMakan), array("opsimakan" => "", "rowspan" => 0, "items" => array(), "subtotal_qty" => 0, "subtotal_rp" => 0, "subtotal_markup" => 0));
        for ($i = 0; $i < count($listOpsiMakan); $i++) {
            $rows[$i]["opsimakan"] = $listOpsiMakan[$i];

            for ($j=0; $j < count($result); $j++) {
                if ($listOpsiMakan[$i] === $result[$j]->NamaOpsiMakan) {
                    $rows[$i]["rowspan"]++;
                    $rows[$i]["subtotal_qty"] += $result[$j]->Qty;
                    $rows[$i]["subtotal_rp"] += $result[$j]->SubTotal + $result[$j]->MarkupValue;
                    $rows[$i]["subtotal_markup"] += $result[$j]->MarkupValue;
                    array_push($rows[$i]["items"], $result[$j]);
                }
            }

            $grandTotalQty += $rows[$i]["subtotal_qty"];
            $grandTotalRp += $rows[$i]["subtotal_rp"];
            $grandTotalMarkup += $rows[$i]["subtotal_markup"];
        }
?>

<div class="widget-head clearfix">
    <span class="h-icon"><i class="fa fa-table"></i></span>
    <h4><?=$header?></h4>
    <ul class="widget-action-bar pull-right">
        <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span>
        </li>
    </ul>
</div>
<div class="widget-container">
    <div class="widget-block">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="min-width:150px;">Tipe Penjualan</th>
                        <th style="padding:0px; margin:0px;  vertical-align:top;">
                            <table class="table" style="background:transparent;">
                                <tr>
                                    <td>Item</td>
                                    <td style="width: 50px" align="right">Qty</td>
                                    <td style="width: 105px" align="right">Total per Item</td>
                                    <td style="width: 105px" align="right">Markup per Item</td>
                                </tr>
                            </table>
                        </th>
                        <th>Total Qty per Tipe</th>
                        <th>Total Rp per Tipe</th>
                        <th>Total Markup per Tipe</th>
                    </tr>      
                </thead>
                <tbody>
            <?php
                foreach ($rows as $r) {
            ?>
                    <tr>
                        <td><?=$r["opsimakan"]?></td>
                        <td style="padding:0px; margin:0px; vertical-align:top;">
                            <table class="table" style="background:transparent; height:100%;">
                            <?php
                                for ($i=0; $i<count($r["items"]); $i++) {
                                    $it = $r["items"][$i];
                            ?>
                                <tr>
                                    <td><?=$it->Item?></td>
                                    <td style="width: 50px; min-width:50px;" align="right"><?=$ctx->currencyformatter->format($it->Qty)?></td>
                                    <td style="width: 105px; min-width:105px;" align="right"><?=$ctx->currencyformatter->format($it->SubTotal)?></td>
                                    <td style="width: 105px; min-width:105px;" align="right"><?=$ctx->currencyformatter->format($it->MarkupValue)?></td>
                                </tr>
                            <?php
                                }
                            ?>
                            </table>
                        </td>
                        <td align="right"><?=$ctx->currencyformatter->format($r["subtotal_qty"])?></td>
                        <td align="right"><?=$ctx->currencyformatter->format($r["subtotal_rp"])?></td>
                        <td align="right"><?=$ctx->currencyformatter->format($r["subtotal_markup"])?></td>
                    </tr>
            <?php
                }
            ?>
                </tbody>
                <tfoot>
                    <td colspan="2">Grand Total <?=$header?></td>
                    <td align="right"><?=$ctx->currencyformatter->format($grandTotalQty)?></td>
                    <td align="right"><?=$ctx->currencyformatter->format($grandTotalRp)?></td>
                    <td align="right"><?=$ctx->currencyformatter->format($grandTotalMarkup)?></td>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php
        }
    }
?>