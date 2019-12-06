<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/05/2016
 * Time: 14:27
 */ ?>
<div class="modal fade" id="satuan-modal" tabindex="-1" role="dialog" aria-labelledby="satuan-modal"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="satuan-modal-title">&nbsp;</h4>
            </div>
            <div class="modal-body">
                <form id="form-satuan">
                    <div class="form-group">
                        <label for="txt-satuan" class="control-label">Satuan</label>
                        <input type="text" class="form-control" id="txt-satuan" name="txtsatuan">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-simpan-satuan" name="btn-simpan">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

