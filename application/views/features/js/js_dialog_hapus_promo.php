<script type="text/javascript">
    jQuery(document).ready(function($) {

        $('#hapus-promo-modal').on('show.bs.modal', function (event) {
            hapusItemModal = $(this);
            $('#loading-hapus-promo-container').show();
            $('#hapus-promo-container').hide();
            var button = $(event.relatedTarget); // Button that triggered the modal
            selectedIDPromoToBeDeleted = button.data('id'); // Extract info from data-* attributes
            $.post('<?=base_url('ajax/ispromoinmultioutlet');?>', {
                    promotitle: selectedIDPromoToBeDeleted
                },
                function (data) {
                    var jsonData = JSON.parse(data);
                    $('#loading-hapus-promo-container').hide();
                    $('#hapus-promo-container').show();
                    var AllOutlets = $('.all-promo-item');
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
                        $('#label-satuan-akan-dihapus').html('promo "' + selectedIDPromoToBeDeleted + '" ada di beberapa outlet, anda bisa menghapusnya secara'
                            + ' bersamaan. Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    } else {
                        $('#label-satuan-akan-dihapus').html('promo "' + selectedIDPromoToBeDeleted + '" hanya ada di outlet dibawah ini'
                            + ' Silahkan beri '
                            + 'tanda centang pada outlet yang ingin dihapus.');
                    }
                }
            );
        });

        $('#btn-hapus-promo-modal').click(function (e) {
            var selectedItemOutlets = $('.all-promo-item:checked');
            var idItemOutlets = {};

            for (var a = 0; a < selectedItemOutlets.length; a++) {
                var outlet = selectedItemOutlets[a];
                var tag = $(outlet).data('tag').split('#@#');
                var idoutlet = tag[0];
                idItemOutlets[a] = idoutlet;
            }
            $.post('<?=base_url('ajax/deletemasterpromo');?>', {
                idoutlet: idItemOutlets,
                idpromo: selectedIDPromoToBeDeleted,
            }, function (data) {
                var data=JSON.parse(data);
                if (isNaN(data.msg)) {
                    alert(data.msg);

                } else {
                    //TODO: Reload Grid
                    alert('Berhasil dihapus');
                    window.location = '<?=base_url('promo/listpromo?outlet=' . $selected_outlet);?>';
                }
            });

        });

        $('#all-selected-promo').change(function () {
            var selected = $(this).prop('checked');
            $('.all-promo-item').prop('checked', selected);
        });
    });
</script>
