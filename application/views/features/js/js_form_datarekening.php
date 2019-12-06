<?php
/**
 * Created by PhpStorm.
 * User: ANDROMEDA
 * Date: 14/06/2017
 * Time: 14.55
 */
?>
<!--suppress ALL -->
<script type="text/javascript">
    var $;
    jQuery(document).ready(function ($) {
        $ = $;

        $('#btn-simpan').click(function (e) {
            $(this).addClass('active');
            $(this).prop('disabled', true);
            var bankName = $('#txt_bankName').val();
            if (bankName.trim() == '') {
                alert('"Bank" tidak boleh kosong');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return;
            }
            var accountNumber = $('#txt_accountNumber').val();
            if (accountNumber.trim() == '') {
                alert('"No.Rekening" tidak boleh kosong');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return;
            }

            var passwordibank = $('#passwordibank').val();
            var usernameibank = $('#usernameibank').val();
            var passwordibankasli = $('#passwordibankasli').val();
            var accountName = $('#txt_accountName').val();
            if (accountName.trim() == '') {
                alert('"Atas Nama" tidak boleh kosong');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return;
            }

            $.post('<?=base_url('ajax/savedatarekening');?>', {
                bankName: bankName, accountNumber: accountNumber, accountName: accountName,
                mode: '<?=$modeform;?>',
				accountID: '<?=$accountID;?>',
                devno: '<?=$devno;?>',
                oldaccountID: '<?=$form['accountID']; ?>',
                idoutlet: <?=$selected_outlet;?>,
                usernameibank: usernameibank,
                passwordibank: passwordibank,
                passwordibankasli: passwordibankasli
                }, function (data) {
                    var jsonData = JSON.parse(data);
                    
                        if (jsonData.status == "OK") {
                            alert('Berhasil Disimpan');
                            $('#btn-simpan').removeClass('active');
                            window.location = '<?=base_url('datarekening/index?outlet='.$selected_outlet);?>';
                        } else {
							$('#btn-simpan').removeClass('active');
                            $('#btn-simpan').prop('disabled', false);
                            alert('Gagal Disimpan');
							
                            
                        }
                    
                }
            );
        });
    });
</script>
