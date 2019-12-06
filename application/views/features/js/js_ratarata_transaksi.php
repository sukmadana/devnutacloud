<?php
/*
 * This file created by Em Husnan
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">


        function js_ratarata_transaksi() {
            var datestart = $('input[name=date_start]').val();
            var dateend = $('input[name=date_end]').val();
            var outlet = $('#outlet').val();
            $.post('<?=base_url() . 'ajaxdsb/ratarata_transaksi_hari_ini';?>', {
                ds: datestart,
                de: dateend,
                o: outlet
            }, function (data) {
                var ret = JSON.parse(data);
                $('#caption-ratarata-transaksi-hari-ini').html(ret.captionhead);
                $('#ratarata-transaksi-hari-ini').html(ret.totalhariini);
                $('#caption-footer-ratarata-transaksi-hari-ini').html(ret.captionfooter);
                $('#loadmask-ratarata-transaksi-hari-ini').hide();
                if (!ret.ishariini) {
                    $('#footer-ratarata-transaksi-hari-ini').hide();
                }

            });
        }

</script>
