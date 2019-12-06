<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 08/12/2015
 * Time: 14:59
 */
?>

<script type="text/javascript">

    function switchUserDailyReportOnOffChanged(inputcheckbox) {
        var $ = jQuery();
        var ischecked = inputcheckbox.checked;
        var username = '<?=$selectedusername;?>';
        jQuery.post("<?=base_url().'ajax/updateuserdailyreport';?>", {
            "username": username,
            "isaktif": ischecked
        });
    }

</script>