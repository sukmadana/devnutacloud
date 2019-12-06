<style type="text/css">
    td {
        vertical-align: middle !important;
    }

    .table-hapus td, .table-hapus th {
        border-top: 0px !important;
    }

    .table-hapus th {
        border-bottom: 1px solid #dddddd;
    }

    .dataTables_empty {
        text-align: center;
    }
</style>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <div class="page-breadcrumb-wrap" style="width: 100%">
                    <div class="page-breadcrumb-info">
                        <form class="form-horizontal pull-right" method="get" id="form-add" action="<?= base_url() ?>journal/formdata">
                            <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                                <div class="form-group col-md-12">
                                    <div class="col-md-2">
                                        <label class="control-label">Outlet</label>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="outlet" id="outlet" required
                                                onchange="selectOutlet()">
                                            <option></option>
                                            <?php foreach ($outlets as $key => $outlet): ?>
                                                <option value="<?= $key ?>" <?= $key == $selected_outlet ? "selected" : "" ?>>
                                                    <?= str_replace('#$%^', ' ', $outlet); ?>
                                                </option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="button" value="Tambah Jurnal"
                                            <?php if ($visibilityMenu['JournalAdd']) { ?>
                                                class="btn btn-primary"
                                            <?php } else { ?>
                                                class="btn btn-default"
                                            <?php } ?> onclick="validation()"/>
                                        <input type="hidden" name="ds" value="<?= $date_start ?>">
                                        <input type="hidden" name="de" value="<?= $date_end ?>">
                                    </div>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($selected_outlet) { ?>
<div class="row">
    <div class="col-md-12">
        <form action="<?= base_url("journal/?outlet=" . $selected_outlet) ?>" method="post" id="filter-date" class="form-horizontal">
            <div class="form-group">
                <label for="date_start" class="col-md-1 control-label">Mulai</label>
                <div class="col-md-3">
                    <div class="input-group date" id="datestart">
                        <input type="text" class="form-control"/>
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i>
                        </span>
                    </div>
                </div>

                <label for="date_end" class="col-md-1 control-label">Sampai</label>
                <div class="col-md-3">
                    <div class="input-group date" id="dateend">
                        <input type="text" class="form-control"/>
                        <span class="input-group-addon">
                            <i class="glyphicon glyphicon-th"></i>
                        </span>
                    </div>
                </div>
            </div>
            <input type="hidden" name="date_start" value="<?= $date_start; ?>"/>
            <input type="hidden" name="date_end" value="<?= $date_end; ?>"/>
        </form>

        <div class="box-widget widget-module">
            <div class="widget-head clearfix">
                <span class="h-icon"><i class="fa fa-table"></i></span>
                <h4><?= $title; ?></h4>
                <ul class="widget-action-bar pull-right">
                    <li>
                        <span class="widget-collapse waves-effect w-collapse"><i class="fa fa-angle-down"></i></span>
                    </li>
                </ul>
            </div>
            <div class="widget-container">
                <div class=" widget-block">
                    <input type="text" placeholder="Cari Transaksi" class="form-control" id="search-item"/>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered  table-striped " id="grid-item">
                            <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Tanggal</th>
                                <th>Nama Transaksi</th>
                                <th>Nomor Transaksi</th>
                                <th>Keterangan</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($journal as $row) { ?>
                                    <tr>
                                        <td><?= $row->JournalNumber; ?></td>
                                        <td><?= formatdateindonesia($row->JournalDate); ?> <?= $row->JournalTime; ?></td>
                                        <td><?= $row->TransactionName; ?></td>
                                        <td><?= $row->TransactionNumber; ?></td>
                                        <td><?= $row->Note; ?></td>
                                        <td>
                                            <a href="<?= base_url().'journal/formdata?journal_id='.$row->JournalID .'&outlet='.$row->DeviceID; ?>" class="btn btn-default" <?= ($row->MonthlyClosingJournal == 1 || $row->AnnualClosingJournal == 1) ? 'disabled' : '' ?>>Edit</a>
                                            <a href="javascript:void(0)" class="btn btn-default"
                                                <?= ($row->MonthlyClosingJournal == 1 || $row->AnnualClosingJournal == 1) ? 'disabled' : '' ?>
                                                onclick="deletingJournal(<?= $row->JournalID ?>, <?= $row->DeviceID ?>, '<?= $row->TransactionNumber; ?>')">
                                                Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>