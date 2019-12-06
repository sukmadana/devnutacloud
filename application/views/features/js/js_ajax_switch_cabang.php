<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 08/12/2015
 * Time: 14:59
 */
?>

<script type="text/javascript">

    function switchCabangOnOffChanged(inputcheckbox) {
        var $ = jQuery();
        var ischecked = inputcheckbox.checked;
        var id = inputcheckbox.getAttribute('data-tag');
        jQuery.post("<?=base_url().'ajax/updatecabang';?>", {
            "id": id,
            "isaktif": ischecked
        });
    }

</script>