<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/05/2016
 * Time: 14:27
 */ ?>
<div class="modal fade" id="hapus-kategori-modal" tabindex="-1" role="dialog" aria-labelledby="hapus-kategori-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center" id="label-kategori-akan-dihapus"></p>
                <table cellpadding="50" cellspacing="50" class="table table-hapus">
                    <tr>
                        <th><input type="checkbox" id="all-selected-kategori"/>Semua</th>
                        <th>Nama Outlet</th>
                        <th>Alamat Outlet</th>
                    </tr>
                    <?php foreach ($outlets as $k => $outlet) {
                        $o = explode('#$%^', $outlet);
                        ?>
                        <tr>
                            <td><input type="checkbox"
                                       class="all-item-kategori"
                                       data-tag="<?= $k . '#@#' . $o[0]; ?>"
                                    <?= $selected_outlet == $k ? 'checked="checked" onclick="return false"' : ''; ?>/>
                            </td>
                            <td><?= $o[0]; ?></td>
                            <td><?= $o[1]; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer" style="border-top:0px">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-hapus-kategori-modal" data-dimiss="modal">Hapus
                </button>
            </div>
        </div>
    </div>
</div>

