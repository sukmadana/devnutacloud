<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/05/2016
 * Time: 14:27
 */ ?>
<div class="modal fade" id="simpan-item-modal" tabindex="-1" role="dialog" aria-labelledby="simpan-item-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center" id="label-item-akan-dihapus"></p>
                <table cellpadding="50" cellspacing="50" class="table table-hapus">
                    <tr>
                        <th><input type="checkbox" id="all-simpan-item-selected"/>Semua</th>
                        <th>Nama Outlet</th>
                        <th>Alamat Outlet</th>
                    </tr>
                    <?php foreach ($outlets as $k => $outlet) {
                        $o = explode('#$%^', $outlet);
                        ?>
                        <tr>
                            <td><input type="checkbox"
                                       class="all-simpan-item-item"
                                       data-tag="<?= $k . '#@#' . $o[0]; ?>"
                                    <?= $selected_outlet == $k ? 'checked' : ''; ?>/></td>
                            <td><?= $o[0]; ?></td>
                            <td><?= $o[1]; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer" style="border-top:0px">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary has-spinner"
                        id="btn-simpan-item-modal" data-dimiss="modal">
                    <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
