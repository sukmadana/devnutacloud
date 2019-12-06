<div class="form-group">
    <label for="date_start" class="col-md-4 control-label">Mulai</label>
    <div class="col-md-3">
        <div class="input-group date" id="datestart" >
            <input type="text" class="form-control"/>
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-th"></i>
            </span>
        </div>
    </div>

    <label for="date_end" class="col-md-1 control-label">Sampai</label>
    <div class="col-md-3">
        <div class="input-group date" id="dateend" >
            <input type="text" class="form-control"/>
        <span class="input-group-addon">
            <i class="glyphicon glyphicon-th"></i>
        </span>
        </div>
    </div>
</div>
<input type="hidden" id="date-start" name="date_start" value="<?= $date_start; ?>"/>
<input type="hidden" id="date-end" name="date_end" value="<?= $date_end; ?>"/>
