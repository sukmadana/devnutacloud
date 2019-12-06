<?php
//echo '<pre>'.print_r($result,1).'</pre>'; die;
    for ($i=0; $i<count($result); $i++) {
        $r = $result[$i];
        
		printShift($r->OpenDate, $r->OpenTime, $r->CloseDate, $r->CloseTime, $r->Details,$r->Fields, $outlet, $this);
    }
	
    function printShift($dateStart, $timeStart, $dateEnd, $timeEnd, $results,$fields, $outlet, $ctx) {
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
                        <table class="table table-bordered  table-striped ">
                            <thead>
                            <tr>
                                <?php foreach ($fields as $field) { ?>
                                    <th>
                                        <?= CamelToWords($field->name); ?>
                                    </th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($results as $row) { ?>
                                <tr>
                                    <?php foreach ($fields as $field) { ?>
                                        <td>
                                            <?php

                                            $fieldname = $field->name;
                                            $lowerfieldname = strtolower($fieldname);
                                            if($lowerfieldname == 'hpp') {
                                                if (isNotEmpty($row->RincianHpp)) {
                                                    echo '<a target="_blank" href="rincianhpp?outlet=' . $outlet . '&detailid=' . $row->RincianHpp . '">' . format_number($row->$fieldname) . '</a>';
                                                } else {
                                                    echo "" . format_number($row->$fieldname);
                                                }
                                            }
                                            else if (strpos($lowerfieldname, 'total') !== FALSE ||
                                                strpos($lowerfieldname, 'harga') !== FALSE ||
                                                strpos($lowerfieldname, 'laba') !== FALSE ||
                                                $lowerfieldname == 'hpp'
                                            ) {
                                                echo "" . format_number($row->$fieldname);
                                            } else if ($lowerfieldname == 'rincianhpp') {

                                                if (isNotEmpty($row->$fieldname)) {
                                                    //echo 'Lihat Rincian dengan DetailID : ' . $row->$fieldname;
                                                    echo '<a target="_blank" href="rincianhpp?outlet=' . $outlet . '&detailid=' . $row->$fieldname . '" class="btn btn-default">Lihat Rincian</a>';
                                                    //echo '<a href="#" data-toggle="modal" class="btn btn-default" data-target="#rincian-hpp-dialog" data-id="' . $row->$fieldname . '" data-name="' . $row->Item . '" data-qty="' . $row->Qty . '">Lihat Rincian</a>';
                                                }
                                            } else {
                                                echo $row->$fieldname;
                                            }
                                            ?>
                                        </td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
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