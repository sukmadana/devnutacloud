<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 20:24
 */
?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var hapusSatuanModal;
        var selectedSatuanToBeDeleted;
        $('#hapus-satuan-modal').on('show.bs.modal', function (event) {
            $('#hapus-item-container').hide();
            $('#loading-hapus-item-container').show();

            hapusSatuanModal = $(this);
            selectedSatuanToBeDeleted = $('#list-satuan :selected');
            $.post('<?=base_url('ajax/issatuaninmultioutlet');?>', {
                    satuanname: selectedSatuanToBeDeleted.val()
                },
                function (data) {
                    var jsonData = JSON.parse(data);
                    $('#loading-hapus-satuan-container').hide();
                    $('#hapus-satuan-container').show();
                    var AllOutletsSatuan = $('.all-item-satuan');
                    var distinctOutletSatuan = []

                    for (var x = 0; x < jsonData.msg.length; x++) {
                        var idoutletserversatuan = jsonData.msg[x];
                        for (var a = 0; a < AllOutletsSatuan.length; a++) {
                            var outlet = AllOutletsSatuan[a];
                            var tag = $(outlet).data('tag').split('#@#');
                            var idoutlet = tag[0];
                            if (idoutlet == idoutletserversatuan) {
                                var isIndistinctOutletSatuanList = false;
                                for (var b = 0; b < distinctOutletSatuan.length; b++) {
                                    if (distinctOutletSatuan[b] == idoutlet) {
                                        isIndistinctOutletSatuanList = true;
                                        break;
                                    }
                                }
                                if (!isIndistinctOutletSatuanList) {
                                    distinctOutletSatuan.push(idoutlet)
                                }
                            }
                        }
                    }

                    for (var d = 0; d < AllOutletsSatuan.length; d++) {
                        $(AllOutletsSatuan[d]).parent().parent().hide();
                    }
                    for (var e = 0; e < distinctOutletSatuan.length; e++) {

                        $('input[data-tag*="' + distinctOutletSatuan[e] + '"').parent().parent().show();
                    }


                    if (jsonData.msg.length > 1) {
                        $('#label-satuan-akan-dihapus').html('Satuan "' + selectedSatuanToBeDeleted.val() + '" ada di beberapa outlet, anda bisa menghapusnya secara'
                            + ' bersamaan. Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    } else {
                        $('#label-satuan-akan-dihapus').html('Satuan "' + selectedSatuanToBeDeleted.val() + '" hanya ada di outlet dibawah ini'
                            + ' Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    }
                }

            )
            ;
        });
        $('#btn-hapus-satuan-modal').click(function (e) {
            var selectedSatuanOutlets = $('.all-item-satuan:checked');
            var idSatuanOutlets = {};
            var idSatuan = $(selectedSatuanToBeDeleted).val();
            var pOutlets=[];

            for (var a = 0; a < selectedSatuanOutlets.length; a++) {
                var outlet = selectedSatuanOutlets[a];
                var tag = $(outlet).data('tag').split('#@#');
                var idoutlet = tag[0];
                idSatuanOutlets[a] = idoutlet;
                pOutlets.push(idoutlet);
            }
            $.post('<?=base_url('ajax/deletesatuan');?>', {
                idoutlet: idSatuanOutlets,
                idsatuan: idSatuan,
            }, function (data) {
                var jsondata = JSON.parse(data);
                var msg = [];
                if (jsondata.msg.trim() != "") {
                    msg = jsondata.msg.split(';');
                }
                if (msg.length == 0) {
                    sendToSocket(NEvent.TRANSFER_UNIT, {source: 'Delete', outlets: pOutlets});

                    selectedSatuanToBeDeleted.remove();
                    $("#list-satuan option").filter(function () {
                        //may want to use $.trim in here
                        return $(this).text() == ' ';
                    }).prop('selected', true);
                    $('#list-satuan').trigger("change");
                    hapusSatuanModal.modal('hide');
                } else {
                    var alertmsg = 'Tidak bisa menghapus di ';
                    for (var p = 0; p < msg.length; p++) {
                        var dtlmsg = msg[p];
                        if (dtlmsg.trim() != "") {
                            var potlts = $('#outlet option[value="' + dtlmsg + '"]').html();
                            alertmsg += potlts;
                        }
                    }
                    alertmsg += "  karena satuan terakhir";
                    alert(alertmsg);
                }
            });

        });
        $('#all-selected-satuan').change(function () {
            var selected = $(this).prop('checked');

            $('.all-item-satuan').filter(function () {
                return $(this).prop('onclick') == null;
            }).prop('checked', selected);


        });
    });
</script>
