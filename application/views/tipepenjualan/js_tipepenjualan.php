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

            window.location = '<?= base_url('tipepenjualan/index?outlet='); ?>' + val;

        });

        $('select').selectpicker({
            liveSearch: true
        });

        // Datatables
        var dataTable = $('#grid-table').DataTable({
            responsive: false,
            scrollX: true,
            scrollY: "400px",
            bInfo: false,
            paging: false,
            columnDefs: [{
                targets: [1, 2, 3, 4, 5],
                orderable: false
            }]
        });

        $('.dataTables_filter, .dataTables_length').hide();

        $('#searchBox').keyup(delay(function(e) {
            dataTable.search($(this).val()).draw();
        }, 500));

        $('#modalAddOpsiMakan').on('shown.bs.modal', function() {
            var $this = $(this);
            $('#formAddOpsiMakan').find('input[name="MarkupPersen"]').inputmask({
                'alias': 'numeric',
                'radixPoint': ',',
                'autoGroup': true,
                'suffix': ' %',
                'placeholder': '',
                'prefix': '',
                'showMaskOnFocus': true,
                'showMaskOnHover': false,
                'clearIncomplete': true
            });

            $('#formAddOpsiMakan').find('input[name="ShareRevenue"]').inputmask({
                'alias': 'numeric',
                'radixPoint': ',',
                'autoGroup': true,
                'suffix': ' %',
                'placeholder': '',
                'prefix': '',
                'showMaskOnFocus': true,
                'showMaskOnHover': false,
                'clearIncomplete': true
            });

            $(this).on('change', '.OjekOnline', function() {
                if ($(this).is(':checked')) {
                    $('#accountOptionAdd').show();
                    $('#accountOptionAdd').closest('div.form-group').css({
                        "margin-bottom": "40px"
                    });
                    $this.find('input[name="OjekOnline"]').val('on');
                } else {
                    $('#accountOptionAdd').hide();
                    $('#accountOptionAdd').closest('div.form-group').css({
                        "margin-bottom": "15px"
                    });
                    $this.find('input[name="OjekOnline"]').val('off');
                }
            });

            $(this).on('change', '.MarkupRounding', function() {
                if ($(this).is(':checked')) {
                    $('#MarkupRoundingOptionAdd').show();
                    $this.find('input[name="MarkupRounding"]').val('on');
                } else {
                    $('#MarkupRoundingOptionAdd').hide();
                    $this.find('input[name="MarkupRounding"]').val('off');
                }
            });
        });

        $('#modalAddOpsiMakan').on('hidden.bs.modal', function() {
            if ($('#formAddOpsiMakan').find('.OjekOnline').prop('checked') == true) {
                $('#formAddOpsiMakan').find('.OjekOnline').trigger('click');
            }
            if ($('#formAddOpsiMakan').find('.MarkupRounding').prop('checked') == true) {
                $('#formAddOpsiMakan').find('.MarkupRounding').trigger('click');
            }
            $('#formAddOpsiMakan').find('input[name="MarkupPersen"]').inputmask("remove");
            $('#formAddOpsiMakan').find('input[name="ShareRevenue"]').inputmask("remove");
        });

        $('#modalAddOpsiMakan').on('keyup', 'input[name="MarkupPersen"]', function() {
            var $this = $(this);
            var validator = $('#formAddOpsiMakan').data('bootstrapValidator');
            var MarkupPersenLength = $this.val().length;

            if (MarkupPersenLength < 1) {
                validator.updateStatus('MarkupPersen', 'INVALID', 'notEmpty');
            } else {
                validator.updateStatus('MarkupPersen', 'VALID', 'notEmpty');
            }

            // if (check_nilai_diskon($this.val()) == false) {
            //     validator.updateStatus('MarkupPersen', 'INVALID', 'notEmpty');
            //     validator.updateMessage('MarkupPersen', 'notEmpty', 'Markup tidak boleh lebih dari 100%');
            // } else {
            //     validator.updateStatus('MarkupPersen', 'VALID', 'notEmpty');
            //     validator.updateMessage('MarkupPersen', 'notEmpty', 'Markup tidak boleh kosong.');
            // }
        });

        $('#modalAddOpsiMakan').on('keyup', 'input[name="ShareRevenue"]', function() {
            var $this = $(this);
            var validator = $('#formAddOpsiMakan').data('bootstrapValidator');
            var ShareRevenueLength = $this.val().length;

            if (ShareRevenueLength < 1) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
            } else {
                validator.updateStatus('ShareRevenue', 'VALID', 'notEmpty');
            }

            if (check_nilai_diskon($this.val()) == false) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
                validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh lebih dari 100%');
            } else {
                if (ShareRevenueLength < 1) {
                    validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
                    validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh kosong.');
                } else {
                    validator.updateStatus('ShareRevenue', 'VALID', 'notEmpty');
                    validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh kosong.');
                }

            }
        });

        // Simpan
        $('#modalAddOpsiMakan').on('click', '#btnSimpan', function() {
            var $this = $(this);
            var saveType = $this.val();
            var validator = $('#formAddOpsiMakan').data('bootstrapValidator');
            validator.validate();
            var MarkupPersenLength = $('#formAddOpsiMakan').find('input[name="MarkupPersen"]').val().length;
            var ShareRevenue = $('#formAddOpsiMakan').find('input[name="ShareRevenue"]').val();

            if (MarkupPersenLength < 1) {
                validator.updateStatus('MarkupPersen', 'INVALID', 'notEmpty');
            }
            if (ShareRevenue.length < 1) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
            }

            if (check_nilai_diskon(ShareRevenue) == false) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
                validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh lebih dari 100%');
            } else {
                validator.updateStatus('ShareRevenue', 'VALID', 'notEmpty');
                validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh kosong.');
            }

            if (validator.isValid()) {
                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>tipepenjualan/ajax_insert_tipepenjualan",
                    data: {
                        Outlet: "<?= $selected_outlet ?>",
                        NamaOpsiMakan: $('#formAddOpsiMakan').find('input[name="NamaOpsiMakan"]').val(),
                        OjekOnline: $('#formAddOpsiMakan').find('input[name="OjekOnline"]').val(),
                        Account: $('#formAddOpsiMakan').find('select[name="Account"]').val(),
                        MarkupPersen: $('#formAddOpsiMakan').find('input[name="MarkupPersen"]').val(),
                        ShareRevenue: $('#formAddOpsiMakan').find('input[name="ShareRevenue"]').val(),
                        MarkupRounding: $('#formAddOpsiMakan').find('input[name="MarkupRounding"]').val(),
                        MarkupRoundingType: $('#formAddOpsiMakan').find('input[name="MarkupRoundingType"]:checked').val(),
                        MarkupRoundingValue: $('#formAddOpsiMakan').find('input[name="MarkupRoundingValue"]:checked').val(),
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
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-success').show();

                            setTimeout(function() {
                                window.location = '<?= base_url('tipepenjualan/index?outlet='); ?>' + <?= $selected_outlet ?>;
                            }, 2000);
                        } else {
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-danger').show();
                        }
                    }
                });
            }
        });

        // Modal Edit
        $('#modalUpdateOpsiMakan').on('keyup', 'input[name="MarkupPersen"]', function() {
            var $this = $(this);
            var validator = $('#formUpdateOpsiMakan').data('bootstrapValidator');
            var MarkupPersenLength = $this.val().length;

            if (MarkupPersenLength < 1) {
                validator.updateStatus('MarkupPersen', 'INVALID', 'notEmpty');
            } else {
                validator.updateStatus('MarkupPersen', 'VALID', 'notEmpty');
            }
        });

        $('#modalUpdateOpsiMakan').on('keyup', 'input[name="ShareRevenue"]', function() {
            var $this = $(this);
            var validator = $('#formUpdateOpsiMakan').data('bootstrapValidator');
            var ShareRevenueLength = $this.val().length;

            if (ShareRevenueLength < 1) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
            } else {
                validator.updateStatus('ShareRevenue', 'VALID', 'notEmpty');
            }

            if (check_nilai_diskon($this.val()) == false) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
                validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh lebih dari 100%');
            } else {
                if (ShareRevenueLength < 1) {
                    validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
                    validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh kosong.');
                } else {
                    validator.updateStatus('ShareRevenue', 'VALID', 'notEmpty');
                    validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh kosong.');
                }

            }
        });

        $('#grid-table').on('click', 'tbody tr td .btnUpdateOpsiMakan', function() {
            var $this = $(this);

            jQuery.ajax({
                method: "GET",
                url: "<?= base_url() ?>tipepenjualan/ajax_get_detail_tipepenjualan?Outlet=<?= $selected_outlet ?>&OpsiMakanID=" + $this.data('opsimakan-id') + "&DeviceNo=" + $this.data('device-no'),
                dataType: "JSON",
                beforeSend: function(xhr) {
                    $('#alert-update-delete .alert-success').html('<i class="fa fa-spin fa-spinner"></i> Memuat Form Edit Tipe Penjualan.');
                    $('#alert-update-delete, #alert-update-delete .alert-success').show();
                },
                success: function(response) {
                    $('#alert-update-delete, #alert-update-delete .alert-success').hide();

                    $('#formUpdateOpsiMakan').find('input[name="OpsiMakanID"]').val(response.OpsiMakanID);
                    $('#formUpdateOpsiMakan').find('input[name="DeviceNo"]').val(response.DeviceNo);
                    $('#formUpdateOpsiMakan').find('input[name="NamaOpsiMakan"]').val(response.NamaOpsiMakan);
                    $('#formUpdateOpsiMakan').find('input[name="MarkupPersen"]').val(response.MarkupPersen);
                    $('#formUpdateOpsiMakan').find('input[name="ShareRevenue"]').val(response.ShareRevenue);

                    if (response.OjekOnline == 1) {
                        if ($('#formUpdateOpsiMakan').find('.OjekOnline').prop('checked') == false) {
                            $('#formUpdateOpsiMakan').find('.OjekOnline').trigger('click');
                        }
                        $('#formUpdateOpsiMakan').find('input[name="OjekOnline"]').val('on');
                        $('#formUpdateOpsiMakan').find('select[name="Account"]').selectpicker('val', response.AccountID + '#' + response.AccountDeviceNo);
                        $('#accountOptionEdit').show();
                        $('#accountOptionEdit').closest('div.form-group').css({
                            "margin-bottom": "40px"
                        });
                    } else {
                        $('#accountOptionEdit').hide();
                        $('#accountOptionEdit').closest('div.form-group').css({
                            "margin-bottom": "15px"
                        });
                    }

                    $('#formUpdateOpsiMakan').find('input[name="MarkupRoundingValue"][value="' + response.MarkupRoundingValue + '"]').attr('checked', true);
                    $('#formUpdateOpsiMakan').find('input[name="MarkupRoundingType"][value="' + response.MarkupRoundingType + '"]').attr('checked', true);

                    if (response.MarkupRounding.length > 0) {
                        if ($('#formUpdateOpsiMakan').find('.MarkupRounding').prop('checked') == false) {
                            $('#formUpdateOpsiMakan').find('.MarkupRounding').trigger('click');
                        }
                        $('#formUpdateOpsiMakan').find('#MarkupRoundingOptionEdit').show();
                        $('#formUpdateOpsiMakan').find('input[name="MarkupRounding"]').val('on');
                    }

                    $('#modalUpdateOpsiMakan').modal('show');
                }
            })
        });

        $('#modalUpdateOpsiMakan').on('shown.bs.modal', function() {
            var $this = $(this);
            $('#formUpdateOpsiMakan').find('input[name="MarkupPersen"]').inputmask({
                'alias': 'numeric',
                'radixPoint': ',',
                'autoGroup': true,
                'suffix': ' %',
                'placeholder': '',
                'prefix': '',
                'showMaskOnFocus': true,
                'showMaskOnHover': false,
                'clearIncomplete': true
            });

            $('#formUpdateOpsiMakan').find('input[name="ShareRevenue"]').inputmask({
                'alias': 'numeric',
                'radixPoint': ',',
                'autoGroup': true,
                'suffix': ' %',
                'placeholder': '',
                'prefix': '',
                'showMaskOnFocus': true,
                'showMaskOnHover': false,
                'clearIncomplete': true
            });

            $this.on('change', '.OjekOnline', function() {
                if ($(this).is(':checked')) {
                    $('#accountOptionEdit').show();
                    $('#accountOptionEdit').closest('div.form-group').css({
                        "margin-bottom": "40px"
                    });
                    $this.find('input[name="OjekOnline"]').val('on');
                } else {
                    $('#accountOptionEdit').hide();
                    $('#accountOptionEdit').closest('div.form-group').css({
                        "margin-bottom": "15px"
                    });
                    $this.find('input[name="OjekOnline"]').val('off');
                }
            });

            $this.on('change', '.MarkupRounding', function() {
                if ($(this).is(':checked')) {
                    $('#MarkupRoundingOptionEdit').show();
                    $this.find('input[name="MarkupRounding"]').val('on');
                } else {
                    $('#MarkupRoundingOptionEdit').hide();
                    $this.find('input[name="MarkupRounding"]').val('off');
                }
            });
        });

        $('#modalUpdateOpsiMakan').on('hidden.bs.modal', function() {
            if ($('#formUpdateOpsiMakan').find('.OjekOnline').prop('checked') == true) {
                $('#formUpdateOpsiMakan').find('.OjekOnline').trigger('click');
            }
            if ($('#formUpdateOpsiMakan').find('.MarkupRounding').prop('checked') == true) {
                $('#formUpdateOpsiMakan').find('.MarkupRounding').trigger('click');
            }
            $('#formUpdateOpsiMakan').find('input[name="MarkupPersen"]').inputmask("remove");
            $('#formUpdateOpsiMakan').find('input[name="ShareRevenue"]').inputmask("remove");
        });

        $('#modalUpdateOpsiMakan').on('click', '#btnSimpan', function() {
            var $this = $(this);
            var saveType = $this.val();
            var validator = $('#formUpdateOpsiMakan').data('bootstrapValidator');
            validator.validate();
            var MarkupPersenLength = $('#formUpdateOpsiMakan').find('input[name="MarkupPersen"]').val().length;
            var ShareRevenue = $('#formUpdateOpsiMakan').find('input[name="ShareRevenue"]').val();

            if (MarkupPersenLength < 1) {
                validator.updateStatus('MarkupPersen', 'INVALID', 'notEmpty');
            }
            if (ShareRevenue.length < 1) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
            }

            if (check_nilai_diskon(ShareRevenue) == false) {
                validator.updateStatus('ShareRevenue', 'INVALID', 'notEmpty');
                validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh lebih dari 100%');
            } else {
                validator.updateStatus('ShareRevenue', 'VALID', 'notEmpty');
                validator.updateMessage('ShareRevenue', 'notEmpty', 'Share Revenue (Bagi Hasil) tidak boleh kosong.');
            }
            if (validator.isValid()) {
                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>tipepenjualan/ajax_update_tipepenjualan",
                    data: {
                        Outlet: "<?= $selected_outlet ?>",
                        OpsiMakanID: $('#formUpdateOpsiMakan').find('input[name="OpsiMakanID"]').val(),
                        DeviceNo: $('#formUpdateOpsiMakan').find('input[name="DeviceNo"]').val(),
                        NamaOpsiMakan: $('#formUpdateOpsiMakan').find('input[name="NamaOpsiMakan"]').val(),
                        OjekOnline: $('#formUpdateOpsiMakan').find('input[name="OjekOnline"]').val(),
                        Account: $('#formUpdateOpsiMakan').find('select[name="Account"]').val(),
                        MarkupPersen: $('#formUpdateOpsiMakan').find('input[name="MarkupPersen"]').val(),
                        ShareRevenue: $('#formUpdateOpsiMakan').find('input[name="ShareRevenue"]').val(),
                        MarkupRounding: $('#formUpdateOpsiMakan').find('input[name="MarkupRounding"]').val(),
                        MarkupRoundingType: $('#formUpdateOpsiMakan').find('input[name="MarkupRoundingType"]:checked').val(),
                        MarkupRoundingValue: $('#formUpdateOpsiMakan').find('input[name="MarkupRoundingValue"]:checked').val(),
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
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-success').show();

                            setTimeout(function() {
                                window.location = '<?= base_url('tipepenjualan/index?outlet='); ?>' + <?= $selected_outlet ?>;
                            }, 2000);
                        } else {
                            $('#alert-update-delete').find('.alert-success').html('<p>' + response.message + '</p>');
                            $('#alert-update-delete, #alert-update-delete .alert-danger').show();
                        }
                    }
                });
            }
        });

        // Hapus
        $('#grid-table').on('click', 'tbody tr .btnDeleteOpsiMakan', function() {
            var $this = $(this);
            var OpsiMakanID = $this.data('opsimakan-id');
            var DeviceNo = $this.data('device-no');

            $('#modalHapus').find('input[name="DeviceNo"]').val(DeviceNo);
            $('#modalHapus').find('input[name="OpsiMakanID"]').val(OpsiMakanID);
            $('#modalHapus').modal('show');
        });

        $('#btnDelete').on('click', function() {
            var $this = $(this);
            $.ajax({
                method: "POST",
                url: "<?= base_url() . 'tipepenjualan/ajax_delete_tipepenjualan'; ?>",
                data: {
                    OpsiMakanID: $('#modalHapus').find('input[name="OpsiMakanID"]').val(),
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
                            window.location = '<?= base_url('tipepenjualan/index?outlet='); ?>' + <?= $selected_outlet ?>;
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
        $('#formAddOpsiMakan').bootstrapValidator({
            message: 'Form Tambah Tipe Penjualan harus diisi dengan benar',
            fields: {
                NamaOpsiMakan: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Tipe Penjualan tidak boleh kosong.'
                        },
                        remote: {
                            onkeyup: false,
                            message: 'Nama Bahan sudah ada.',
                            url: "<?= base_url() ?>tipepenjualan/ajax_validation_exist",
                            delay: 100,
                            type: "post",
                            data: {
                                Outlet: "<?= $selected_outlet ?>",
                                field: "NamaOpsiMakan",
                                value: function() {
                                    return $('#formAddOpsiMakan').find('input[name="NamaOpsiMakan"]').val();
                                }
                            }
                        }
                    }
                },
                OjekOnline: {
                    validators: {
                        notEmpty: {
                            message: 'Pilihan ojek online harus dipilih.'
                        }
                    }
                },
                Account: {
                    validators: {
                        callback: {
                            message: 'Pilihan Rekening harus diisi.',
                            callback: function(value, validator) {
                                var OjekOnline = validator.getFieldElements('OjekOnline').val();

                                if (OjekOnline == 'on' && value.length > 0) {
                                    console.log(value + ': true');
                                    return true;
                                } else {
                                    console.log(value + ': false');
                                    return false;
                                }
                            }
                        }
                    }
                },
                MarkupPersen: {
                    validators: {
                        notEmpty: {
                            message: 'Markup tidak boleh kosong.'
                        },
                    }
                },
                ShareRevenue: {
                    validators: {
                        notEmpty: {
                            message: 'Share Revenue (Bagi Hasil) tidak boleh kosong.'
                        },
                    }
                },
            }
        });

        $('#formUpdateOpsiMakan').bootstrapValidator({
            message: 'Form Edit Tipe Penjualan harus diisi dengan benar',
            fields: {
                NamaOpsiMakan: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Tipe Penjualan tidak boleh kosong.'
                        },
                        remote: {
                            onkeyup: false,
                            message: 'Nama Bahan sudah ada.',
                            url: "<?= base_url() ?>tipepenjualan/ajax_validation_exist",
                            delay: 100,
                            type: "post",
                            data: {
                                Outlet: "<?= $selected_outlet ?>",
                                field: "NamaOpsiMakan",
                                value: function() {
                                    return $('#formUpdateOpsiMakan').find('input[name="NamaOpsiMakan"]').val();
                                },
                                id: function() {
                                    return $('#formUpdateOpsiMakan').find('input[name="OpsiMakanID"]').val();
                                }
                            }
                        }
                    }
                },
                OjekOnline: {
                    validators: {
                        notEmpty: {
                            message: 'Pilihan ojek online harus dipilih.'
                        }
                    }
                },
                Account: {
                    validators: {
                        callback: {
                            message: 'Pilihan Rekening harus diisi.',
                            callback: function(value, validator) {
                                var OjekOnline = validator.getFieldElements('OjekOnline').val();

                                if (OjekOnline == 'on' && value.length > 0) {
                                    console.log(value + ': true');
                                    return true;
                                } else {
                                    console.log(value + ': false');
                                    return false;
                                }
                            }
                        }
                    }
                },
                MarkupPersen: {
                    validators: {
                        notEmpty: {
                            message: 'Markup tidak boleh kosong.'
                        },
                    }
                },
                ShareRevenue: {
                    validators: {
                        notEmpty: {
                            message: 'Share Revenue (Bagi Hasil) tidak boleh kosong.'
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