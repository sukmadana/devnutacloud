<form action="<?=base_url('produk/import_act')?>" enctype="multipart/form-data" method="post">
<div class="container-fluid">
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-12">
                <div class="page-breadcrumb-wrap" style="width: 100%">
                    <div class="page-breadcrumb-info">

                        <div class="pull-right">
                            <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
                                <div class="row">
                                    <div class="col-md-2" style="padding:10px">
                                        <label class="control-label">Outlet</label>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-9">
                                        <select class="form-control" name="outlet" id="outlet">
                                            <?php
                                            if (count($outlets) > 1) { ?>
                                                <option value="-999" <?= $selected_outlet == -999 ? 'selected' : '' ?>>
                                                    &nbsp;
                                                </option>
                                            <?php }
                                            foreach ($outlets as $k => $v) { ?>
                                                <?php if ($k == $selected_outlet) { ?>
                                                    <option value="<?= $k; ?>"
                                                            selected=""><?= str_replace('#$%^', ' ', $v); ?></option>
                                                <?php } else { ?>
                                                    <option
                                                            value="<?= $k; ?>"><?= str_replace('#$%^', ' ', $v); ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <input name="fContent" type="file" class="form_control" accept=".xls,.xlsx">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary btn-block">Import</button>
    </div>
</div>
</form>