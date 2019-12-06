<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 12/12/2015
 * Time: 16:02
 */ ?>

<script type="text/javascript">

    function setOutletNonAktif(outletid, namaoutlet, object) {
        swal({
            title: "Konfirmasi nonaktif Outlet",
            text: "Anda akan menonaktifkan outlet " + namaoutlet + ", Outlet yang sudah non-aktif hanya bisa diaktifkan kembali dengan cara login ke tablet. Lanjutkan",
            type: "warning",
            showCancelButton: true,
            cancelButtonText: "Tidak",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                jQuery.post("<?=base_url() . 'ajax/setoutletnonaktif';?>", {
                    "id": outletid,
                }, function (data) {
                    var obj = JSON.parse(data);
                    if (obj.code == 200) {
                        window.location.replace("<?=base_url() . 'perusahaan/akunsaya';?>");
                    }
                });
            } else {
                jQuery(object).click();

            }
        });
    }
</script>
