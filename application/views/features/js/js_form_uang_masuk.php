<?php
/**
 * Created by PhpStorm.
 * User: ANDROMEDA
 * Date: 30/04/17
 * Time: 11:05
 */
?>
<script type="text/javascript">
    var $;
    jQuery(document).ready(function ($) {
        $ = $;

        $('#btn-simpan').click(function (e) {
            $(this).addClass('active');
            $(this).prop('disabled', true);
            var dari = $('#txt-dari').val();
            var jumlah = $('#txt-jumlah').val();
            if (dari.trim() == '') {
                alert('"Dari" tidak boleh kosong');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return;
            }
            if (jumlah.trim() == '') {
                alert('"Jumlah" tidak boleh kosong');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return;
            }
            var account = $('#list-account :selected').data('tag');
            var keterangan = $('#txt-keterangan').val();
            var jenis = $('input[name=jenis]:checked').val();

            $.post('<?=base_url('ajax/saveuangmasuk');?>', {
                    masukKe: account, dari: dari, jumlah: jumlah, keterangan: keterangan, jenis: jenis,
                    mode: '<?=$modeform;?>',
                    oldcash: '<?=$form['dari']; ?>',
                    idoutlet: <?=$selected_outlet;?>
                }, function (data) {
                    var jsonData = JSON.parse(data);
                    if (!isNaN(parseInt(jsonData.msg))) {
                        if (data.status == "OK") {
                            $('#btn-simpan').removeClass('active');
                            $('#btn-simpan').prop('disabled', false);
                            alert('Gagal Disimpan');
                        } else {
                            var pOutlets = [];
                            pOutlets.push(<?=$selected_outlet;?>);
                            sendToSocket(NEvent.TRANSFER_CASHIN, {source: 'Save', outlets: pOutlets});
                            alert('Berhasil Disimpan');


                            $('#btn-simpan').removeClass('active');
                            window.location = '<?=base_url('uangmasuk/index?outlet=' . $selected_outlet);?>';
                        }
                    }
                }
            );
        });
    });
</script>