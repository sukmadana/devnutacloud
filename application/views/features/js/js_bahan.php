<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="/js/datatables.custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="/js/bootstrap-select.min.js"></script>
<script type="text/javascript" src="/js/jquery.inputmask.bundle.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('#outlet').change(function() {
            var val = $(this).val();

            window.location = '<?= base_url('bahan/index?outlet='); ?>' + val;

        });

        $('select').selectpicker({
            liveSearch: true
        });

        // Datatables
        var dataTable = $('#grid-table').DataTable({
            processing: true,
            serverSide: true,
            bInfo: false,
            scrollX: true,
            scrollY: "600px",
            pageLength: 10,
            responsive: false,
            order: [
                [2, 'asc'],
                [1, 'asc']
            ],
            ajax: {
                url: window.base_url + 'bahan/ajaxIngredients?Outlet=<?= $selected_outlet ?>',
                type: "POST"
            },
            language: {
                loadingRecords: "Memuat data bahan . . .",
                processing: "Memuat data bahan . . ."
            },
            columnDefs: [{
                targets: [0, 5],
                orderable: false
            }]
        });

        $('.dataTables_filter, .dataTables_length').hide();

        $('#searchBox').keyup(delay(function(e) {
            dataTable.search($(this).val()).draw();
        }, 500));

        // -- Child Rows
        $('#grid-table tbody').on('click', '.ingredients-details', function() {
            var $this = $(this);
            var tr = $(this).closest('tr');
            var row = dataTable.row(tr);

            if (row.child.isShown()) {
                tr.find('a.ingredients-details.icon').html('<i class="fa fa-chevron-right"></i>')
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                jQuery.ajax({
                    method: "GET",
                    url: "<?= base_url() ?>bahan/ajax_detail_by_ingredients?IngredientsID=" + $this.data('ingredients-id') + "&IngredientsDeviceNo=" + $this.data('device-no') + "&OutletID=" + <?= $selected_outlet ?>,
                    dataType: "JSON",
                    beforeSend: function(xhr) {
                        tr.find('a.ingredients-details.icon').html('<i class="fa fa-spinner fa-spin"></i>')
                    },
                    success: function(result) {
                        tr.find('a.ingredients-details.icon').html('<i class="fa fa-chevron-down"></i>')

                        var html = '';
                        jQuery.each(result, function(i, v) {
                            html += '<table cellspacing="0" border="0" style="width:100%"><tr>';
                            html += '<td width="35%"><span class="pl-50">' + v.Produk + '</span></td>';
                            html += '<td><span>' + v.KebutuhanBahan + '</span></td>';
                            html += '</tr></table>';
                        });

                        row.child(html, 'no-padding').show();
                        tr.addClass('shown');
                    }
                });

            }
        });

        // Modal Add Bahan
        $('#modalAddBahan').on('shown.bs.modal', function() {
            var $this = $(this);
            $('#formAddBahan').bootstrapValidator('resetForm', true);
            $this.find('input[name="PurchasePrice"]').inputmask('Regex', {
                regex: "[0-9,]+",
                showMaskOnFocus: false,
                showMaskOnHover: false,
                clearIncomplete: true
            });
        });

        $('#modalAddBahan').on('keyup', 'input[name="PurchasePrice"]', function() {
            var $this = $(this);
            var validator = $('#formAddBahan').data('bootstrapValidator');
            var PurchasePriceLength = $this.val().length;

            if (PurchasePriceLength < 1) {
                validator.updateStatus('PurchasePrice', 'INVALID');
            }
        });

        $('#modalAddBahan').on('click', '.btnSimpan', function() {
            var $this = $(this);
            var saveType = $this.val();
            var validator = $('#formAddBahan').data('bootstrapValidator');
            var ItemNameLength = $('#formAddBahan').find('input[name="ItemName"]').val().length;
            var CategoryNameLength = $('#formAddBahan').find('input[name="CategoryName"]').val().length;
            var UnitLength = $('#formAddBahan').find('input[name="Unit"]').val().length;
            var PurchasePriceLength = $('#formAddBahan').find('input[name="PurchasePrice"]').val().length;

            if (ItemNameLength < 1) {
                validator.updateStatus('ItemName', 'INVALID', 'notEmpty');
            }
            if (CategoryNameLength < 1) {
                validator.updateStatus('CategoryName', 'INVALID');
            }
            if (UnitLength < 1) {
                validator.updateStatus('Unit', 'INVALID');
            }
            if (PurchasePriceLength < 1) {
                validator.updateStatus('PurchasePrice', 'INVALID');
            }

            validator.validate();
            if (validator.isValid()) {
                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>bahan/ajax_insert_bahan",
                    data: {
                        Outlet: "<?= $selected_outlet ?>",
                        ItemName: $('#formAddBahan').find('input[name="ItemName"]').val(),
                        CategoryName: $('#formAddBahan').find('input[name="CategoryName"]').val(),
                        Unit: $('#formAddBahan').find('input[name="Unit"]').val(),
                        PurchasePrice: $('#formAddBahan').find('input[name="PurchasePrice"]').val(),
                    },
                    dataType: "JSON",
                    beforeSend: function(xhr) {
                        $this.attr('disabled', true);
                        $this.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan');
                    },
                    success: function(response) {
                        $this.attr('disabled', false);
                        if (saveType == 'simpan') {
                            $this.html('Simpan');
                        } else {
                            $this.html('Simpan dan Tambah')
                        }
                        if (response.status == 200) {
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-success').show();

                            $('#formAddBahan').trigger('reset');
                            // $('#formAddBahan').find('select').val('default').selectpicker('refresh');

                            if (saveType == 'simpan') {
                                $('#modalAddBahan').modal('hide');
                            }

                            setTimeout(function() {
                                $('#alert-update-delete, #alert-update-delete .alert-success').hide();

                                var ajax_url = window.base_url + 'bahan/ajaxingredients?Outlet=<?= $selected_outlet ?>';
                                dataTable.ajax.url(ajax_url).load(function(result) {
                                    $('#totalData').text('Total jumlah Bahan : ' + result.recordsTotal);
                                });
                            }, 2000);
                        } else {
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-danger').show();
                        }
                    }
                });
            }
        });

        // Modal Edit Bahan
        $('#grid-table').on('click', 'tbody tr td .btnUpdateBahan', function() {
            var $this = $(this);

            jQuery.ajax({
                method: "GET",
                url: "<?= base_url() ?>bahan/ajax_get_detail_bahan?Outlet=<?= $selected_outlet ?>&ItemID=" + $this.data('ingredients-id') + "&DeviceNo=" + $this.data('device-no'),
                dataType: "JSON",
                beforeSend: function(xhr) {
                    $('#alert-update-delete .alert-success').html('<i class="fa fa-spin fa-spinner"></i> Memuat Form Edit Bahan.');
                    $('#alert-update-delete, #alert-update-delete .alert-success').show();
                },
                success: function(response) {
                    $('#alert-update-delete, #alert-update-delete .alert-success').hide();

                    $('#formEditBahan').find('input[name="ItemID"]').val(response.ItemID);
                    $('#formEditBahan').find('input[name="DeviceNo"]').val(response.DeviceNo);
                    $('#formEditBahan').find('input[name="ItemName"]').val(response.ItemName);
                    $('#formEditBahan').find('input[name="CategoryName"]').val(response.CategoryName);
                    // $('#formEditBahan').find('select[name="CategoryID"]').val(response.CategoryID).selectpicker('refresh')
                    $('#formEditBahan').find('input[name="Unit"]').val(response.Unit);
                    $('#formEditBahan').find('input[name="PurchasePrice"]').val(response.PurchasePrice);

                    $('#modalEditBahan').modal('show');
                }
            })
        });

        $('#modalEditBahan').on('shown.bs.modal', function() {
            var $this = $(this);
            $this.find('input[name="PurchasePrice"]').inputmask('Regex', {
                regex: "[0-9,]+",
                showMaskOnFocus: false,
                showMaskOnHover: false,
                clearIncomplete: true
            });
        });

        $('#modalEditBahan').on('keyup', 'input[name="PurchasePrice"]', function() {
            var $this = $(this);
            var validator = $('#formEditBahan').data('bootstrapValidator');
            var PurchasePriceLength = $this.val().length;

            if (PurchasePriceLength < 1) {
                validator.updateStatus('PurchasePrice', 'INVALID');
            }
        });

        $('#modalEditBahan').on('click', '.btnSimpan', function() {
            var $this = $(this);
            var saveType = $this.val();
            var validator = $('#formEditBahan').data('bootstrapValidator');
            validator.validate();
            var PurchasePriceLength = $('#formEditBahan').find('input[name="PurchasePrice"]').val().length;

            if (PurchasePriceLength < 1) {
                validator.updateStatus('PurchasePrice', 'INVALID');
            }

            if (validator.isValid() && PurchasePriceLength > 0) {
                $('#formEditBahan').find('input[name="PurchasePrice"]').closest('.form-group').removeClass('has-error');
                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>bahan/ajax_update_bahan",
                    data: {
                        Outlet: "<?= $selected_outlet ?>",
                        ItemID: $('#formEditBahan').find('input[name="ItemID"]').val(),
                        DeviceNo: $('#formEditBahan').find('input[name="DeviceNo"]').val(),
                        ItemName: $('#formEditBahan').find('input[name="ItemName"]').val(),
                        CategoryName: $('#formEditBahan').find('input[name="CategoryName"]').val(),
                        Unit: $('#formEditBahan').find('input[name="Unit"]').val(),
                        PurchasePrice: $('#formEditBahan').find('input[name="PurchasePrice"]').val(),
                    },
                    dataType: "JSON",
                    beforeSend: function(xhr) {
                        $this.attr('disabled', true);
                        $this.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan');
                    },
                    success: function(response) {
                        $this.attr('disabled', false);
                        $this.html('Simpan Perubahan');


                        if (response.status == 200) {
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-success').show();

                            $('#modalEditBahan').find('input[type="text"]').val('');
                            // $('#modalEditBahan').find('select').val('default').selectpicker('refresh');

                            $('#modalEditBahan').modal('hide');

                            setTimeout(function() {
                                $('#alert-update-delete, #alert-update-delete .alert-success').hide();

                                var ajax_url = window.base_url + 'bahan/ajaxingredients?Outlet=<?= $selected_outlet ?>';
                                dataTable.ajax.url(ajax_url).load(function(result) {
                                    $('#totalData').text('Total jumlah Bahan : ' + result.recordsTotal);
                                });
                            }, 2000);
                        } else {
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-danger').show();
                        }
                    }
                });
            }
        });

        // Hapus Bahan
        $('#grid-table').on('click', 'tbody tr .btnDeleteBahan', function() {
            var $this = $(this);
            var ItemID = $this.data('ingredients-id');
            var DeviceNo = $this.data('device-no');

            $('#modalHapus').find('input[name="ItemID"]').val(ItemID);
            $('#modalHapus').find('input[name="DeviceNo"]').val(DeviceNo);
            $('#modalHapus').modal('show');
        });

        $('#btnDelete').on('click', function() {
            var $this = $(this);
            $.ajax({
                method: "POST",
                url: "<?= base_url() . 'bahan/ajax_delete_bahan'; ?>",
                data: {
                    ItemID: $('#modalHapus').find('input[name="ItemID"]').val(),
                    DeviceNo: $('#modalHapus').find('input[name="DeviceNo"]').val(),
                    Outlet: "<?= $selected_outlet ?>"
                },
                dataType: 'JSON',
                beforeSend: function(xhr) {
                    $this.attr('disabled', true);
                    $this.html('<i class="fa fa-refresh fa-spin"></i> Menghapus');
                },
                success: function(response) {
                    $this.html('Yakin');
                    if (response.status == 200) {
                        $('#modalHapus').modal('hide');
                        $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                        $('#alert-update-delete, #alert-update-delete .alert-success').show();
                        setTimeout(function() {
                            $this.attr('disabled', false);
                            $('#alert-update-delete, #alert-update-delete .alert-danger').hide();

                            var ajax_url = window.base_url + 'bahan/ajaxingredients?Outlet=<?= $selected_outlet ?>';
                            dataTable.ajax.url(ajax_url).load(function(result) {
                                $('#totalData').text('Total jumlah Bahan : ' + result.recordsTotal);
                            });
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

        // Validation
        $('#formAddBahan').bootstrapValidator({
            submitButtons: '[class="btnSimpan"]',
            message: 'Form Tambah Bahan harus diisi dengan benar',
            fields: {
                ItemName: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Bahan tidak boleh kosong.'
                        },
                        remote: {
                            onkeyup: false,
                            message: 'Nama Bahan sudah ada.',
                            url: "<?= base_url() ?>bahan/ajax_validation_exist",
                            delay: 100,
                            type: "post",
                            data: {
                                Outlet: "<?= $selected_outlet ?>",
                                field: "ItemName",
                                value: function() {
                                    return $('#formAddBahan').find('input[name="ItemName"]').val();
                                }
                            }
                        }
                    }
                },
                // CategoryID: {
                //     validators: {
                //         notEmpty: {
                //             message: 'Kategori tidak boleh kosong.'
                //         },
                //     }
                // },
                CategoryName: {
                    validators: {
                        notEmpty: {
                            message: 'Kategori tidak boleh kosong.'
                        },
                    }
                },
                Unit: {
                    validators: {
                        notEmpty: {
                            message: 'Satuan tidak boleh kosong.'
                        },
                    }
                },
                PurchasePrice: {
                    validators: {
                        notEmpty: {
                            message: 'Harga Beli tidak boleh kosong.'
                        },
                    }
                },
            }
        });

        $('#formEditBahan').bootstrapValidator({
            message: 'Form Edit Bahan harus diisi dengan benar',
            fields: {
                ItemName: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Bahan tidak boleh kosong.'
                        },
                        remote: {
                            message: 'Nama Bahan sudah ada.',
                            url: "<?= base_url() ?>bahan/ajax_validation_exist",
                            delay: 100,
                            type: "post",
                            data: {
                                Outlet: "<?= $selected_outlet ?>",
                                field: "ItemName",
                                value: function() {
                                    return $('#formEditBahan').find('input[name="ItemName"]').val();
                                },
                                id: function() {
                                    return $('#formEditBahan').find('input[name="ItemID"]').val();
                                }
                            }
                        }
                    }
                },
                // CategoryID: {
                //     validators: {
                //         notEmpty: {
                //             message: 'Kategori tidak boleh kosong.'
                //         },
                //     }
                // },
                CategoryName: {
                    validators: {
                        notEmpty: {
                            message: 'Kategori tidak boleh kosong.'
                        },
                    }
                },
                Unit: {
                    validators: {
                        notEmpty: {
                            message: 'Satuan tidak boleh kosong.'
                        },
                    }
                },
                PurchasePrice: {
                    validators: {
                        notEmpty: {
                            message: 'Harga Beli tidak boleh kosong.'
                        },
                    }
                },
            }
        });
    });


    function redirectTonewItem() {
        <?php if ($visibilityMenu['ItemAdd']) { ?>
            var selected_outlet = xx('#outlet').val();
            if (selected_outlet == -999) {
                alert('Pilih outlet terlebih dahulu.');
            } else {
                window.location = '<?= base_url('bahan/itemform?outlet='); ?>' + selected_outlet;
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
