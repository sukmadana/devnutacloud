<form class="form-horizontal">
    <?php $this->load->view('features/filters/filter_hapus_untildate_horizontal'); ?>
    <?php if (getLoggedInNamaPerusahaan() != "Individual") { ?>
        <div class="form-group">
            <label class="col-md-1 control-label">Outlet</label>

            <div class="col-md-3">
                <select class="form-control" onchange="this.form.submit()" name="outlet" id="outlet">
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
    
</form>