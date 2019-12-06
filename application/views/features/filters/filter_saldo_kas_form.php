<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
<form class="form-horizontal">
    <?php $this->load->view('features/filters/filter_date_horizontal'); ?>
    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
        <div class="form-group">
            <label class="col-md-1 control-label">Outlet</label>

            <div class="col-md-3">
                <select class="form-control" name="outlet" id="outlet">
                    <?php foreach ($outlets as $k => $v) { ?>
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
    <div class="form-group">
        <label class="col-md-1 control-label">&nbsp;</label>

        <div class="col-md-11">
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Proses</button>
            </div>
        </div>
    </div>
</form>