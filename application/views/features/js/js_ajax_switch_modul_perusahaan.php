<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/12/2015
 * Time: 20:39
 */ ?>


<script type="text/javascript">

    function switchModulPerusahaanOnOffChanged(swpeofselect) {
        var $ = jQuery();
        var swpeofischecked = swpeofselect.checked;
        var swpeofkol = swpeofselect.getAttribute('data-tag');
        jQuery.post("<?=base_url() . 'ajax/updatemodulperusahaan';?>", {
            "kol": swpeofkol,
            "isaktif": swpeofischecked
        },function(data){
            location.reload();
        });

    }

</script>
