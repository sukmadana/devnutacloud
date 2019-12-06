<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/05/2016
 * Time: 14:27
 */ ?>
<div class="modal fade" id="hapus-item-modal" tabindex="-1" role="dialog" aria-labelledby="hapus-item-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div id="hapus-item-container">
                    <p style="text-align: center" id="label-satuan-akan-dihapus"></p>
                    <table cellpadding="50" cellspacing="50" class="table table-hapus">
                        <tr>
                            <th><input type="checkbox" id="all-selected-item"/>Semua</th>
                            <th>Nama Outlet</th>
                            <th>Alamat Outlet</th>
                        </tr>
                        <?php foreach ($outlets as $k => $outlet) {
                            $o = explode('#$%^', $outlet);
                            ?>
                            <tr>
                                <td><input type="checkbox"
                                           class="all-item-item"
                                           data-tag="<?= $k . '#@#' . $o[0]; ?>"
                                        <?= $selected_outlet == $k ? 'checked onclick="return false"' : ''; ?>/></td>
                                <td><?= $o[0]; ?></td>
                                <td><?= $o[1]; ?></td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
                <div id="loading-hapus-item-container" class="loadmask">
                    <div style="left: 50%;top: 50%;transform: translate(-50%,50%);" class="loadmask-msg">
                        <div class="clearfix">
                            <div class="w-loader"></div>
                            <span class="w-mask-label">Loading..<span></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:0px">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-hapus-item-modal" data-dimiss="modal">Hapus
                </button>
            </div>
        </div>
    </div>
</div>

