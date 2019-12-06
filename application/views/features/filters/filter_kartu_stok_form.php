<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */
?>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
<form class="form-horizontal">
    <?php $this->load->view('features/filters/filter_date_mulai_sampai_horizontal'); ?>

    <div class="form-group">
        <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
            <label class="col-md-1 control-label">Outlet</label>

            <div class="col-md-3">
                <select class="form-control" name="outlet" id="outlet" onchange="getItemByOutlet(this)" data-live-search="true">
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
        <label class="col-md-1 control-label" style="text-align:center;">Item</label>

        <div class="col-md-3">
            <select class="selectpicker2" data-show-subtext="true" data-live-search="true" name="item" id="item">
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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>