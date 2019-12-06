<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 23/01/2016
 * Time: 10:44
 */ ?>
<div class="form-group">
    <label for="date_start">Mulai</label>
    <div class="input-group date" id="datestart" style="max-width:185px;">
        <input type="text" class="form-control"/>
            <span class="input-group-addon">
                <i class="glyphicon glyphicon-th"></i>
            </span>
    </div>
</div>
<div class="form-group">
    <label for="date_end">Sampai</label>
    <div class="input-group date" id="dateend" style="max-width:185px;">
        <input type="text" class="form-control"/>
        <span class="input-group-addon">
            <i class="glyphicon glyphicon-th"></i>
        </span>
    </div>
</div>
<input type="hidden" name="date_start" value="<?= $date_start; ?>"/>
<input type="hidden" name="date_end" value="<?= $date_end; ?>"/>