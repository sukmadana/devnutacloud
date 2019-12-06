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
                                    <th>Akun</th>
                                    <th style="text-align: right;">Saldo Awal</th>
                                    <th style="text-align: right;">Perubahan Debit</th>
                                    <th style="text-align: right;">Perubahan Kredit</th>
                                    <th style="text-align: right;">Total Perubahan</th>
                                    <th style="text-align: right;">Saldo Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($journal as $key => $val) {
                                    $total_balance = 0;
                                    $total_debit = 0;
                                    $total_credit = 0;
                                    $total_transaction = 0;
                                    $total_end_balance = 0;
                                    ?>
                                    <tr style="background-color: #ddd; font-weight: 500;">
                                        <td colspan="8"><?= $val->TypeCode; ?> - <?= $val->TypeName; ?></td>
                                    </tr>
                                    <?php
                                    foreach ($val->Account as $acc) {
                                        $subtotal_balance = 0;
                                        $subtotal_debit = 0;
                                        $subtotal_credit = 0;
                                        $subtotal_transaction = 0;
                                        $subtotal_end_balance = 0;
                                        ?>
                                        <tr style="background-color: #f5f5f5; font-weight: 500;">
                                            <td colspan="8" style="padding-left: 25px;"><?= $acc->AccountCode; ?> - <?= $acc->AccountName; ?></td>
                                        </tr>
                                        <?php
                                        foreach ($acc->Account as $jnl) {
                                            if ($jnl->IsDefault == true) continue;
                                            $balance = $jnl->Balance;
                                            $debit = $jnl->Debit;
                                            $credit = $jnl->Credit;
                                            $transaction = 0;
                                            if ($jnl->AccountType == 1 || $jnl->AccountType == 2 || $jnl->AccountType == 6 || $jnl->AccountType == 8 || $jnl->AccountType == 9) {
                                                $transaction = $debit - $credit;
                                            }else{
                                                $transaction = $credit - $debit;
                                            }
                                            $end_balance = $balance + $transaction;

                                            $subtotal_balance += $balance;
                                            $subtotal_debit += $debit;
                                            $subtotal_credit += $credit;
                                            $subtotal_transaction += $transaction;
                                            $subtotal_end_balance += $end_balance;
                                            ?>
                                            <tr style="background-color: #fff;">
                                                <td style="padding-left: 50px;"><?= $jnl->AccountCode; ?></td>
                                                <td><?= $jnl->AccountName; ?></td>
                                                <td><?= $jnl->AccountCode; ?> - <?= $jnl->AccountName; ?></td>
                                                <td style="text-align: right;"><?= format_number($balance) ?></td>
                                                <td style="text-align: right;"><?= format_number($debit); ?></td>
                                                <td style="text-align: right;"><?= format_number($credit); ?></td>
                                                <td style="text-align: right;"><?= format_number($transaction); ?></td>
                                                <td style="text-align: right;"><?= format_number($end_balance); ?></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        <tr style="text-align: right; background-color: #f3f3f3; font-weight: 500;">
                                            <td colspan="3">Jumlah <?= $acc->AccountName; ?></td>
                                            <td style="text-align: right;"><?= format_number($subtotal_balance); ?></td>
                                            <td style="text-align: right;"><?= format_number($subtotal_debit); ?></td>
                                            <td style="text-align: right;"><?= format_number($subtotal_credit); ?></td>
                                            <td style="text-align: right;"><?= format_number($subtotal_transaction); ?></td>
                                            <td style="text-align: right;"><?= format_number($subtotal_end_balance); ?></td>
                                        </tr>
                                        <?php

                                        $total_balance += $subtotal_balance;
                                        $total_debit += $subtotal_debit;
                                        $total_credit += $subtotal_credit;
                                        $total_transaction += $subtotal_transaction;
                                        $total_end_balance += $subtotal_end_balance;
                                    }
                                    ?>
                                    <tr style="text-align: right; background-color: #f7f7f7; font-weight: 500;">
                                        <td colspan="3">Total Jumlah <?= $val->TypeName; ?></td>
                                        <td style="text-align: right;"><?= format_number($total_balance); ?></td>
                                        <td style="text-align: right;"><?= format_number($total_debit); ?></td>
                                        <td style="text-align: right;"><?= format_number($total_credit); ?></td>
                                        <td style="text-align: right;"><?= format_number($total_transaction); ?></td>
                                        <td style="text-align: right;"><?= format_number($total_end_balance); ?></td>
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
