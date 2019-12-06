<?php
/*
 * This file created by Rahmat
 * Copyright 2017
 */
?>

<?php
foreach ($datagrid['result'] as $row) {
    $strShift = "";
    $opendate = $row->OpenDate;
    $opentime = $row->OpenTime;
    $closedate = $row->CloseDate;
    $closetime = $row->CloseTime;
    if($dateStart == $dateEnd) {
        if(empty($closedate) || $closedate == "") {
            $strShift = $opentime;
        } else {
            $strShift = $opentime . " - " . $closetime;
        }
    } else {
        if(substr($dateStart,0,4) == substr($dateEnd,0,4)) {
            if(empty($closedate) || $closedate == "") {
                $strShift = str_replace(" ".substr($opendate,0,4),"",
                        formatdateindonesia($opendate)) . ", " . $opentime;
            } else if ($opendate == $closedate) {
                $strShift = str_replace(" ".substr($opendate,0,4),"",
                        formatdateindonesia($opendate)) . ", " . $opentime . " - " . $closetime;
            } else {
                $strShift = str_replace(" ".substr($opendate,0,4),"",
                        formatdateindonesia($opendate)) . ", " . $opentime . " - " .
                    str_replace(" ".substr($closedate,0,4),"",
                        formatdateindonesia($closedate)) . ", " . $closetime;
            }
        } else {
            if(empty($closedate) || $closedate == "") {
                $strShift = formatdateindonesia($opendate) . " " . $opentime;
            } else if ($opendate == $closedate) {
                $strShift = formatdateindonesia($opendate) . " " . $opentime . " - " . $closetime;
            } else {
                $strShift = formatdateindonesia($opendate) . " " . $opentime . " - " .
                    formatdateindonesia($closedate) . " " . $closetime;
            }
        }
    }

    ?>

    <div class="widget-head clearfix">
        <span class="h-icon"><i class="fa fa-table"></i></span>
        <h4>Shift <?= $strShift; ?></h4>
        <ul class="widget-action-bar pull-right">
            <li><span class="widget-collapse waves-effect w-collapse"><i
                        class="fa fa-angle-down"></i></span>
            </li>
        </ul>
    </div>
    <div class="widget-container">
        <div class=" widget-block">

        </div>
    </div>
    <?php
}