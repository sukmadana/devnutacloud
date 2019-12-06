<?php
/**
 * Created by PhpStorm.
 * User: ANDROMEDA
 * Date: 14/06/2017
 * Time: 07.19
 */
?>
<script type="text/javascript">

    window.$ = jQuery;
    jQuery('.delete-button').bind('click', function(){
        var dialog = confirm($(this).data('message'));

        if (dialog == true) {
            jQuery($(this).data('target')).submit();
        }
    });

    function getDataOutlet() {
        var outlet = document.getElementById('outlet');
        return outlet.options[outlet.selectedIndex].value;
    }

    function validaten() {
        <?php if($visibilityMenu['SupplierAdd'])
        {   ?>
        var selected = getDataOutlet();
        if (!selected)
            return alert('Pilih outlet terlebih dahulu.');
        <?php if(($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98)) { ?>
        return document.getElementById('supplier-add').submit();
        <?php }
        else { ?>
        alert('Aplikasi Nuta di tablet Anda masih versi lama. Silakan update nuta dari playstore.');
        <?php } ?>
        <?php
        } else { ?>
        alert('Anda tidak memiliki hak akses untuk menambah Supplier.');
        <?php
        } ?>
    }

    function selectinge() {
        var selected = getDataOutlet();
        if (!selected)
            return false;

        return location.href = base_url+"supplier/index?outlet="+selected;
    }
</script>
