<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
<form class="form-horizontal">
    <?php $this->load->view('features/filters/filter_date_mulai_sampai_horizontal'); ?>
    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
        <div class="form-group">
            <label class="col-md-1 control-label">Outlet</label>

            <div class="col-md-3">
                <select class="form-control" name="outlet" id="outlet">
                    <?php if (count($outlets) > 1) { ?>
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
            <label class="col-md-1 control-label">Rekap per</label>

            <div class="col-md-3">
                <select class="form-control" name="rekapper" id="rekapper">
                    <option value="item" <?php if ($selected_rekapper === "item") {
                        echo 'selected=""';
                    } ?>>Item
                    </option>
                    <option value="pelanggan" <?php if ($selected_rekapper === "pelanggan") {
                        echo 'selected=""';
                    } ?>>Pelanggan
                    </option>
                    <option value="pelangganitem" <?php if ($selected_rekapper === "pelangganitem") {
                        echo 'selected=""';
                    } ?>>Item &amp; Pelanggan
                    </option>
                    <option value="kategori" <?php if ($selected_rekapper === "kategori") {
                        echo 'selected=""';
                    } ?>>Kategori
                    </option>
                    <option value="kategoriitem" <?php if ($selected_rekapper === "kategoriitem") {
                        echo 'selected=""';
                    } ?>>Kategori dan Item
                    </option>
                    <option value="user" <?php if ($selected_rekapper === "user") {
                        echo 'selected=""';
                    } ?>>User
                    </option>

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