<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/05/2016
 * Time: 14:27
 */ ?>
<div class="modal fade" id="kategori-modal" tabindex="-1" role="dialog" aria-labelledby="kategori-modal"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="kategori-modal-title">Tambah Kategori</h4>
            </div>
            <div class="modal-body">
                <form id="form-kategori">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Kategori</label>
                        <input type="text" class="form-control" id="txt-kategori">
                    </div>
                </form>
            </div>
            <div class="form-group" style="display: none">
                <label class="col-md-4 control-label" id="label-nama-item">Cetak Ke</label>
            </div>
            <div class="btn-group" data-toggle="buttons" style="display: none">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="option1"> Dapur
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="option2"> Bar
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="option3"> Tidak Cetak
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary has-spinner" id="btn-simpan-kategori">
                    <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

