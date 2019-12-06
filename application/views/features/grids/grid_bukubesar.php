<?php
?>
<div class="row">
    <div class="col-md-12">
        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4><?= $title; ?></h4>
                <ul class="widget-action-bar pull-right">
                    <li><span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span></li>
                </ul>
            </div>
            <div class="widget-container">
                <div class=" widget-block">
                    <?php $this->load->view('features/filters/filter_form'); ?>
                    <hr/>
                    <div class="table-responsive">
                        <table class="table table-bordered  table-striped dt-table-export ">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th>Nama Akun</th>
                                    <th>No Jurnal</th>
                                    <th>Tanggal Jurnal</th>
                                    <th>Nama Transaksi</th>
                                    <th>Nomor Transaksi</th>
                                    <th style="text-align: right;">Debit</th>
                                    <th style="text-align: right;">Kredit</th>
                                    <th style="text-align: right;">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                
                                foreach ($journal as $key => $val) {
                                    $debit = 0;
                                    $credit = 0;
                                    $balance = $val->SaldoAwal;
                                    ?>
                                    <tr style="font-weight: 500; background-color: #ddd;"><td colspan="9"><?= $val->AccountCode; ?> - <?= $val->AccountName; ?></td></tr>
                                    <tr style="background-color: #f3f3f3;">
                                        <td><?= $val->AccountCode; ?></td>
                                        <td><?= $val->AccountName; ?></td>
                                        <td></td>
                                        <td><?= formatdateindonesia($date_start); ?> 00:00</td>
                                        <td>Saldo Awal</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;"><?= format_number($balance); ?></td>
                                    </tr>
                                    <?php
                                    
                                    foreach ($val->Journal as $row) {
                                        if ($val->AccountType == 1 || $val->AccountType == 2 || $val->AccountType == 6 || $val->AccountType == 8 || $val->AccountType == 9) {
                                            $balance += ((double)$row->Debit - (double)$row->Credit);
                                        }else{
                                            $balance += ((double)$row->Credit - (double)$row->Debit);
                                        }
                                        
                                        ?>
                                        <tr style="background-color: #fff;">
                                            <td><?= $row->AccountCode; ?></td>
                                            <td><?= $row->AccountName; ?></td>
                                            <td><?= $row->JournalNumber; ?></td>
                                            <td><?= formatdateindonesia($row->JournalDate) ?>  <?= $row->JournalTime; ?></td>
                                            <td><?= $row->TransactionName; ?></td>
                                            <td><?= $row->TransactionNumber; ?></td>
                                            <td style="text-align: right;"><?= format_number($row->Debit); ?></td>
                                            <td style="text-align: right;"><?= format_number($row->Credit); ?></td>
                                            <td style="text-align: right;"><?= format_number($balance); ?></td>
                                        </tr>
                                        <?php
                                        $debit += $row->Debit;
                                        $credit += $row->Credit;
                                        
                                    }
                                    ?>
                                    <tr style="background-color: #f3f3f3;">
                                        <td><?= $val->AccountCode; ?></td>
                                        <td><?= $val->AccountName; ?></td>
                                        <td></td>
                                        <td><?= formatdateindonesia($date_end); ?> 23:59</td>
                                        <td>Saldo Akhir</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;"><?= format_number($balance); ?></td>
                                    </tr>
                                    <tr style="background-color: #fff; font-weight: 500;">
                                        <td colspan="6"></td>
                                        <td style="text-align: right;"><?= format_number($debit); ?></td>
                                        <td style="text-align: right;"><?= format_number($credit); ?></td>
                                        <td style="text-align: right;"></td>
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
    </div>
</div>
