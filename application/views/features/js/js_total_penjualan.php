<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">
    var $;
    jQuery(document).ready(function (x) {
        $ = x;
    });
    function js_total_penjualan() {
        var datestart = $('input[name=date_start]').val();
        var dateend = $('input[name=date_end]').val();
        var outlet = $('#outlet').val();
        $.post('<?=base_url() . 'ajaxdsb/penjualan_hari_ini';?>', {
            ds: datestart,
            de: dateend,
            o: outlet
        }, function (data) {

            var ret = JSON.parse(data);
            $('#caption-penjualan-hari-ini').html(ret.captionhead);
            $('#total-penjualan-hari-ini').html(ret.totalhariini);
            $('#caption-footer-penjualan-hari-ini').html(ret.captionfooter);
            $('#loadmask-total-penjualan-hari-ini').hide();
            if (!ret.ishariini) {
                $('#footer-penjualan-hari-ini').hide();
            }

        });
    }
</script>
