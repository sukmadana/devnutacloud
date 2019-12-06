<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 08/12/2015
 * Time: 14:59
 */
?>

<script type="text/javascript">

        function swichOnOffChanged(inputcheckbox) {
            var $ = jQuery();
            var ischecked = inputcheckbox.checked;
            var username = inputcheckbox.getAttribute('data-tag');
            jQuery.post("<?=base_url().'ajax/changeuserisaktif';?>", {"username": username, "isaktif": ischecked});
        }

</script>