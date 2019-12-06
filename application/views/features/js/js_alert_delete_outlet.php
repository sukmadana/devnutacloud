<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 12/12/2015
 * Time: 16:02
 */ ?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('#konfirmdeleteoutlet').on('click', function (e) {
            e.preventDefault();
            swal({
                title: "Hapus outlet <?=$nama_outlet;?> ?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    $.post("<?=base_url() . 'ajax/deleteuser';?>", {
                        "username": '<?=$id_outlet;?>',
                    }, function (data) {
                        var obj = JSON.parse(data);
                        if (obj.code == 200) {
                            window.location.replace("<?=base_url() . 'perusahaan/user';?>");
                        }
                    });
                }
            });
        });
    });
</script>
