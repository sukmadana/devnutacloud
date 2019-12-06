<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 08/12/2015
 * Time: 14:59
 */
?>

<script type="text/javascript">

    function switchUserCabangOnOffChanged(inputcheckbox) {
        var $ = jQuery();
        var ischecked = inputcheckbox.checked;
        var id = inputcheckbox.getAttribute('data-tag');
        var username = '<?=$selectedusername;?>';
        jQuery.post("<?=base_url().'ajax/updateusercabang';?>", {
            "username": username,
            "id": id,
            "isaktif": ischecked
        });
    }

</script>