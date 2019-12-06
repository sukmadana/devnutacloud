<?php
    for ($i=0; $i<count($result); $i++) {
        $r = $result[$i];
        
		printShift($r->OpenDate, $r->OpenTime, $r->CloseDate, $r->CloseTime, $r->DeviceNo, $r->Details, $outlet, $this);
    }
	
    function printShift($dateStart, $timeStart, $dateEnd, $timeEnd, $devno, $result, $outlet, $ctx) {
        $perangkatke = "";
        if($devno != 1) 
            $perangkatke = " Perangkat ke-" . $devno;
        if (!is_null($dateStart) && !is_null($dateEnd) && !is_null($outlet)) {
            $header = "Shift " . str_replace(" " . substr($dateStart, 0, 4), "", formatdateindonesia($dateStart)) . ", " . $timeStart . " - " . str_replace(" " . substr($dateEnd, 0, 4), "", formatdateindonesia($dateEnd))  . ", " . $timeEnd . $perangkatke;
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
							<th>Uraian</th>
							<th>Total</th>
						</tr>      
					</thead>
					<tbody>
				<?php
					foreach ($result as $r) {             ?>
						<tr>
							<td><?= $r->Uraian?></td>
							<td><?= format_number($r->Total)?></td>
							
						</tr>
				<?php
					}
				?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php
        }
    }
?>