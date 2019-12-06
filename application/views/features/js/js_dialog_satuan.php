<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 17:36
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var satuanModal;
        var Mode;
        var EditsatuanOldValue;
        $('#satuan-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            Mode = button.data('mode'); // Extract info from data-* attributes
            $('#satuan-modal-title').html(Mode + ' satuan')
            if (Mode == 'Edit') {
                EditsatuanOldValue = $('#list-satuan').val();
                $('#txt-satuan').val(EditsatuanOldValue);
            } else {
                $('#txt-satuan').val('');
            }
            satuanModal = $(this);
            satuanModal.modal('hide')
        })
        $('#btn-simpan-satuan').click(function (e) {
            var Newsatuan = $.trim($('#txt-satuan').val());
            if (Newsatuan == '') {
                alert('Satuan tidak boleh kosong');
            } else {
                $(this).addClass('active');
                $(this).prop('disabled', true);
                if (Mode == 'Tambah') {


                    $.post("<?=base_url('ajax/createnewsatuan');?>", {
                        namasatuan: Newsatuan,
                        idoutlet:<?=$selected_outlet;?>
                    }, function (data) {
                        var jsonData = JSON.parse(data);
                        if (isNaN(jsonData.msg)) {
                            alert(jsonData.msg);
                        } else {
                            sendToSocket(NEvent.TRANSFER_UNIT, {source: 'Save', outlets: [<?=$selected_outlet;?>]});
                            $('#list-satuan').append('<option value="' + Newsatuan + '" data-tag="' + Newsatuan + '">' + Newsatuan + '</option>');
                            $('#list-satuan').val(Newsatuan);
                            $('#list-satuan').trigger("change");
                            //refresh satuan di bahan
                            jsonSatuan.push({name: Newsatuan, tag: jsonData.msg});
                            $('.itembahan select').append('<option value="' + Newsatuan + '" data-tag="' + Newsatuan + '">' + Newsatuan + '</option>');
                        }
                        $('#btn-simpan-satuan').removeClass('active');
                        $('#btn-simpan-satuan').prop('disabled', false);
                        satuanModal.modal('hide')
                    });

                } else if (Mode == 'Edit') {
                    var option = $('#list-satuan option[value="' + EditsatuanOldValue + '"]');
                    $.post("<?=base_url('ajax/editsatuan');?>", {
                        oldnamasatuan: EditsatuanOldValue,
                        newnamasatuan: Newsatuan,
                        id: option.data('tag'),
                        idoutlet:<?=$selected_outlet;?>
                    }, function (data) {
                        var jsonData = JSON.parse(data);
                        if (isNaN(jsonData.msg)) {
                            alert(jsonData.msg);
                        } else {
                            sendToSocket(NEvent.TRANSFER_UNIT, {source: 'Save', outlets: [<?=$selected_outlet;?>]});
                            option.attr('value', Newsatuan);
                            option.html(Newsatuan);
                            $('.itembahan select option').remove();
                            for (var a = 0; a < jsonSatuan.length; a++) {
                                if (jsonSatuan[a].name == EditsatuanOldValue) {
                                    jsonSatuan[a].name = Newsatuan;
                                }
                                $('.itembahan select').append('<option value="' + jsonSatuan[a].name + '" data-tag="' + jsonSatuan[a].name + '">' + jsonSatuan[a].name + '</option>');
                            }

                            $('#list-satuan').trigger("change");
                            $('#btn-simpan-satuan').removeClass('active');
                            $('#btn-simpan-satuan').prop('disabled', false);
                            satuanModal.modal('hide');
                        }
                    });

                }
            }
        });

        $('#form-satuan').keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        $('#form-satuan').keyup(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
