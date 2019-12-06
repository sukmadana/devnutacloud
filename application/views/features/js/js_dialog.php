<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 06/01/2016
 * Time: 15:07
 */
$msg = '<div class=row>
<div class="col-md-3">

</div>
<div class="col-md-9">
<p>Daftarkan perusahaan anda untuk mendapatkan ID perusahaan.</p>
</div>

</div>
<div class=row>
<div class="col-md-3">

</div>
<div class="col-md-9">
<p>ID perusahaan dapat menggabungkan laporan semua cabang/tablet anda dalam satu akun nutacloud.</p>
</div>

</div>';
?>
<script src="<?= base_url(); ?>js/bootbox.js"></script>
<script type="text/javascript">

    jQuery(document).ready(function ($) {
        bootbox.dialog({
            size: 'large',
            closeButton: false,


            /*message: '<div class=row>'+
             '<div class="col-md-2">'+
             '<img src="<?=base_url();?>images/help-id-perusahaan-1.png"/>'+
             '</div>'+
             '<div class="col-md-10">'+
             '<p>Daftarkan perusahaan anda untuk mendapatkan ID perusahaan.</p>'+
             '</div>'+

             '</div>'+
             '<div class=row>'+
             '<div class="col-md-2">'+
             '<img src="<?=base_url();?>images/help-id-perusahaan-2.png"/>'+
             '</div>'+
             '<div class="col-md-10">'+
             '<p>ID perusahaan dapat menggabungkan laporan semua cabang/tablet anda dalam satu akun nutacloud.</p>'+
             '</div>'+

             '</div>'*/
            message: '<form class="form-horizontal">' +
            '<div class="form-group">' +
            '<label class="col-md-12 control-label" style="text-align: left;padding-top:0px;">' +
            '<img src="<?=base_url();?>images/help-id-perusahaan-1.png" style="display:inline-block;" /> ' +
            '<p style="display: inline-block">Daftarkan perusahaan anda untuk mendapatkan ID perusahaan.</p>' +
            '</label>' +
            '</div>' +
            '<div class="form-group">' +
            '<label class="col-md-12 control-label" style="text-align: left;padding-top:0px;">' +
            '<img src="<?=base_url();?>images/help-id-perusahaan-2.png" style="display:inline-block;" /> ' +
            '<p style="display: inline-block">ID perusahaan dapat menggabungkan laporan semua cabang/outlet Anda dalam satu akun nutacloud.</p>' +
            '</label>' +
            '</div>' + '<div class="form-group">' +
            '<label class="col-md-12 control-label" style="text-align: center;padding-top:0px;">' +
            '<img src="<?=base_url();?>images/help-id-perusahaan-3.png" class="img-responsive"  style="margin:0 auto;" /> ' +
            '</label>' +
            '</div>' +
            '</form>',
            buttons: [{
                label: "Daftar sekarang",
                className: 'btn-primary',
                callback: function () {
                    $('.box-widget').animate({opacity: 1}, 1000);
                }
            }]
        });
    });
</script>

