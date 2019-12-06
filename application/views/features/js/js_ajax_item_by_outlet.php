<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/12/2015
 * Time: 20:39
 */ ?>


<script type="text/javascript">

    <?php
$a='[';
foreach($outlets as $key =>$val){
$a.='{
val:"'.$val.'",
key:"'.$key.'"
},';

    }
    $a.=']';
    ?>
    var b =<?=$a;?>;
    function getKey(val) {
        for (var a = 0; a < b.length; a++) {
            if (b[a].val === val) {
                return b[a].key;
            }
        }
    }
    function getItemByOutlet(select) {
        var val = jQuery(select).val();


        jQuery.post("<?=base_url().'ajax/getitembyoutlet';?>", {
            "o": val,
            "v": jQuery('#varian').val()
        }, function (data) {
            jQuery('#item').empty();
            var obj = JSON.parse(data);
            for (var x = 0; x < obj.length; x++) {
                jQuery('#item').append('<option value="' + obj[x].id + '">' + obj[x].name + "</option>");
            }
            jQuery('#item').animate({marginLeft: '10px'}, 88).animate({marginLeft:'0px'}, 88).animate({marginLeft:'10px'},88).animate({marginLeft:'0px'},88);

        });
    }

</script>
