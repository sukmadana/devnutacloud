<?php $oldversion=false;
if($option->CreatedVersionCode<98 && $option->EditedVersionCode<98)
    $oldversion=true;
?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        <?php if($oldversion) { ?>
        $('#kirimAktivasi').prop('disabled',true );
        $('#TidakBisaAktivasi').html("<br>Tidak Bisa Aktivasi dari nutacloud karena aplikasi nuta di tablet belum di-update. Silakan update nuta dari playstore.");
        <?php } else { ?>
        $('#kirimAktivasi').prop('disabled',false );
        $('#TidakBisaAktivasi').html("");
        <?php } ?>
        $('#kirimAktivasi').click(function () {
//            $.get('<?//=base_url('activation/pay1');?>//', function (data) {
//                snap.pay(data, {language: 'id'});
//            });


            <?php if(!$has_new_aktivasi){?>
            var _devid = $('#outlet').val();
            var _nama = $('#outlet  option:selected').text().trim();
            var _alamat = $('#alamat').val();
            var _email = $('#email').val();
            var _masaaktif = $('#masaaktif').val();
            var _fitur = $('#fitur').val();
            var _voucher = $('#voucher').val();
            var data = {
                nama: _nama,
                outletid: _devid,
                alamat: _alamat,
                email: _email,
                masaaktif: _masaaktif,
                fitur: _fitur,
                voucher: _voucher
            };
            $.post('<?=base_url('activation/request');?>', data, function (json) {
                if (json.status === "OK") {
                    $('#hiddenkodeaktivasi').val(json.kodeaktivasi);
                    $('#hiddentoken').val(json.token);
                    $('#hiddentotal').val(json.total);
                    $('#totalharga').html(json.totalharga);
                    $('#kodeaktivasi').html(json.kodeaktivasi);
                    $('#myWizard').easyWizard('nextStep');
                } else {
                    alert(json.message);
                }
            }, "json")
            <?php }else{ ?>
            $('#myWizard').easyWizard('nextStep');
            <?php } ?>


        });
        $('#pembayaran').click(function () {
            snap.pay($('#hiddentoken').val(), {language: 'id'});
        });
        function payment_redirect() {
            var outlet = $('#outlet').val();
            var url = '<?=base_url('activation/index?outlet=');?>' + outlet;
            var masa = $('#masaaktif').val();
            if (masa != undefined) {
                var amount = masa.replace(" Bulan demo", "").replace(" Bulan", "");
                url += '&amount=' + amount;
            }
            var fitur = $('#fitur').val();
            if (fitur != undefined) {
                url += '&fitur=' + fitur;
            }
            window.location = url;
        }

        $('#outlet').change(function () {
            payment_redirect();
        });
        $('#masaaktif').change(function () {
            payment_redirect();
        });
        $('#fitur').change(function () {
            payment_redirect();
        });
        $('#kembaliKeKirimAktivasi').click(function () {
            $('#myWizard').easyWizard('prevStep');
        });
        $('#myWizard').easyWizard({
            showButtons: false,
            submitButton: false
        });
        <?php
        if($existing_aktivasi['status'] === 'Payment Successful'){ ?>
        $('#myWizard').easyWizard('goToStep', 3);
        <?php } ?>
    });
</script>
