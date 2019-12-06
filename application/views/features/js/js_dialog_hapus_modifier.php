<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 18:08
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var hapusItemModal;
        var selectedIDItemToBeDeleted;
        $('#hapus-item-modal').on('show.bs.modal', function (event) {
            hapusItemModal = $(this);
            $('#loading-hapus-item-container').show();
            $('#hapus-item-container').hide();
            var button = $(event.relatedTarget); // Button that triggered the modal
            selectedIDItemToBeDeleted = button.data('id'); // Extract info from data-* attributes
            $.post('<?=base_url('ajax/ismodifierinmultioutlet');?>', {
                    itemname: selectedIDItemToBeDeleted
                },
                function (data) {
                    var jsonData = JSON.parse(data);
                    $('#loading-hapus-item-container').hide();
                    $('#hapus-item-container').show();
                    var AllOutlets = $('.all-item-item');
                    var distinct = []

                    for (var x = 0; x < jsonData.msg.length; x++) {
                        var idoutletserver = jsonData.msg[x];
                        for (var a = 0; a < AllOutlets.length; a++) {
                            var outlet = AllOutlets[a];
                            var tag = $(outlet).data('tag').split('#@#');
                            var idoutlet = tag[0];
                            if (idoutlet == idoutletserver) {
                                var isInDistinctList = false;
                                for (var b = 0; b < distinct.length; b++) {
                                    if (distinct[b] == idoutlet) {
                                        isInDistinctList = true;
                                        break;
                                    }
                                }
                                if (!isInDistinctList) {
                                    distinct.push(idoutlet)
                                }
                            }
//                            else {
//                                $(outlet).parent().parent().hide();
//                            }
                        }
                    }

                    for (var d = 0; d < AllOutlets.length; d++) {
                        $(AllOutlets[d]).parent().parent().hide();
                    }
                    for (var e = 0; e < distinct.length; e++) {

                        $('input[data-tag*="' + distinct[e] + '"').parent().parent().show();
                    }


                    if (jsonData.msg.length > 1) {
                        $('#label-satuan-akan-dihapus').html('Item "' + selectedIDItemToBeDeleted + '" ada di beberapa outlet, anda bisa menghapusnya secara'
                            + ' bersamaan. Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    } else {
                        $('#label-satuan-akan-dihapus').html('Item "' + selectedIDItemToBeDeleted + '" hanya ada di outlet dibawah ini'
                            + ' Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    }
                }
            );
        });
        $('#btn-hapus-item-modal').click(function (e) {
            var selectedItemOutlets = $('.all-item-item:checked');
            var idItemOutlets = {};
            var idOutlets = [];

            for (var a = 0; a < selectedItemOutlets.length; a++) {
                var outlet = selectedItemOutlets[a];
                var tag = $(outlet).data('tag').split('#@#');
                var idoutlet = tag[0];
                idItemOutlets[a] = idoutlet;
                idOutlets.push(idoutlet);
            }
            $.post('<?=base_url('ajax/deletemastermodifier');?>', {
                idoutlet: idItemOutlets,
                iditem: selectedIDItemToBeDeleted,
            }, function (data) {
                var data = JSON.parse(data);
                if (isNaN(data.msg)) {
                    alert(data.msg);

                } else {
                    //TODO: Reload Grid
                    sendToSocket(NEvent.TRANSFER_ITEM, {source: 'Delete', outlets: idOutlets});
                    alert('Berhasil dihapus');
                    window.location = '<?=base_url('item/index?outlet=' . $selected_outlet);?>';
                }
            });

        });
        $('#all-selected-item').change(function () {
            var selected = $(this).prop('checked');

            $('.all-item-item').filter(function () {
                return $(this).prop('onclick') == null && $(this).is(':visible');
            }).prop('checked', selected);


        });
    });
</script>
