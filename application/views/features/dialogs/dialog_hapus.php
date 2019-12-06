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
                <p style="text-align: center" id="label-item-akan-dihapus"></p>
                <table cellpadding="50" cellspacing="50" class="table table-hapus">
                    <tr>
                        <th><input type="checkbox" id="all-selected"/>Semua</th>
                        <th>Nama Outlet</th>
                        <th>Alamat Outlet</th>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="all-item"/></td>
                        <td>Bakpiapia Jakal</td>
                        <td>Jl. Kaliurang</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="all-item"/></td>
                        <td>Bakpiapia Jakal</td>
                        <td>Jl. Kaliurang</td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" class="all-item"/></td>
                        <td>Bakpiapia Jakal</td>
                        <td>Jl. Kaliurang</td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer" style="border-top:0px">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btn-hapus-kategori-modal" data-dimiss="modal">Hapus</button>
            </div>
        </div>
    </div>
</div>