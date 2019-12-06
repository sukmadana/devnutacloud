<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/05/2016
 * Time: 14:27
 */ ?>
<div class="modal fade" id="variasi-harga-modal" tabindex="-1" role="dialog" aria-labelledby="kategori-modal"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div id="variasi-harga-container">
                    <h4 class="modal-title text-center" id="kategori-modal-title">Tambah Variasi Harga</h4>
                    <form id="form-variasi-harga">
                        <table align="center">

                            <tr id="row-tambah-variasi-harga">
                                <td colspan="2" align="center"><a href="#" id="tambah-baris-variasi-harga">Tambah
                                        Baru</a></td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div id="loading-variasi-harga-container" class="loadmask">
                    <div style="left: 50%;top: 50%;transform: translate(-50%,50%);" class="loadmask-msg">
                        <div class="clearfix">
                            <div class="w-loader"></div>
                            <span class="w-mask-label">Loading..<span></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary has-spinner" id="btn-simpan-variasi-harga">
                    <span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
