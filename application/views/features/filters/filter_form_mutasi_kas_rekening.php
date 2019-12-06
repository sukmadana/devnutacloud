<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
<form class="form-horizontal">
    <?php $this->load->view('features/filters/filter_date_mulai_sampai_horizontal'); ?>

    <div class="form-group">
        <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
            <label class="col-md-1 control-label">Outlet</label>

            <div class="col-md-3">
                <select class="form-control" name="outlet" id="outlet" onchange="getKasRekeningByOutlet(this)">
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
        <?php } ?>
        <label class="col-md-1 control-label" style="text-align:center;">Kas / Rekening</label>

        <div class="col-md-3">
            <select class="form-control" name="item" id="item">
                <?php foreach ($items as $k => $v) { ?>
                    <?php if ($k == $selected_item) { ?>
                        <option value="<?= $k; ?>" selected=""><?= $v; ?></option>
                    <?php } else { ?>
                        <option value="<?= $k; ?>"><?= $v; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-1 control-label">&nbsp;</label>

        <div class="col-md-11">
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Proses</button>
            </div>
        </div>
    </div>
</form>