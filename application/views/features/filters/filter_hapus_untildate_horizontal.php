<div class="form-group">
    <label for="date_start" class="col-md-1 control-label">Sampai Tanggal</label>
    <div class="col-md-3">
        <div class="input-group date" id="datestart" >
            <input type="text" onchange="this.form.submit()" class="form-control"/>
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-th"></i>
            </span>
        </div>
    </div>
</div>
<input type="hidden" name="date_start" value="<?= $date_start; ?>"/>
