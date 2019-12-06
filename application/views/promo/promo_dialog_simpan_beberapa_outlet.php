<div class="modal fade" id="simpan-promo-modal" tabindex="-1" role="dialog" aria-labelledby="simpan-item-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <p style="text-align: center" id="label-item-akan-dihapus"></p>
                <table cellpadding="50" cellspacing="50" class="table table-hapus">
                    <tr>
                        <th><input type="checkbox" id="all-simpan-promo-selected"/>Semua</th>
                        <th>Nama Outlet</th>
                        <th>Alamat Outlet</th>
                    </tr>
                    <?php foreach ($outlets as $k => $outlet) {
                        $o = explode('#$%^', $outlet);
                        ?>
                        <tr>
                            <td><input type="checkbox"
                                       class="all-simpan-promo-item"
                                       data-tag="<?= $k . '#@#' . $o[0]; ?>"
                                    <?= $selected_outlet == $k ? 'checked' : ''; ?>/></td>
                            <td><?= $o[0]; ?></td>
                            <td><?= $o[1]; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer" style="border-top:0px">
                <div class="form-group">
                    <div id="myProgress">
                        <div id="myBar"></div>
                    </div>
                    <br>
                    <span id="outletzz">Loading</span>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-simpan-promo-modal" data-dimiss="modal">
                    <i class="fa fa-spinner fa-spin load" style="display: none"></i>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
