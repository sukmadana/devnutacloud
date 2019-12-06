<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">
        function js_total_laba() {
            var datestart = $('input[name=date_start]').val();
            var dateend = $('input[name=date_end]').val();
            var outlet = $('#outlet').val();
            $.post('<?=base_url() . 'ajaxdsb/laba_kotor_hari_ini';?>', {
                ds: datestart,
                de: dateend,
                o: outlet
            }, function (data) {
                var ret = JSON.parse(data);
                $('#caption-laba-kotor-hari-ini').html(ret.captionhead);
                $('#total-laba-kotor-hari-ini').html(ret.totalhariini);
                $('#caption-footer-laba-kotor-hari-ini').html(ret.captionfooter);
                $('#loadmask-laba-kotor-hari-ini').hide();
                if (!ret.ishariini) {
                    $('#footer-laba-kotor-hari-ini').hide();
                }

            });
        }

</script>
