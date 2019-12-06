<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 20:25
 */?>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var hapusKategoriModal;
        var selectedKategoriToBeDeleted;
        $('#hapus-kategori-modal').on('show.bs.modal', function (event) {
            hapusKategoriModal = $(this);
            selectedKategoriToBeDeleted = $('#list-kategori').find(":selected");
            $('#label-item-akan-dihapus').html('Kategori "' + selectedKategoriToBeDeleted.val() + '" ada di beberapa outlet, anda bisa menghapusnya secara'
                + 'bersamaan. Silahkan beri'
                + 'tanda centang pada outlet yang ingin dihapus.');
        });
        $('#btn-hapus-kategori-modal').click(function (e) {
            selectedKategoriToBeDeleted.remove();
            $("#list-kategori option").filter(function () {
                //may want to use $.trim in here
                return $(this).text() == ' ';
            }).prop('selected', true);
            $('#list-kategori').trigger("change");
            hapusKategoriModal.modal('hide');
        });
        $('#all-selected').change(function () {
            var selected = $(this).prop('checked');

            $('.all-item').prop('checked', selected);


        });
    });
</script>
