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
    var xx;
    jQuery(document).ready(function($) {
        $('#outlet').change(function() {
            var val = $(this).val();

            window.location = '<?= base_url('discount/index?outlet='); ?>' + val;

        });

        $('select').selectpicker({
            liveSearch: true
        });

        // Datatables
        var dataTable = $('#grid-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            scrollX: true,
            scrollY: "600px",
            bInfo: false,
            pageLength: 10,
            order: [0, 'asc'],
            ajax: {
                url: window.base_url + 'discount/ajaxdiscount?Outlet=<?= $selected_outlet ?>',
                type: "POST"
            },
            language: {
                loadingRecords: "Memuat data diskon . . .",
                processing: "Memuat data diskon . . ."
            },
            columnDefs: [{
                targets: [1, 2],
                orderable: false
            }]
        });

        $('.dataTables_filter, .dataTables_length').hide();

        $('#searchBox').keyup(delay(function(e) {
            dataTable.search($(this).val()).draw();
        }, 500));

        // Modal Add Diskon
        $('#modalAddDiscount').on('click', 'input[name="r_percent"]', function() {
            $('#formAddDiscount').find('input[name="Percent"]').val($(this).val());

            if ($(this).val() == 'yes') {

                $('#formAddDiscount').find('input[name="Discount"]').inputmask({
                    'alias': 'numeric',
                    'groupSeparator': '.',
                    'radixPoint': ',',
                    'autoGroup': true,
                    'suffix': ' %',
                    'placeholder': '0',
                    'prefix': '',
                    'showMaskOnFocus': true,
                    'showMaskOnHover': false,
                    'clearIncomplete': true
                });

                // $('#formAddDiscount').find('input[name="Discount"]').val(discountExist + '%');
            } else {
                $('#formAddDiscount').find('input[name="Discount"]').inputmask({
                    'alias': 'numeric',
                    'groupSeparator': '.',
                    'radixPoint': ',',
                    'autoGroup': true,
                    'suffix': '',
                    'placeholder': '0',
                    'prefix': 'Rp ',
                    'digits': 0,
                    'showMaskOnFocus': true,
                    'showMaskOnHover': false,
                    'clearIncomplete': true
                });
            }
        });

        $('#modalAddDiscount').on('shown.bs.modal', function() {
            var $this = $(this);
            $('#formAddDiscount').bootstrapValidator('resetForm', true);
            $('#formAddDiscount').find('input[name="r_percent"]').prop('checked', false);
            $('#formAddDiscount').find('input[name="Percent"]').val('');
            // $('#formAddDiscount').find('input[name="Discount"]').val('%');
        });

        $('#modalAddDiscount').on('keyup', 'input[name="Discount"]', function() {
            var $this = $(this);
            var validator = $('#formAddDiscount').data('bootstrapValidator');
            var discountLength = $this.val().length;

            if (discountLength < 1) {
                validator.updateStatus('Discount', 'INVALID', 'notEmpty');
            } else {
                validator.updateStatus('Discount', 'VALID', 'notEmpty');
            }

            if ($('#formAddDiscount').find('input[name="Percent"]').val() == 'yes') {
                if (check_nilai_diskon($this.val()) == false) {
                    validator.updateStatus('Discount', 'INVALID', 'notEmpty');
                    validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh lebih dari 100%');
                } else {
                    if (discountLength < 1) {
                        validator.updateStatus('Discount', 'INVALID', 'notEmpty');
                        validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh kosong.');
                    } else {
                        validator.updateStatus('Discount', 'VALID', 'notEmpty');
                        validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh kosong.');
                    }
                }
            }
        });

        $('#modalAddDiscount').on('click', '#btnSimpan', function() {
            var $this = $(this);
            var validator = $('#formAddDiscount').data('bootstrapValidator');
            validator.validate();
            var discount = $('#formAddDiscount').find('input[name="Discount"]').val();

            if (discount.length < 1) {
                validator.updateStatus('Discount', 'INVALID', 'notEmpty');
            }

            if (check_nilai_diskon(discount) == false) {
                validator.updateStatus('Discount', 'INVALID', 'notEmpty');
                validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh lebih dari 100%');
            }

            if (validator.isValid()) {
                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>discount/ajax_insert_discount",
                    data: {
                        Outlet: "<?= $selected_outlet ?>",
                        DiscountName: $('#formAddDiscount').find('input[name="DiscountName"]').val(),
                        Discount: $('#formAddDiscount').find('input[name="Discount"]').val(),
                        Percent: $('#formAddDiscount').find('input[name="Percent"]').val(),
                    },
                    dataType: "JSON",
                    beforeSend: function(xhr) {
                        $this.attr('disabled', true);
                        $this.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan');
                    },
                    success: function(response) {
                        $this.attr('disabled', false);
                        $this.html('Simpan');

                        if (response.status == 200) {
                            $('#modalAddDiscount').find('.alert-fixed-danger').hide();
                            $('#modalAddDiscount').find('.alert-fixed-success .alert-success').text(response.message);
                            $('#modalAddDiscount').find('.alert-fixed-success').show();

                            $('#modalAddDiscount').find('input[type="text"]').val('');
                            $('#modalAddDiscount').find('input[type="hidden"]').val('');

                            setTimeout(function() {
                                $('#modalAddDiscount').find('.alert-fixed-success').hide();
                                $('#modalAddDiscount').modal('hide');

                                var ajax_url = window.base_url + 'discount/ajaxdiscount?Outlet=<?= $selected_outlet ?>';
                                dataTable.ajax.url(ajax_url).load(function(result) {
                                    $('#totalData').text('Total jumlah Diskon : ' + result.recordsTotal);
                                });
                            }, 2000);
                        } else {
                            $('#modalAddDiscount').find('.alert-fixed-success').hide();
                            $('#modalAddDiscount').find('.alert-fixed-danger .alert-danger').text(response.message);
                            $('#modalAddDiscount').find('.alert-fixed-danger').show();
                        }
                    }
                });
            }
        });

        // Modal Edit Diskon
        $('#grid-table').on('click', 'tbody tr td .btnUpdateDiscount', function() {
            var $this = $(this);

            jQuery.ajax({
                method: "GET",
                url: "<?= base_url() ?>discount/ajax_get_detail_discount?Outlet=<?= $selected_outlet ?>&DiscountID=" + $this.data('discount-id') + "&DeviceNo=" + $this.data('device-no'),
                dataType: "JSON",
                beforeSend: function(xhr) {
                    $('#alert-update-delete .alert-success').html('<i class="fa fa-spin fa-spinner"></i> Memuat Form Edit Diskon.');
                    $('#alert-update-delete, #alert-update-delete .alert-success').show();
                },
                success: function(response) {
                    $('#alert-update-delete, #alert-update-delete .alert-success').hide();

                    $('#formEditDiscount').find('input[name="DiscountID"]').val(response.DiscountID);
                    $('#formEditDiscount').find('input[name="DeviceNo"]').val(response.DeviceNo);
                    $('#formEditDiscount').find('input[name="DiscountName"]').val(response.DiscountName);
                    $('#formEditDiscount').find('input[name="Discount"]').val(response.Discount);


                    if (response.percent == 'yes') {
                        $('#formEditDiscount').find('input[name="r_percent"][value="yes"]').prop('checked', true);
                        $('#formEditDiscount').find('input[name="r_percent"][value="no"]').prop('checked', false);
                    } else {
                        $('#formEditDiscount').find('input[name="r_percent"][value="yes"]').prop('checked', false);
                        $('#formEditDiscount').find('input[name="r_percent"][value="no"]').prop('checked', true);
                    }

                    $('#formEditDiscount').find('input[name="Percent"]').val(response.percent);


                    $('#modalEditDiscount').modal('show');
                }
            })
        });

        $('#modalEditDiscount').on('click', 'input[name="r_percent"]', function() {
            $('#formEditDiscount').find('input[name="Percent"]').val($(this).val());
            var discountExist = $('#formEditDiscount').find('input[name="Discount"]').val();

            if ($('#formEditDiscount').find('input[name="Percent"]').val() == 'yes') {
                $('#formEditDiscount').find('input[name="Discount"]').inputmask({
                    'alias': 'numeric',
                    'groupSeparator': '.',
                    'radixPoint': ',',
                    'autoGroup': true,
                    'suffix': ' %',
                    'placeholder': '0',
                    'prefix': '',
                    'showMaskOnFocus': true,
                    'showMaskOnHover': false,
                    'clearIncomplete': true
                });

                // $('#formEditDiscount').find('input[name="Discount"]').val(discountExist + '%');
            } else {
                $('#formEditDiscount').find('input[name="Discount"]').inputmask({
                    'alias': 'numeric',
                    'groupSeparator': '.',
                    'radixPoint': ',',
                    'autoGroup': true,
                    'suffix': '',
                    'placeholder': '0',
                    'prefix': 'Rp ',
                    'digits': 0,
                    'showMaskOnFocus': true,
                    'showMaskOnHover': false,
                    'clearIncomplete': true
                });
            }
        });

        $('#modalEditDiscount').on('hidden.bs.modal', function() {
            $('#formEditDiscount').find('input[name="Discount"]').inputmask("remove");
        });

        $('#modalEditDiscount').on('shown.bs.modal', function() {
            var $this = $(this);
            if ($this.find('input[name="Percent"]').val() == 'yes') {
                $('#formEditDiscount').find('input[name="Discount"]').inputmask({
                    'alias': 'numeric',
                    'radixPoint': ',',
                    'autoGroup': true,
                    'suffix': ' %',
                    'placeholder': '0',
                    'prefix': '',
                    'showMaskOnFocus': true,
                    'showMaskOnHover': false,
                    'clearIncomplete': true
                });
            } else {
                $('#formEditDiscount').find('input[name="Discount"]').inputmask({
                    'alias': 'numeric',
                    'groupSeparator': '.',
                    'radixPoint': ',',
                    'autoGroup': true,
                    'suffix': '',
                    'placeholder': '0',
                    'prefix': 'Rp ',
                    'digits': 0,
                    'showMaskOnFocus': true,
                    'showMaskOnHover': false,
                    'clearIncomplete': true
                });
            }
        });

        $('#modalEditDiscount').on('keyup', 'input[name="Discount"]', function() {
            var $this = $(this);
            var validator = $('#formEditDiscount').data('bootstrapValidator');
            var discountLength = $this.val().length;

            if (discountLength < 1) {
                validator.updateStatus('Discount', 'INVALID', 'notEmpty');
            } else {
                validator.updateStatus('Discount', 'VALID', 'notEmpty');
            }

            if ($('#formEditDiscount').find('input[name="Percent"]').val() == 'yes') {
                if (check_nilai_diskon($this.val()) == false) {
                    validator.updateStatus('Discount', 'INVALID', 'notEmpty');
                    validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh lebih dari 100%');
                } else {
                    if (discountLength < 1) {
                        validator.updateStatus('Discount', 'INVALID', 'notEmpty');
                        validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh kosong.');
                    } else {
                        validator.updateStatus('Discount', 'VALID', 'notEmpty');
                        validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh kosong.');
                    }
                }
            }

        });

        $('#modalEditDiscount').on('click', '#btnSimpan', function() {
            var $this = $(this);
            var validator = $('#formEditDiscount').data('bootstrapValidator');
            var discount = $('#formEditDiscount').find('input[name="Discount"]').val();

            if (discount.length < 1) {
                validator.updateStatus('Discount', 'INVALID', 'notEmpty');
            }

            if (check_nilai_diskon(discount) == false) {
                validator.updateStatus('Discount', 'INVALID', 'notEmpty');
                validator.updateMessage('Discount', 'notEmpty', 'Diskon tidak boleh lebih dari 100%');
            }

            validator.validate()
            if (validator.isValid()) {
                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>discount/ajax_update_discount",
                    data: {
                        Outlet: <?= $selected_outlet ?>,
                        DiscountID: $('#formEditDiscount').find('input[name="DiscountID"]').val(),
                        DeviceNo: $('#formEditDiscount').find('input[name="DeviceNo"]').val(),
                        DiscountName: $('#formEditDiscount').find('input[name="DiscountName"]').val(),
                        Discount: $('#formEditDiscount').find('input[name="Discount"]').val(),
                        Percent: $('#formEditDiscount').find('input[name="Percent"]').val(),
                    },
                    dataType: "JSON",
                    beforeSend: function(xhr) {
                        $this.attr('disabled', true);
                        $this.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan');
                    },
                    success: function(response) {
                        $this.attr('disabled', false);
                        $this.html('Simpan');

                        if (response.status == 200) {
                            $('#modalEditDiscount').find('.alert-fixed-danger').hide();
                            $('#modalEditDiscount').find('.alert-fixed-success .alert-success').text(response.message);
                            $('#modalEditDiscount').find('.alert-fixed-success').show();

                            $('#modalEditDiscount').find('input[type="text"]').val('');
                            $('#modalEditDiscount').find('input[type="hidden"]').val('');

                            setTimeout(function() {
                                $('#modalEditDiscount').find('.alert-fixed-success').hide();
                                $('#modalEditDiscount').modal('hide');

                                var ajax_url = window.base_url + 'discount/ajaxdiscount?Outlet=<?= $selected_outlet ?>';
                                dataTable.ajax.url(ajax_url).load(function(result) {
                                    $('#totalData').text('Total jumlah Diskon : ' + result.recordsTotal);
                                });
                            }, 2000);
                        } else {
                            $('#modalEditDiscount').find('.alert-fixed-success').hide();
                            $('#modalEditDiscount').find('.alert-fixed-danger .alert-danger').text(response.message);
                            $('#modalEditDiscount').find('.alert-fixed-danger').show();
                        }
                    }
                });
            }
        });

        // Hapus Diskon
        $('#grid-table').on('click', 'tbody tr .btnDeleteDiscount', function() {
            var $this = $(this);
            var DiscountID = $this.data('discount-id');
            var DeviceNo = $this.data('device-no');
            // var DiscountName = $this.data('discount-name');

            // $('#modalHapus').find('#delete-discount').text(DiscountName);
            $('#modalHapus').find('input[name="DiscountID"]').val(DiscountID);
            $('#modalHapus').find('input[name="DeviceNo"]').val(DeviceNo);
            $('#modalHapus').modal('show');
        });

        $('#btnDelete').on('click', function() {
            var $this = $(this);
            $.ajax({
                method: "POST",
                url: "<?= base_url() . 'discount/ajax_delete_discount'; ?>",
                data: {
                    DiscountID: $('#modalHapus').find('input[name="DiscountID"]').val(),
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

                            var ajax_url = window.base_url + 'discount/ajaxdiscount?Outlet=<?= $selected_outlet ?>';
                            dataTable.ajax.url(ajax_url).load(function(result) {
                                $('#totalData').text('Total jumlah Diskon : ' + result.recordsTotal);
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
        $('#formAddDiscount').bootstrapValidator({
            message: 'Form Tambah Diskon harus diisi dengan benar',
            fields: {
                DiscountName: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Diskon tidak boleh kosong.'
                        },
                    }
                },
                r_percent: {
                    validators: {
                        notEmpty: {
                            message: 'Type diskon ( % / Rp ) tidak boleh kosong.'
                        },
                    }
                },
                Discount: {
                    validators: {
                        notEmpty: {
                            message: 'Diskon tidak boleh kosong.'
                        }
                    }
                },
            }
        });
        $('#formEditDiscount').bootstrapValidator({
            message: 'Form Edit Diskon harus diisi dengan benar',
            fields: {
                DiscountName: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Diskon tidak boleh kosong.'
                        },
                    }
                },
                r_percent: {
                    validators: {
                        notEmpty: {
                            message: 'Type diskon ( % / Rp ) tidak boleh kosong.'
                        },
                    }
                },
                Discount: {
                    validators: {
                        notEmpty: {
                            message: 'Diskon tidak boleh kosong.'
                        },
                    }
                },
            }
        });
    });

    function check_nilai_diskon(value) {
        var rep1 = value.replace('%', '');
        var rep2 = rep1.replace('.', '');
        var rep3 = rep2.replace(',', '.');

        var lastString = value[value.length - 1];

        if (value.length > 0 && parseFloat(rep3) > 100 && lastString == '%') {
            console.log('false: ' + rep3);
            return false;
        } else {
            console.log('true: ' + rep3);
            return true;
        }
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