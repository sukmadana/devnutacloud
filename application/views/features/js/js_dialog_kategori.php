<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 17/11/16
 * Time: 20:46
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var kategoriModal;
        var Mode;
        var EditKategoriOldValue;
        $('#kategori-modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            Mode = button.data('mode');
            $('#kategori-modal-title').html(Mode + ' Kategori');
            if (Mode == 'Edit') {
                EditKategoriOldValue = $('#list-kategori').val();
                $('#txt-kategori').val(EditKategoriOldValue);
            } else {
                $('#txt-kategori').val('');
            }
            kategoriModal = $(this);
            kategoriModal.modal('hide')
        })
        $('#btn-simpan-kategori').click(function (e) {
            var NewKategori = $.trim($('#txt-kategori').val());
            if (NewKategori == '') {
                alert('Nama Kategori tidak boleh kosong');
                return;
            } else {
                $(this).addClass('active');
                $(this).prop('disabled', true);
                if (Mode == 'Tambah') {
                    $.post("<?=base_url('ajax/createnewkategori');?>", {
                        namakategori: NewKategori,
                        idoutlet:<?=$selected_outlet;?>
                    }, function (data) {
                        var jsonData = JSON.parse(data);
                        if (isNaN(jsonData.msg)) {
                            alert(jsonData.msg);
                        } else {
                            sendToSocket(NEvent.TRANSFER_CATEGORY, {source: 'Save', outlets: [<?=$selected_outlet;?>]});
                            $('#list-kategori').append('<option data-tag="' + NewKategori + '" value="' + NewKategori + '">' + NewKategori + '</option>');
                            $('#list-kategori').val(NewKategori);
                            $('#list-kategori').trigger("change");
                            kategoriModal.modal('hide');
                        }
                        $('#btn-simpan-kategori').removeClass('active');
                        $('#btn-simpan-kategori').prop('disabled', false);
                    });

                } else if (Mode == 'Edit') {
                    var option = $('#list-kategori option[value=\'' + EditKategoriOldValue + '\']');
                    $.post("<?=base_url('ajax/editkategori');?>", {
                        oldnamakategori: EditKategoriOldValue,
                        newnamakategori: NewKategori,
                        idoutlet:<?=$selected_outlet;?>
                    }, function (data) {
                        var jsonData = JSON.parse(data);
                        if (isNaN(jsonData.msg)) {
                            alert(jsonData.msg);
                        } else {
//                            $('#list-kategori :selected').attr('data-tag',EditKategoriOldValue)
//                            $('#list-kategori :selected').html('data-tag',NewKategori)
//                            $('#list-kategori').val(NewKategori);
                            sendToSocket(NEvent.TRANSFER_CATEGORY, {source: 'Save', outlets: [<?=$selected_outlet;?>]});
                            option.attr('value', NewKategori);
                            option.html(NewKategori);
                            $('#list-kategori').trigger("change");
                            kategoriModal.modal('hide');
                        }
                        $('#btn-simpan-kategori').removeClass('active');
                        $('#btn-simpan-kategori').prop('disabled', false);
                    });
                }
            }
        });
        $('#form-kategori').keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        $('#form-kategori').keyup(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
