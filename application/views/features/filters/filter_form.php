<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
<form class="form-horizontal">
    <div class="row">
        <div class="col-md-3 col-xs-12">
        <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
            <div class="form-group">
                <label class="col-md-4 col-xs-4 control-label">Outlet</label>

                <div class="col-md-8 col-xs-8">
                    <select class="form-control" name="outlet" id="outlet">
                        <?php echo $selected_outlet;
                        if (count($outlets) > 1) { ?>
                            <option value="Semua" <?= $selected_outlet === "Semua" ? 'selected=""' : ''; ?>>Semua</option>
                        <?php }
                        foreach ($outlets as $k => $v) { ?>
                            <?php if ($k == $selected_outlet) { ?>
                                <option value="<?= $k; ?>" selected=""><?= str_replace('#$%^', ' - ', $v); ?></option>
                            <?php } else { ?>
                                <option value="<?= $k; ?>"><?= str_replace('#$%^', ' - ', $v); ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        </div>
        
        <?php $this->load->view('features/filters/filter_date_mulai_sampai_horizontal'); ?>

        <div class="col-md-2">
        <button class="btn btn-primary col-md-12 col-xs-6 col-xs-offset-6" type="submit">Terapkan</button>

        </div>
    </div>
</form>