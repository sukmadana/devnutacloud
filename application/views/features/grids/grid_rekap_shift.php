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
    if($row->DeviceNo > 1) {
        $strShift .= " Perangkat ke-" . $row->DeviceNo;
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
            <div class="table-responsive">
                <table class="table table-bordered  table-striped ">
                    <tr>
                        <td>Dibuka Oleh</td>
<!--                        <td>:</td>-->
                        <td><?= $row->OpenedBy; ?></td>
                    </tr>
                    <tr>
                        <td>Modal Awal</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->StartingCash); ?></td>
                    </tr>
                    <tr>
                        <td>Total Penjualan</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->TotalSales); ?></td>
                    </tr>
                    <tr>
                        <td><i style="margin-left: 10px;"></i>Tunai</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->TotalCashSales); ?></td>
                    </tr>
                    <tr>
                        <td><i style="margin-left: 10px;"></i>Kartu/Non Tunai</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->TotalSales - $row->TotalCashSales); ?></td>
                    </tr>
                    <tr>
                        <td>Pemasukan Lain</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->OtherIncome); ?></td>
                    </tr>
                    <tr>
                        <td>Pengeluaran</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->Expense); ?></td>
                    </tr>
                    <tr>
                        <td>Uang di Laci</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->StartingCash + $row->TotalCashSales + $row->OtherIncome - $row->Expense); ?></td>
                    </tr>
                    <tr>
                        <td>Uang Ditarik</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->Withdrawal); ?></td>
                    </tr>
                    <tr>
                        <td>Sisa Uang Sistem</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->ExpectedBalance); ?></td>
                    </tr>
                    <tr>
                        <td>Sisa Uang Aktual</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->ActualBalance); ?></td>
                    </tr>
                    <tr>
                        <td>Selisih</td>
<!--                        <td>:</td>-->
                        <td><?= $this->currencyformatter->format($row->Difference); ?></td>
                    </tr>
                    <tr>
                        <td>Ditutup Oleh</td>
<!--                        <td>:</td>-->
                        <td><?= $row->ClosedBy; ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <?php
}