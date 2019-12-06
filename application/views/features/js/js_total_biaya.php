<?php
/**
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">
        function js_total_biaya() {
            var datestart = $('input[name=date_start]').val();
            var dateend = $('input[name=date_end]').val();
            var outlet = $('#outlet').val();
            $.post('<?=base_url() . 'ajaxdsb/biaya_hari_ini';?>', {
                ds: datestart,
                de: dateend,
                o: outlet
            }, function (data) {
                var ret = JSON.parse(data);
                $('#caption-biaya-hari-ini').html(ret.captionhead);
                $('#total-biaya-hari-ini').html(ret.totalhariini);
                $('#caption-footer-biaya-hari-ini').html(ret.captionfooter);
                $('#loadmask-total-biaya-hari-ini').hide();
                if (!ret.ishariini) {
                    $('#footer-biaya-hari-ini').hide();
                }

            });
        }
</script>
