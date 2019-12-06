<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/12/2015
 * Time: 20:39
 */ ?>


<script type="text/javascript">

    function getOutletByVarian(select) {
        var val = jQuery(select).val();
        var all = jQuery(select).attr('data-tag');

        jQuery.post("<?=base_url().'ajax/getoutletbyvarian';?>", {
            "o": val,
            "a": all
        }, function (data) {
            jQuery('#outlet').empty();
            var obj = JSON.parse(data);
            for (var x = 0; x < obj.length; x++) {
                jQuery('#outlet').append('<option value="' + obj[x].id + '">' + obj[x].name + "</option>");
            }

            if (jQuery('#item').length == 1) {
                getItemByOutlet(jQuery('#outlet'));
            }
            jQuery('#outlet').animate({marginLeft: '10px'}, 88).animate({marginLeft:'0px'}, 88).animate({marginLeft:'10px'},88).animate({marginLeft:'0px'},88);

        });
    }

</script>
