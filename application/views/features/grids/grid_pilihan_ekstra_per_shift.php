<?php
    for ($i=0; $i<count($result); $i++) {
        $r = $result[$i];


        printShift($r->OpenDate, $r->OpenTime, $r->CloseDate, $r->CloseTime, $r->Details, $outlet, $this);
    }

    function printShift($dateStart, $timeStart, $dateEnd, $timeEnd, $result, $outlet, $ctx) {

	if (!is_null($dateStart) && !is_null($dateEnd) && !is_null($outlet)) {
            $header = "Shift " . str_replace(" " . substr($dateStart, 0, 4), "", formatdateindonesia($dateStart)) . ", " . $timeStart . " - " . str_replace(" " . substr($dateEnd, 0, 4), "", formatdateindonesia($dateEnd))  . ", " . $timeEnd;


?>
<div class="row">
	<div class="widget-head clearfix">
		<span class="h-icon"><i class="fa fa-table"></i></span>
		<h4><?=$header?></h4>
		<ul class="widget-action-bar pull-right">
			<li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span></li>
		</ul>
	</div>
	<div class="widget-container">
		<div class="widget-block">
			<div class="table-responsive">
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th style="min-width:150px;">Nama Item</th>
							<td>Kelompok Pilihan</th>
							<th>Pilihan</th>
							<th style="width: 50px" align="right">Qty</th>
							<th style="width: 105px" align="right">Total</th>
						</tr>      
					</thead>
					<tbody>
						<?php 
						if (empty ($result)){
							echo '<tr><td colspan="5" align="center">Tidak Ada Data</td></tr>';
						}else
						foreach ($result as $row){?>
						<tr>
							<td><?= $row->ItemName?></td>
							<td><?=$row->KelompokPilihan?></td>
							<td><?=$row->Pilihan?></td>
							<td style="width: 50px; min-width:50px;" align="right"><?=$ctx->currencyformatter->format($row->Quantity)?></td>
							<td style="width: 105px; min-width:105px;" align="right"><?=$ctx->currencyformatter->format($row->Total)?></td>
						</tr>
						<?php 
						$grandTotalQty += $row->Quantity;
						$grandTotalRp += $row->Total;
						
						} ?>
					</tbody>
					<tfoot>
						<td colspan="3">Grand Total <?=$header?></td>
						<td align="right"><?=$ctx->currencyformatter->format($grandTotalQty)?></td>
						<td align="right"><?=$ctx->currencyformatter->format($grandTotalRp)?></td>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
        }
    }
	
?>