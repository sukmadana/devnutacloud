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
                        <table class="table table-bordered table-striped dt-table-export">
                            <thead>
                                <tr>
                                    <th width="50%">AKTIVA</th>
                                    <th width="50%">PASIVA</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr style="background-color: #fff;">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span style="font-weight: 500;"><?= $account[1]->TypeName; ?></span>
                                            </div>
                                        </div>

                                        <?php
                                            $total_aktiva = 0;
                                            $total_pasiva = 0;

                                            $total_lancar = 0;
                                            $total_tidak_lancar = 0;
                                            $total_hutang = 0;
                                            $total_modal = 0;
                                        ?>
                                        
                                        <?php foreach ($account[1]->Account as $row) { ?>
                                            <?php
                                            $lancar = 0;
                                            foreach ($row->Balance as $x1) {
                                                $saldo = isNotEmpty($x1->Saldo) ? (double)$x1->Saldo : 0;
                                                $lancar += $saldo;
                                                $total_lancar += $saldo;
                                                $total_aktiva += $saldo;
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <span style=""><?= $row->AccountName; ?></span>
                                                </div>
                                                <div class="col-md-5">
                                                    Rp. <span class="pull-right"><?= format_number($lancar); ?>,00</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span style="font-weight: 500;"><?= $account[3]->TypeName; ?></span>
                                            </div>
                                        </div>
                                        <?php foreach ($account[3]->Account as $row) { ?>
                                            <?php
                                            $hutang = 0;
                                            foreach ($row->Balance as $x1) {
                                                $saldo = isNotEmpty($x1->Saldo) ? (double)$x1->Saldo : 0;
                                                $hutang += $saldo;
                                                $total_hutang += $saldo;
                                                $total_pasiva += $saldo;
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <span style=""><?= $row->AccountName; ?></span>
                                                </div>
                                                <div class="col-md-5">
                                                    Rp. <span class="pull-right"><?= format_number($hutang); ?>,00</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-7"><span style="font-weight: 500;">Jumlah <?= $account[1]->TypeName; ?></span></div>
                                            <div class="col-md-5">
                                                Rp. <span class="pull-right"><?= format_number($total_lancar); ?>,00</span>
                                            </div>
                                        </div>
                                        
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-7"><span style="font-weight: 500;">Jumlah <?= $account[3]->TypeName; ?></span></div>
                                            <div class="col-md-5">
                                                Rp. <span class="pull-right"><?= format_number($total_hutang); ?>,00</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr style="background-color: #fff;">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span style="font-weight: 500;"><?= $account[2]->TypeName; ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php foreach ($account[2]->Account as $row) { ?>
                                        <?php
                                        $tidak_lancar = 0;
                                        foreach ($row->Balance as $x1) {
                                            $saldo = isNotEmpty($x1->Saldo) ? (double)$x1->Saldo : 0;
                                            $tidak_lancar += $saldo;
                                            $total_tidak_lancar += $saldo;
                                            $total_aktiva += $saldo;
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-7">
                                                <span style=""><?= $row->AccountName; ?></span>
                                            </div>
                                            <div class="col-md-5">
                                                Rp.<span class="pull-right"><?= format_number($tidak_lancar); ?>,00</span>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span style="font-weight: 500;"><?= $account[4]->TypeName; ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php foreach ($account[4]->Account as $row) { ?>
                                            <?php
                                            $modal = 0;
                                            foreach ($row->Balance as $x1) {
                                                $saldo = isNotEmpty($x1->Saldo) ? (double)$x1->Saldo : 0;
                                                $modal += $saldo;
                                                $total_modal += $saldo;
                                                $total_pasiva += $saldo;
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <span style=""><?= $row->AccountName; ?></span>
                                                </div>
                                                <div class="col-md-5">
                                                    Rp.<span class="pull-right"><?= format_number($hutang); ?>,00</span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        
                                    </td>
                                </tr>
                                <tr style="background-color: #f9f9f9;">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-7"><span style="font-weight: 500;">Jumlah <?= $account[2]->TypeName; ?></span></div>
                                            <div class="col-md-5">
                                                Rp. <span class="pull-right"><?= format_number($total_tidak_lancar); ?>,00</span>
                                            </div>
                                        </div>
                                        
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-7"><span style="font-weight: 500;">Jumlah <?= $account[4]->TypeName; ?></span></div>
                                            <div class="col-md-5">
                                                Rp. <span class="pull-right"><?= format_number($total_modal); ?>,00</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr style="font-weight: 500;">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-7"><span style="font-weight: 500;">TOTAL AKTIVA</span></div>
                                            <div class="col-md-5">
                                                Rp. <span class="pull-right"><?= format_number($total_aktiva); ?>,00</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-7"><span style="font-weight: 500;">TOTAL PASIVA</span></div>
                                            <div class="col-md-5">
                                                Rp. <span class="pull-right"><?= format_number($total_pasiva); ?>,00</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
