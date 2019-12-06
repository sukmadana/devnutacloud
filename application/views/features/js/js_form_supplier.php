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
            var nama = $('#txt-nama').val();
            if (nama.trim() == '') {
                alert('"Nama" tidak boleh kosong');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return;
            }
            var alamat = $('#txt-alamat').val();
            var telepon = $('#txt-telepon').val();
            /*if (!isNaN(telepon)) {
                alert('Hanya boleh dengan angka');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return false;
            }*/
            var email = $('#txt-email').val();
//            var atpos = email.indexOf("@");
//            var dotpos = email.lastIndexOf(".");
//            if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
//                alert("Masukkan email dengan benar");
//                $('#btn-simpan').removeClass('active');
//                $('#btn-simpan').prop('disabled', false);
//                return false;
//            }
            var catatan = $('#txt-catatan').val();

            $.post('<?=base_url('ajax/savesupplier');?>', {
                nama: nama, alamat: alamat, telepon: telepon, email: email, catatan: catatan,
                mode: '<?=$modeform;?>',
				id_supp: '<?=$id_supp;?>',
                devno: '<?=$devno;?>',
                oldsupp: '<?=$form['nama']; ?>',
                idoutlet: <?=$selected_outlet;?>
                }, function (data) {
                    var jsonData = JSON.parse(data);
                    
                        if (jsonData.status == "OK") {
                            alert('Berhasil Disimpan');
                            $('#btn-simpan').removeClass('active');
                            window.location = '<?=base_url('supplier/index?outlet='.$selected_outlet);?>';
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
