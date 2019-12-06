<?php

/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 19:53
 */
?>
<script type="text/javascript" src="/js/bootstrap-select.min.js"></script>
<script type="text/javascript">
    var xx;
    jQuery(document).ready(function($) {
        xx = $;
        $('#outlet').change(function() {
            var val = $(this).val();

            window.location = '<?= base_url('category/index?outlet='); ?>' + val;

        });

        $('select').selectpicker({
            liveSearch: true
        });

        // Datatables

        var dataTable = $('#grid-table').DataTable({
            "bInfo": false,
            "paging": false,
            "responsive": false,
            "scrollX": true,
            "scrollY": "400px",
            "order": [],
            "columnDefs": [{
                "targets": [0, 2],
                "orderable": false
            }]
        });

        $('.dataTables_filter, .dataTables_length').hide();

        $('#searchBox').keyup(delay(function(e) {
            dataTable.search($(this).val()).draw();
        }, 500));

        // -- Child Rows
        $('#grid-table tbody').on('click', '.detail-items', function() {
            var $this = $(this);
            var tr = $(this).closest('tr');
            var row = dataTable.row(tr);

            if (row.child.isShown()) {
                tr.find('a.detail-items.icon').html('<i class="fa fa-chevron-right"></i>')
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                jQuery.ajax({
                    method: "GET",
                    url: "<?= base_url() ?>category/ajax_items_by_category?CategoryID=" + $this.data('category-id') + "&OutletID=" + <?= $selected_outlet ?> + "&DeviceNo=" + $this.data('device-no'),
                    dataType: "JSON",
                    beforeSend: function(xhr) {
                        tr.find('a.detail-items.icon').html('<i class="fa fa-spinner fa-spin"></i>')
                    },
                    success: function(result) {
                        tr.find('a.detail-items.icon').html('<i class="fa fa-chevron-down"></i>')

                        var html = '';
                        jQuery.each(result, function(i, v) {
                            html += '<table cellspacing="0" border="0" style="width:100%"><tr>';
                            html += '<td><span class="pl-50">' + v.ItemName + '</span></td>';
                            html += '</tr></table>';
                        });

                        row.child(html, 'no-padding').show();
                        tr.addClass('shown');
                    }
                });

            }
        });

        $('#modalAddKategori').on('hidden.bs.modal', function() {
            $('.alert-fixed-danger').hide();
            $('.alert-fixed-success').hide();
        });

        // Add Kategori
        $('.btnSimpan').on('click', function() {
            var $this = $(this);
            var jenisSimpan = $this.val();
            var CategoryName = $('#formAddCategory').find('input[name="CategoryName"]').val();
            jQuery.ajax({
                method: "POST",
                url: "<?= base_url() ?>category/ajax_create_category",
                data: {
                    outlet: "<?= $selected_outlet ?>",
                    CategoryName: $('#formAddCategory').find('input[name="CategoryName"]').val(),
                    IPPrinter: $('#formAddCategory').find('input[name="IPPrinter"]').val(),
                },
                dataType: 'JSON',
                beforeSend: function(xhr) {
                    $this.attr('disabled', true);
                    $this.html('<i class="fa fa-refresh fa-spin"></i> Menyimpan');
                },
                success: function(response) {
                    if (response.status == 200) {
                        if (jenisSimpan == 'simpan') {
                            $this.attr('disabled', false);
                            $this.html('Simpan');

                            $('.alert-fixed-danger').hide();
                            $('.alert-fixed-success').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('.alert-fixed-success').show();
                            setTimeout(function() {
                                window.location.replace("<?= base_url() . 'category/index?outlet=' . $selected_outlet; ?>");
                            }, 2000);

                        } else {
                            $this.attr('disabled', false);
                            $this.html('Simpan dan Tambahkan Produk');
                            $('#formAddCategory').find('input[name="CategoryName"]').val('');
                            $('#formAddCategory').find('input:radio').removeAttr('checked');
                            $('#modalAddKategori').modal('hide');

                            $('#modalMoveProduk').find('.CategoryName').text(CategoryName);
                            $('#modalMoveProduk').find('#newCategoryID').val(response.CategoryID);
                            $('#modalMoveProduk').find('#newCategoryDeviceNo').val(response.CloudDevNo);
                            $('#modalMoveProduk').modal('show');
                        }
                    } else {
                        if (jenisSimpan == 'simpan') {
                            $this.attr('disabled', false);
                            $this.html('Simpan');
                        } else {
                            $this.attr('disabled', false);
                            $this.html('Simpan dan Tambahkan Produk');
                        }
                        $('.alert-fixed-success').hide();
                        $('.alert-fixed-danger').find('.alert').html('<p>' + response.message + '</p>');
                        $('.alert-fixed-danger').show();
                    }

                }
            })
        });

        var selecteditems = 0;
        // Create produk datatable
        var tableProduk = $('#table-move-produk').DataTable({
            "ajax": '',
            "responsive": false,
            "scrollX": true,
            "columns": [{
                "data": null,
                "defaultContent": ''
            }, {
                "data": "itemName"
            }, {
                "data": "CategoryName",
                "className": "text-muted"
            }],
            "columnDefs": [{
                "targets": 0,
                "data": "ItemID",
                "render": function(data, type, row, meta) {
                    return '<input type="checkbox" class="mr-10 pull-right select-item" name="ItemsID[]" value="' + data.itemID + '"><input type="hidden" name="DeviceNo[]" value="' + data.DeviceNo + '">'
                }
            }, {
                "targets": [0, 2],
                "orderable": false,
                "searchable": false
            }],
            "language": {
                "loadingRecords": "Memuat data produk . . .",
                "processing": "Memuat data produk . . ."
            },
            "scrollY": "300px",
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "info": false,
        });

        $('.dataTables_filter, .dataTables_length').hide();

        // Event pencarian modal pindahkan produk
        $('#searchBoxItems').keyup(delay(function(e) {
            var value = $(this).val().toLowerCase();
            $("#table-move-produk tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }, 500));

        // Modal Pindahkan Produk
        $('#grid-table').on('click', 'tbody tr .btnMoveProduk', function() {
            var $this = $(this);
            var categoryID = $this.data('category-id');
            var categoryName = $this.data('category-name');
            var categoryDeviceNo = $this.data('device-no');

            $('#modalMoveProduk').find('.CategoryName').text(categoryName);
            $('#modalMoveProduk').find('#newCategoryID').val(categoryID);
            $('#modalMoveProduk').find('#newCategoryDeviceNo').val(categoryDeviceNo);
            $('#modalMoveProduk').modal('show');
        });

        // Modal Pindahkan Produk Show
        $('#modalMoveProduk').on('show.bs.modal', function() {
            // Show Table of Produk
            var categoryID = $('#modalMoveProduk').find('#newCategoryID').val();
            var categoryDeviceNo = $('#modalMoveProduk').find('#newCategoryDeviceNo').val();
            var ajax_url = "<?= base_url() ?>category/ajax_items_other_category?outlet=<?= $selected_outlet ?>&categoryID=" + categoryID + "&deviceNo=" + categoryDeviceNo;
            $('#countMoveProduk').text('0');
            // load ajax baru tiap modal pindahkan produk terbuka
            tableProduk.ajax.url(ajax_url).load();
            $('.dataTables_filter, .dataTables_length').hide();
        });

        // Event selected item
        $('#table-move-produk').on('change', 'tbody tr .select-item', function() {
            var $this = $(this);

            if ($this.is(':checked')) {
                selecteditems = selecteditems + 1;
            } else {
                selecteditems = selecteditems - 1;
            }

            $('#countMoveProduk').text(selecteditems);
        });

        // Pindahkan Produk
        $('#btnSimpanProduk').on('click', function() {
            var $this = $(this);

            var itemsID = $('#formMoveProduk').find('input[name="ItemsID[]"]').map(function() {
                if ($(this).is(":checked")) {
                    return this.value;
                }
            }).get();

            var ItemsDeviceNo = $('#formMoveProduk').find('input[name="DeviceNo[]"]').map(function() {
                if ($(this).closest('td').find('input[name="ItemsID[]"]').is(":checked")) {
                    return this.value;
                }
            }).get();

            $.ajax({
                method: "POST",
                url: "<?= base_url() . 'category/ajax_update_items_category'; ?>",
                data: {
                    outlet: "<?= $selected_outlet ?>",
                    categoryID: $('#formMoveProduk').find('input[name="newCategoryID"]').val(),
                    categoryDeviceNo: $('#formMoveProduk').find('input[name="newCategoryDeviceNo"]').val(),
                    'itemsID[]': itemsID,
                    'itemsDeviceNo[]': ItemsDeviceNo,
                },
                dataType: 'JSON',
                beforeSend: function(xhr) {
                    $this.attr('disabled', true);
                    $this.html('<i class="fa fa-refresh fa-spin"></i> Menyimpan');
                },
                success: function(response) {
                    $this.attr('disabled', false);
                    $this.html('Pindahkan Produk');
                    if (response.status == 200) {
                        $('#modalMoveProduk').find('.alert-produk.alert-fixed-danger').hide();
                        $('#modalMoveProduk').find('.alert-produk .alert-success').text(response.message);
                        $('#modalMoveProduk').find('.alert-produk.alert-fixed-success').show();

                        $('#formMoveProduk').find('input[name="newCategoryID"]').val('');
                        $('#formMoveProduk').find('.select-item').attr('checked', false);

                        setTimeout(function() {
                            window.location = '<?= base_url('category/index?outlet='); ?>' + <?= $selected_outlet ?>;
                        }, 2000);
                    } else {
                        $('#modalMoveProduk').find('.alert-produk.alert-fixed-success').hide();
                        $('#modalMoveProduk').find('.alert-produk .alert-danger').text(response.message);
                        $('#modalMoveProduk').find('.alert-produk.alert-fixed-danger').show();
                    }
                }
            })
        });


        // Modal Edit Nama Kategori
        $('#grid-table').on('click', 'tbody tr .btnUpdateKategori', function() {
            var $this = $(this);
            var categoryID = $this.data('category-id');
            var categoryName = $this.data('category-name');
            var categoryDeviceNo = $this.data('device-no');

            $.ajax({
                method: "GET",
                url: "<?= base_url() ?>category/ajax_get_detail_category?Outlet=<?= $selected_outlet ?>&CategoryID=" + categoryID + "&DeviceNo=" + categoryDeviceNo,
                dataType: "JSON",
                beforeSend: function(xhr) {
                    $this.html('<i class="fa fa-spin fa-spinner"></i> Memuat data ...');
                },
                success: function(response) {
                    $this.html('Edit Kategori');

                    $('#modalUpdateKategori').find('input[name="CategoryName"]').val(response.CategoryName);
                    $('#modalUpdateKategori').find('input[name="CategoryID"]').val(response.CategoryID);
                    $('#modalUpdateKategori').find('input[name="DeviceNo"]').val(response.DeviceNo);
                    $('#modalUpdateKategori').find('input[name="IPPrinter"][value="' + response.IPPrinter + '"]').attr("checked", "checked");
                    $('#modalUpdateKategori').modal('show');
                }
            })
        });

        $('#btnUpdateKategori').on('click', function() {
            var $this = $(this);
            jQuery.ajax({
                method: "POST",
                url: "<?= base_url() ?>category/ajax_update_category",
                data: {
                    outlet: "<?= $selected_outlet ?>",
                    CategoryID: $('#formUpdateKategori').find('input[name="CategoryID"]').val(),
                    DeviceNo: $('#formUpdateKategori').find('input[name="DeviceNo"]').val(),
                    CategoryName: $('#formUpdateKategori').find('input[name="CategoryName"]').val(),
                    IPPrinter: $('#formUpdateKategori').find('input[name="IPPrinter"]').val()
                },
                dataType: 'JSON',
                beforeSend: function(xhr) {
                    $this.attr('disabled', true);
                    $this.html('<i class="fa fa-refresh fa-spin"></i> Menyimpan');
                },
                success: function(response) {
                    $this.attr('disabled', false);
                    $this.html('Simpan');

                    if (response.status == 200) {
                        $('#alert-update-delete .alert-danger').hide();
                        $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                        $('#alert-update-delete, #alert-update-delete .alert-success').show();
                        setTimeout(function() {
                            window.location.replace("<?= base_url() . 'category/index?outlet=' . $selected_outlet; ?>");
                        }, 2000);
                    } else {
                        $('#alert-update-delete .alert-success').hide();
                        $('#alert-update-delete').find('.alert-danger').html('<p>' + response.message + '</p>');
                        $('#alert-update-delete, #alert-update-delete .alert-danger').show();
                        setTimeout(function() {
                            $('#alert-update-delete, #alert-update-delete .alert-danger').hide();
                        }, 2000);
                    }

                }
            })
        });

        // Modal Hapus Nama Kategori
        $('#grid-table').on('click', 'tbody tr .btnDeleteCategory', function() {
            var $this = $(this);
            var categoryID = $this.data('category-id');
            var categoryDeviceNo = $this.data('device-no');
            var categoryName = $this.data('category-name');

            $('#modalHapus').find('#delete-kategori').text(categoryName);
            $('#modalHapus').find('input[name="CategoryID"]').val(categoryID);
            $('#modalHapus').find('input[name="DeviceNo"]').val(categoryDeviceNo);
            $('#modalHapus').modal('show');
        });

        $('#btnDelete').on('click', function() {
            var $this = $(this);
            $.ajax({
                method: "POST",
                url: "<?= base_url() . 'category/ajax_delete_category'; ?>",
                data: {
                    CategoryID: $('#modalHapus').find('input[name="CategoryID"]').val(),
                    DeviceNo: $('#modalHapus').find('input[name="DeviceNo"]').val(),
                    outlet: "<?= $selected_outlet ?>"
                },
                dataType: 'JSON',
                beforeSend: function(xhr) {
                    $this.attr('disabled', true);
                    $this.html('<i class="fa fa-refresh fa-spin"></i> Menghapus');
                },
                success: function(response) {
                    $this.attr('disabled', false);
                    $this.html('Yakin');
                    if (response.status == 200) {
                        $('#modalHapus').modal('hide');
                        $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                        $('#alert-update-delete, #alert-update-delete .alert-success').show();
                        setTimeout(function() {
                            window.location.replace("<?= base_url() . 'category/index?outlet=' . $selected_outlet; ?>");
                        }, 2000);
                    } else {
                        $('#alert-update-delete .alert-success').hide();
                        $('#alert-update-delete').find('.alert-danger').html('<p>' + response.message + '</p>');
                        $('#alert-update-delete, #alert-update-delete .alert-danger').show();
                        setTimeout(function() {
                            $('#alert-update-delete, #alert-update-delete .alert-danger').hide();
                        }, 2000);
                    }
                }
            })
        });
    });

    function redirectTonewItem() {
        <?php if ($visibilityMenu['ItemAdd']) { ?>
            var selected_outlet = xx('#outlet').val();
            if (selected_outlet == -999) {
                alert('Pilih outlet terlebih dahulu.');
            } else {
                window.location = '<?= base_url('category/itemform?outlet='); ?>' + selected_outlet;
            }
        <?php } else { ?>
            alert('Anda tidak memiliki hak akses untuk menambah produk.');
        <?php } ?>
    }

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
</script>