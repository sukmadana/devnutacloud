<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 31/05/17
 * Time: 14:29
 */ ?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        sendToSocket(NEvent.TRANSFER_CASHOUT, {source: '<?=$src;?>', outlets: [<?=$selected_outlet;?>]});
    });
</script>