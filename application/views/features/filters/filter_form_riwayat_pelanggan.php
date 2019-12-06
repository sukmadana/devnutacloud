<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015
 */

    if (strlen($selected_customer) > 0) {
        $tmp = explode('.', $selected_customer);
        $customerid = $tmp[0];
        $customerdeviceno = $tmp[1];
    } else {
        $customerid = '0';
        $customerdeviceno = '0';
    }
?>
<form class="form-horizontal" id="frm">
    <div class="form-group">
        <label class="col-md-1 control-label">Outlet</label>
        <div class="col-md-3">
            <select class="form-control" name="outlet" id="outlet" onchange="this.form.submit()">
                <?php foreach ($outlets as $k => $v) { ?>
                    <?php if ($k == $selected_outlet) { ?>
                        <option value="<?= $k; ?>" selected=""><?= str_replace('#$%^',' - ',$v); ?></option>
                    <?php } else { ?>
                        <option value="<?= $k; ?>"><?= str_replace('#$%^',' - ',$v); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        
        <label class="col-md-1 control-label">Pelanggan</label>
        <div class="col-md-3">
            <select class="form-control" name="customer" id="customer">
                <?php foreach ($customers as $k => $v) { ?>
                    <?php if ($v->CustomerID == $customerid && $v->DeviceNo == $customerdeviceno) { ?>
                        <option value="<?= $v->CustomerID . "."
                         . $v->DeviceNo ?>" selected=""><?= str_replace('#$%^',' - ',$v->CustomerName); ?></option>
                    <?php } else { ?>
                        <option value="<?= $v->CustomerID . "."
                         . $v->DeviceNo ?>"><?= str_replace('#$%^',' - ',$v->CustomerName); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-1 control-label"></label>
        <div class="col-md-3">
            <input type="radio" name="usedate" value="1" <?=(int)$this->input->get('usedate') != 0 ? "checked": ""?> onchange="this.form.submit()"> Periode Tertentu<br>
            <input type="radio" name="usedate" value="0" <?=(int)$this->input->get('usedate') == 0 ? "checked": ""?> onchange="this.form.submit()"> Riwayat Keseluruhan<br>
        </div>
    </div>
    <?php 
        if ((int)$this->input->get('usedate') != 0) {
            $this->load->view('features/filters/filter_date_mulai_sampai_horizontal');
        } 
    ?>
    <div class="form-group">
        <label class="col-md-1 control-label">&nbsp;</label>

        <div class="col-md-11">
            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Proses</button>
            </div>
        </div>
    </div>
</form>