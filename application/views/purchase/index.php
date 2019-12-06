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
</style>
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <div class="page-breadcrumb-wrap" style="width: 100%">
                    <div class="page-breadcrumb-info">
                        <form class="form-horizontal pull-right" method="get"
                              id="form-add" action="<?= base_url() ?>pembelian/form">
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
                                        <input type="button" value="Tambah Pembelian"
                                            <?php if ($visibilityMenu['PurchaseAdd']) { ?>
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
<div class="row">
    <div class="col-md-12">
        <?php if ($selected_outlet): ?>

            <form action="<?= base_url("pembelian/?outlet=" . $selected_outlet) ?>" method="post" id="filter-date"
                  class="form-horizontal">
                <?php //$this->load->view('features/filters/filter_date_mulai_sampai_horizontal'); ?>
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
                    <h4>Daftar pembelian</h4>
                    <ul class="widget-action-bar pull-right">
                        <li><span class="widget-collapse waves-effect w-collapse"><i
                                        class="fa fa-angle-down"></i></span></li>
                    </ul>
                </div>
                <div class="widget-container">
                    <div class=" widget-block">

						<input type="text" placeholder="Cari Item" class="form-control" id="search-item"/>
                        <!-- <input type="text" placeholder="Cari pembelian" class="form-control" id="search-item" style="margin-bottom: 20px"/> -->
                        <?php if (!$error_mesg): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered  table-striped " id="grid-item">
                                    <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Supplier</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($items as $key => $value): ?>
                                        <tr>
                                            <td><?= $value->PurchaseNumber ?></td>
                                            <td><?= date_format(date_create($value->PurchaseDate), "j M Y") ?>
                                                , <?= $value->PurchaseTime ?></td>
                                            <td><?= $value->SupplierName <> 'null' ? $value->SupplierName : '-'  ?></td>
                                            <td><?= format_number($value->Total) ?></td>
                                            <td>
                                                <?php if ($visibilityMenu['PurchaseEdit'] && ($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98)) { ?>
                                                    <a href="<?= base_url('/pembelian/edit/' . $value->DeviceID . '/' . $value->TransactionID . '.' . $value->DeviceNo . "?ds=" . $date_start . "&de=" . $date_end) ?>"
                                                       class="btn btn-default">Edit</a>
                                                <?php } if ($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98) {  ?>
                                                <a href="<?= base_url('/pembelian/view/' . $value->DeviceID . '/' . $value->TransactionID . '.' . $value->DeviceNo . "?ds=" . $date_start . "&de=" . $date_end) ?>"
                                                   class="btn btn-default">Lihat</a>
                                                <?php } if ( 1==0 && $visibilityMenu['PurchaseDelete'] && ($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98)
                                                ) { ?>
                                                    <button class="btn btn-default delete-button"
                                                            data-message="hapus <?= $value->PurchaseNumber ?>"
                                                            data-target=".delete-form-<?= $key ?>">Hapus
                                                    </button>
                                                <?php } ?>
                                                <form action="<?= base_url('/pembelian/destroy') ?>" method="post"
                                                      class="hidden delete-form-<?= $key ?>">
                                                    <input type="number" name="outlet" value="<?= $selected_outlet ?>">
                                                    <input type="number" name="id" value="<?= $value->TransactionID ?>">
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert"><?php echo $error_mesg ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= "" ?>