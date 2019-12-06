<div class="modal fade" id="pilihan-ekstra-modal" tabindex="-1" role="dialog" aria-labelledby="kategori-modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="modal-title text-center" id="kategori-modal-title">Tambah Pilihan Ekstra </h4>
                <form id="form-pilihan-ekstra" class="form">
                    <table align="center" id="nuta-plihan-ekstra-table-form">
                        <tr>
                            <td colspan="5">
                                <input placeholder="<?= $pilihan_ekstra['PlaceholderPilihan']; ?>" class="form-control"
                                       type="text" id="namaEkstra">
                            </td>
                        </tr>
                        <tr style="border: 1px solid #e5e5e5;border-radius: 3px;">
                            <td colspan="4">Pelanggan hanya bisa pilih satu ekstra</td>
                            <td align="right"><input type="checkbox" id="checkPilihSatu"
                                                     class="switch-small" <?= $pilihan_ekstra['HanyaBisaPilihSatu'] == 'true' ? 'checked' : ''; ?>/>
                            </td>
                        </tr>
                        <?php if ($option->StockModifier == 1) { ?>
                            <tr style="border: 1px solid #e5e5e5;border-radius: 3px;">
                                <td colspan="4">Pelanggan bisa menambah jumlah per pilihan</td>
                                <td align="right"><input type="checkbox" id="checkBisaTambahJumlahPilihan"
                                                         class="switch-small" <?= $pilihan_ekstra['BisaMenambahJumlahPerPilihan'] == 'true' ? 'checked' : ''; ?>
                                                         onchange="changeBisaTambahJumlahPilihan(this)"/></td>
                            </tr>
                        <?php } ?>
                        <tr id="headerBisaTambahJumlahPilihan">
                            <td>Pilihan</td>
                            <td>Harga</td>
                            <td>Qty Dibutuhkan</td>
                            <td colspan="2">Satuan</td>
                        </tr>
                        <tr id="row-tambah-pilihan-ekstra">
                            <td colspan="2" align="center"><a href="#" id="tambah-baris-pilihan-ekstra">Tambah Baris</a></td>
                        </tr>
                    </table>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-default" id="hapusmodifier">Hapus</button>
                <button type="button" class="btn btn-primary has-spinner" id="btn-simpan-pilihan-ekstra">
                    <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
