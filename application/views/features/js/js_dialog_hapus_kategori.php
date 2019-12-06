<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 17:38
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var hapusKategoriModal;
        var selectedKategoriToBeDeleted;
        $('#hapus-kategori-modal').on('show.bs.modal', function (event) {
            hapusKategoriModal = $(this);
            selectedKategoriToBeDeleted = $('#list-kategori').find(":selected");
            $.post('<?=base_url('ajax/iskategoriinmultioutlet');?>', {
                    kategoriname: selectedKategoriToBeDeleted.val()
                },
                function (data) {
                    var jsonData = JSON.parse(data);
                    $('#loading-hapus-satuan-container').hide();
                    $('#hapus-satuan-container').show();
                    var AllOutletsKategori = $('.all-item-kategori');
                    var distinctOutletKategori = []

                    for (var x = 0; x < jsonData.msg.length; x++) {
                        var idoutletserverkategori = jsonData.msg[x];
                        for (var a = 0; a < AllOutletsKategori.length; a++) {
                            var outlet = AllOutletsKategori[a];
                            var tag = $(outlet).data('tag').split('#@#');
                            var idoutlet = tag[0];
                            if (idoutlet == idoutletserverkategori) {
                                var isIndistinctOutletKategoriList = false;
                                for (var b = 0; b < distinctOutletKategori.length; b++) {
                                    if (distinctOutletKategori[b] == idoutlet) {
                                        isIndistinctOutletKategoriList = true;
                                        break;
                                    }
                                }
                                if (!isIndistinctOutletKategoriList) {
                                    distinctOutletKategori.push(idoutlet)
                                }
                            }
                        }
                    }

                    for (var d = 0; d < AllOutletsKategori.length; d++) {
                        $(AllOutletsKategori[d]).parent().parent().hide();
                    }
                    for (var e = 0; e < distinctOutletKategori.length; e++) {

                        $('input[data-tag*="' + distinctOutletKategori[e] + '"').parent().parent().show();
                    }


                    if (jsonData.msg.length > 1) {
                        $('#label-kategori-akan-dihapus').html('Kategori "' + selectedKategoriToBeDeleted.val() + '" ada di beberapa outlet, anda bisa menghapusnya secara'
                            + ' bersamaan. Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    } else {
                        $('#label-kategori-akan-dihapus').html('Kategori "' + selectedKategoriToBeDeleted.val() + '" hanya ada di outlet dibawah ini'
                            + ' Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    }
                })
        });
        $('#btn-hapus-kategori-modal').click(function (e) {
            var selectedOutlets = $('.all-item-kategori:checked');
            var idoutlets = {};
            var idKategori = $(selectedKategoriToBeDeleted).val();
            var pOutlets=[];

            for (var a = 0; a < selectedOutlets.length; a++) {
                var outlet = selectedOutlets[a];
                var tag = $(outlet).data('tag').split('#@#');
                var idoutlet = tag[0];
                var nama = tag[1];
                idoutlets[a] = idoutlet;
                pOutlets.push(idoutlet);
            }
            /**/
            $.post('<?=base_url('ajax/deletekategori');?>', {
                idoutlet: idoutlets,
                namakategori: idKategori,
            }, function (data) {
                sendToSocket(NEvent.TRANSFER_CATEGORY, {source: 'Delete', outlets: pOutlets});
                selectedKategoriToBeDeleted.remove();
                $("#list-kategori option").filter(function () {
                    //may want to use $.trim in here
                    return $(this).text() == idKategori;
                }).remove();
                $("#list-kategori option").filter(function () {
                    //may want to use $.trim in here
                    return $(this).text() == ' ';
                }).prop('selected', true);
                $('#list-kategori').trigger("change");
                hapusKategoriModal.modal('hide');
            });


        });
        $('#all-selected-kategori').change(function () {
            var selected = $(this).prop('checked');

            $('.all-item-kategori').filter(function () {
                return $(this).prop('onclick') == null;
            }).prop('checked', selected);


        });
    });
</script>
