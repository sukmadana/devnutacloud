<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="/js/datatables.custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="/js/bootstrap-select.min.js"></script>
<script type="text/javascript">
    var xx;
    jQuery(document).ready(function($) {
        $('#outlet').change(function() {
            var val = $(this).val();

            window.location = '<?= base_url('extra/index?outlet='); ?>' + val;

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
            order: [1, 'asc'],
            ajax: {
                url: window.base_url + 'extra/ajaxmodifier?Outlet=<?= $selected_outlet ?>',
                type: "POST"
            },
            language: {
                loadingRecords: "Memuat data pilihan ekstra . . .",
                processing: "Memuat data pilihan ekstra . . ."
            },
            columnDefs: [{
                targets: [0, 2, 3],
                orderable: false
            }]
        });

        $('.dataTables_filter, .dataTables_length').hide();

        $('#searchBox').keyup(delay(function(e) {
            dataTable.search($(this).val()).draw();
        }, 500));

        // -- Child Rows
        $('#grid-table tbody').on('click', '.modifier-details', function() {
            var $this = $(this);
            var tr = $(this).closest('tr');
            var row = dataTable.row(tr);

            if (row.child.isShown()) {
                tr.find('a.modifier-details.icon').html('<i class="fa fa-chevron-right"></i>')
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                jQuery.ajax({
                    method: "GET",
                    url: "<?= base_url() ?>extra/ajax_detail_by_modifier?ModifierID=" + $this.data('modifier-id') + "&DeviceNo=" + $this.data('device-no') + "&OutletID=" + <?= $selected_outlet ?>,
                    dataType: "JSON",
                    beforeSend: function(xhr) {
                        tr.find('a.modifier-details.icon').html('<i class="fa fa-spinner fa-spin"></i>')
                    },
                    success: function(result) {
                        tr.find('a.modifier-details.icon').html('<i class="fa fa-chevron-down"></i>')

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

        // List Choice
        var indexChoice = 3;

        $('#modalAddModifier').on('shown.bs.modal', function() {

            $(this).on('keyup', '.ChoiceName', function() {
                if ($(this).closest('tr').is(':last-child')) {
                    if ($(this).val().length > 0) {
                        $('#btnAddChoice').trigger('click');
                    }
                }
            });
        });

        $('#btnAddChoice').on('click', function() {
            indexChoice = indexChoice + 1;

            var CanAddQuantity = $('#formAddModifier').find('.CanAddQuantity');
            var display = '';
            if (CanAddQuantity.is(':checked')) {
                display = 'block';
                var html = `<tr class="additional">
                            <td>
                                <div class="row">
                                    <div class="col-name col-md-6 col-sm-6 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="" placeholder="Qty">
                                    </div>
                                    <div class="col-price col-md-3 col-sm-3 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2  text-left pl-0">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
            } else {
                display = 'none';
                var html = `<tr class="additional">
                            <td>
                                <div class="row">
                                    <div class="col-name col-md-7 col-sm-7 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="" placeholder="Qty">
                                    </div>
                                    <div class="col-price col-md-4 col-sm-4 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2  text-left pl-0">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
            }

            $(html).appendTo('#listChoiceAdd');

            $('.btnRemoveChoice').on('click', function() {
                var $this = $(this);
                $this.closest('tr').remove();
            })
        });

        $('.ChooseOneOnly').on('change', function() {
            var $this = $(this);

            if ($this.is(':checked')) {
                $this.closest('div').find('input[name="ChooseOneOnly"]').val(1);
            } else {
                $this.closest('div').find('input[name="ChooseOneOnly"]').val(0);
            }
        });

        $('.CanAddQuantity').on('change', function() {
            var $this = $(this);

            if ($this.is(':checked')) {
                $this.closest('div').find('input[name="CanAddQuantity"]').val(1);
                $('.OptionQtyNeed').show();
                $('.col-name').removeClass('col-md-7');
                $('.col-name').removeClass('col-sm-7');
                $('.col-name').addClass('col-md-6 col-sm-6');

                $('.col-price').removeClass('col-md-4');
                $('.col-price').removeClass('col-sm-4');
                $('.col-price').addClass('col-md-3 col-sm-3');
            } else {
                $this.closest('div').find('input[name="CanAddQuantity"]').val(0);
                $('.OptionQtyNeed').hide();
                $('.col-name').removeClass('col-md-6');
                $('.col-name').removeClass('col-sm-6');
                $('.col-name').addClass('col-md-7 col-sm-7');

                $('.col-price').removeClass('col-md-3');
                $('.col-price').removeClass('col-sm-3');
                $('.col-price').addClass('col-md-4 col-sm-4');
            }
        });

        $('.btnRemoveChoice').on('click', function() {
            var $this = $(this);
            $this.closest('tr').remove();
        });

        $('#btnSimpan').on('click', function() {
            var $this = $(this);
            var validator = $('#formAddModifier').data('bootstrapValidator');
            validator.validate();

            if (validator.isValid()) {
                var ChoiceName = $('#formAddModifier').find('.ChoiceName').map(function() {
                    return this.value;
                }).get();

                var ChoicePrice = $('#formAddModifier').find('.ChoicePrice').map(function() {
                    return this.value;
                }).get();

                var QtyNeed = $('#formAddModifier').find('.QtyNeed').map(function() {
                    return this.value;
                }).get();

                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>extra/ajax_create_modifier",
                    data: {
                        Outlet: "<?= $selected_outlet ?>",
                        ModifierName: $('#formAddModifier').find('input[name="ModifierName"]').val(),
                        ChooseOneOnly: $('#formAddModifier').find('input[name="ChooseOneOnly"]').val(),
                        CanAddQuantity: $('#formAddModifier').find('input[name="CanAddQuantity"]').val(),
                        'ChoiceName[]': ChoiceName,
                        'ChoicePrice[]': ChoicePrice,
                        'QtyNeed[]': QtyNeed,
                    },
                    dataType: 'JSON',
                    beforeSend: function(xhr) {
                        $this.attr('disabled', true);
                        $this.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan');
                    },
                    success: function(response) {
                        $this.attr('disabled', false);
                        $this.html('Ok');

                        if (response.status == 200) {
                            $('#modalAddModifier').find('.alert-fixed-danger').hide();
                            $('#modalAddModifier').find('.alert-fixed-success .alert-success').text(response.message);
                            $('#modalAddModifier').find('.alert-fixed-success').show();

                            $('#formAddModifier').find('input[name="ModifierName"]').val('');
                            $('#formAddModifier').find('#ChooseOneOnly').attr('checked', false);
                            $('#formAddModifier').find('input[name="ChooseOneOnly"]').val(0);
                            $('#formAddModifier').find('#listChoiceAdd .additional').remove();
                            indexChoice = 3;
                            $('#formAddModifier').find('input[type="text"]').val('');

                            setTimeout(function() {
                                $('#modalAddModifier').find('.alert-fixed-success').hide();
                                $('#modalAddModifier').modal('hide');

                                var ajax_url = '<?= base_url() ?>extra/ajaxmodifier?Outlet=<?= $selected_outlet ?>';
                                dataTable.ajax.url(ajax_url).load(function(result) {
                                    $('#totalData').text('Total jumlah Pilihan Ekstra : ' + result.recordsTotal);
                                });
                            }, 2000);
                        } else {
                            $('#modalAddModifier').find('.alert-fixed-success').hide();
                            $('#modalAddModifier').find('.alert-fixed-danger .alert-danger').text(response.message);
                            $('#modalAddModifier').find('.alert-fixed-danger').show();
                        }
                    }
                });
            }

        });

        // Show modal edit

        $('#grid-table').on('click', 'tbody tr .btnUpdateModifier', function() {
            var $this = $(this);

            jQuery.ajax({
                method: "GET",
                url: "<?= base_url() ?>extra/ajax_get_detail_modifier?Outlet=<?= $selected_outlet ?>&ModifierID=" + $this.data('modifier-id') + "&ModifierID=" + $this.data('modifier-id') + "&DeviceNo=" + $this.data('device-no') + "",
                dataType: "JSON",
                beforeSend: function(xhr) {
                    $('#alert-update-delete .alert-success').html('<i class="fa fa-spin fa-spinner"></i> Memuat Form Edit Pilihan Ekstra.');
                    $('#alert-update-delete, #alert-update-delete .alert-success').show();
                },
                success: function(response) {
                    $('#alert-update-delete, #alert-update-delete .alert-success').hide();

                    $('#formEditModifier').find('input[name="ModifierID"]').val(response.ModifierID);
                    $('#formEditModifier').find('input[name="DeviceNo"]').val(response.DeviceNo);
                    $('#formEditModifier').find('input[name="ModifierName"]').val(response.ModifierName);
                    $('#formEditModifier').find('input[name="ChooseOneOnly"]').val(response.ChooseOneOnly);
                    <?php if ($Options->StockModifier == 1) { ?>
                        $('#formEditModifier').find('input[name="CanAddQuantity"]').val(response.CanAddQuantity);
                    <?php } ?>


                    $('#formEditModifier').find('.btnDeleteModifier').data('modifier-id', response.ModifierID);
                    $('#formEditModifier').find('.btnDeleteModifier').data('device-no', response.DeviceNo);
                    $('#formEditModifier').find('.btnDeleteModifier').data('modifier-name', response.ModifierName);

                    if (response.ChooseOneOnly == 1) {
                        if ($('#formEditModifier').find('.ChooseOneOnly').prop('checked') == false) {
                            $('#formEditModifier').find('.ChooseOneOnly').trigger('click');
                        }
                    }
                    <?php if ($Options->StockModifier == 1) { ?>
                        indexChoice = 0;

                        var display = '';
                        if (response.CanAddQuantity == 1) {
                            display = 'block';
                        } else {
                            display = 'none';
                        }

                        var html = '';
                        $.each(response.rs_detail, function(i, v) {
                            indexChoice = indexChoice + 1;
                            html += `<tr>
                            <td>
                                <div class="row">
                                    <div class="col-name col-md-7 col-sm-7 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="` + v.ChoiceName + `" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="` + v.QtyNeed + `" placeholder="Qty">
                                    </div>
                                    <div class="col-price col-md-4 col-sm-4 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="` + v.ChoicePrice + `" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2 text-left pl-0">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                        });

                        indexChoice = indexChoice + 1;
                        html += `<tr>
                            <td>
                                <div class="row">
                                    <div class="col-name col-md-7 col-sm-7 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="" placeholder="Qty">
                                    </div>
                                    <div class="col-price col-md-4 col-sm-4 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2 text-left pl-0">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                        $(html).appendTo('#listChoiceEdit');
                        if (response.CanAddQuantity == 1) {
                            if ($('#formEditModifier').find('.CanAddQuantity').prop('checked') == false) {
                                $('#formEditModifier').find('.CanAddQuantity').trigger('click');
                            }
                        } else {
                            $('.OptionQtyNeed').hide();
                            $('.col-name').removeClass('col-md-6');
                            $('.col-name').removeClass('col-sm-6');
                            $('.col-name').addClass('col-md-7 col-sm-7');

                            $('.col-price').removeClass('col-md-3');
                            $('.col-price').removeClass('col-sm-3');
                            $('.col-price').addClass('col-md-4 col-sm-4');
                        }
                    <?php } else { ?>
                        indexChoice = 0;

                        var display = 'none';

                        var html = '';
                        $.each(response.rs_detail, function(i, v) {
                            indexChoice = indexChoice + 1;
                            html += `<tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-7 col-sm-7 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="` + v.ChoiceName + `" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="` + v.QtyNeed + `" placeholder="Qty">
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="` + v.ChoicePrice + `" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2 text-left pl-0">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                        });

                        indexChoice = indexChoice + 1;
                        html += `<tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-7 col-sm-7 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="" placeholder="Qty">
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2 text-left pl-0">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                        $(html).appendTo('#listChoiceEdit');
                    <?php } ?>
                    $('.btnRemoveChoice').on('click', function() {
                        var $this = $(this);
                        $this.closest('tr').remove();
                    })

                    $('#modalEditModifier').modal('show');
                }
            })

        });

        $('#modalEditModifier').on('shown.bs.modal', function() {

            $(this).on('keyup', '.ChoiceName', function() {
                if ($(this).closest('tr').is(':last-child')) {
                    if ($(this).val().length > 0) {
                        $('#btnAddChoiceEdit').trigger('click');
                    }
                }
            });
        });

        // On close modal edit
        $('#modalEditModifier').on('hidden.bs.modal', function() {
            if ($('#formEditModifier').find('.ChooseOneOnly').is(':checked')) {
                $('#formEditModifier').find('.ChooseOneOnly').trigger('click');
            }

            if ($('#formEditModifier').find('.CanAddQuantity').is(':checked')) {
                $('#formEditModifier').find('.CanAddQuantity').trigger('click');
            }

            $('#formEditModifier').find('#listChoiceEdit').html('');
        });

        $('#btnAddChoiceEdit').on('click', function() {
            indexChoice = indexChoice + 1;

            var CanAddQuantity = $('#formEditModifier').find('.CanAddQuantity');
            var display = '';
            if (CanAddQuantity.is(':checked')) {
                display = 'block';
                var html = `<tr>
                            <td>
                                <div class="row">
                                    <div class="col-name col-md-6 col-sm-6 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="" placeholder="Qty">
                                    </div>
                                    <div class="col-price col-md-3 col-sm-3 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2 text-center">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
            } else {
                display = 'none';
                var html = `<tr>
                            <td>
                                <div class="row">
                                    <div class="col-name col-md-7 col-sm-7 col-xs-12 mb-5">
                                        <input type="text" class="form-control ChoiceName" name="ChoiceName[` + indexChoice + `]" value="" placeholder="Misal : Keju">
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-5 OptionQtyNeed" style="display:` + display + `">
                                        <input type="text" class="form-control QtyNeed" name="QtyNeed[` + indexChoice + `]" value="" placeholder="Qty">
                                    </div>
                                    <div class="col-price col-md-4 col-sm-4 col-xs-5">
                                        <input type="text" class="form-control ChoicePrice" name="ChoicePrice[` + indexChoice + `]" value="" placeholder="Rp">
                                    </div>
                                    <div class="col-md-1 col-sm-1 col-xs-2 text-center">
                                        <a href="#" class="text-muted btnRemoveChoice">
                                            <i class="fa fa-times-circle fa-2x"></i>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
            }


            $(html).appendTo('#listChoiceEdit');

            $('.btnRemoveChoice').on('click', function() {
                var $this = $(this);
                $this.closest('tr').remove();
            })
        });

        $('#modalEditModifier').on('click', '#btnSimpan', function() {
            var $this = $(this);
            var validator = $('#formEditModifier').data('bootstrapValidator');
            validator.validate();

            if (validator.isValid()) {
                var ChoiceName = $('#formEditModifier').find('.ChoiceName').map(function() {
                    return this.value;
                }).get();

                var ChoicePrice = $('#formEditModifier').find('.ChoicePrice').map(function() {
                    return this.value;
                }).get();

                var QtyNeed = $('#formEditModifier').find('.QtyNeed').map(function() {
                    return this.value;
                }).get();

                jQuery.ajax({
                    method: "POST",
                    url: "<?= base_url() ?>extra/ajax_update_modifier",
                    data: {
                        Outlet: "<?= $selected_outlet ?>",
                        ModifierID: $('#formEditModifier').find('input[name="ModifierID"]').val(),
                        DeviceNo: $('#formEditModifier').find('input[name="DeviceNo"]').val(),
                        ModifierName: $('#formEditModifier').find('input[name="ModifierName"]').val(),
                        ChooseOneOnly: $('#formEditModifier').find('input[name="ChooseOneOnly"]').val(),
                        CanAddQuantity: $('#formEditModifier').find('input[name="CanAddQuantity"]').val(),
                        'ChoiceName[]': ChoiceName,
                        'ChoicePrice[]': ChoicePrice,
                        'QtyNeed[]': QtyNeed,
                    },
                    dataType: 'JSON',
                    beforeSend: function(xhr) {
                        $this.attr('disabled', true);
                        $this.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan');
                    },
                    success: function(response) {
                        $this.attr('disabled', false);
                        $this.html('Simpan');

                        if (response.status == 200) {
                            $('#modalEditModifier').find('.alert-fixed-danger').hide();
                            $('#modalEditModifier').find('.alert-fixed-success .alert-success').text(response.message);
                            $('#modalEditModifier').find('.alert-fixed-success').show();

                            $('#formEditModifier').find('input[name="ModifierName"]').val('');
                            $('#formEditModifier').find('#ChooseOneOnly').attr('checked', false);
                            $('#formEditModifier').find('input[name="ChooseOneOnly"]').val(0);
                            $('#formEditModifier').find('#listChoiceAdd .additional').remove();
                            indexChoice = 3;
                            $('#formEditModifier').find('input[type="text"]').val('');

                            setTimeout(function() {

                                $('#modalEditModifier').find('.alert-fixed-success').hide();
                                $('#modalEditModifier').modal('hide');

                                var ajax_url = '<?= base_url() ?>extra/ajaxmodifier?Outlet=<?= $selected_outlet ?>';
                                dataTable.ajax.url(ajax_url).load(function(result) {
                                    $('#totalData').text('Total jumlah Pilihan Ekstra : ' + result.recordsTotal);
                                });
                            }, 2000);
                        } else {
                            $('#modalEditModifier').find('.alert-fixed-success').hide();
                            $('#modalEditModifier').find('.alert-fixed-danger .alert-danger').text(response.message);
                            $('#modalEditModifier').find('.alert-fixed-danger').show();
                        }
                    }
                });
            }

        });

        // Modal Terapkan Produk
        $('#grid-table').on('click', 'tbody tr .btnTerapkanProduk', function() {
            var $this = $(this);
            var ModifierID = $this.data('modifier-id');
            var DeviceNo = $this.data('device-no');
            var ModifierName = $this.data('modifier-name');

            $('#modalTerapkanProduk').find('.ModifierName').text(ModifierName);
            $('#modalTerapkanProduk').find('#ModifierID').val(ModifierID);
            $('#modalTerapkanProduk').find('#DeviceNo').val(DeviceNo);
            $('#modalTerapkanProduk').modal('show');
        });

        // Create datatable untuk produk
        var selecteditems = 0;
        // Create produk datatable
        var tableProduk = $('#table-terapkan-produk').DataTable({
            "ajax": '',
            "columns": [{
                "data": null,
                "defaultContent": ''
            }, {
                "data": "ItemName"
            }, {
                "data": "CategoryName",
                "className": "text-muted"
            }],
            "columnDefs": [{
                    "targets": 0,
                    "data": "ItemID",
                    "render": function(data, type, row, meta) {
                        var checked = '';
                        if (data.status == 'checked') {
                            checked = 'checked=""';
                        }
                        return '<input type="checkbox" class="mr-10 pull-right select-item" name="ItemsID[]" value="' + data.ItemID + '" ' + checked + '><input type="hidden" name="DeviceNo[]" value="' + data.DeviceNo + '">'
                    }
                },
                {
                    "targets": [0, 2],
                    "orderable": false
                },
                {
                    "targets": [0],
                    "searchable": false
                }
            ],
            "language": {
                "loadingRecords": "Memuat data produk . . .",
                "processing": "Memuat data produk . . ."
            },
            "scrollY": "300px",
            "responsive": false,
            "scrollX": true,
            "scrollCollapse": true,
            "paging": false,
            "ordering": false,
            "info": false,
            "drawCallback": function(settings) {

                var ModifierID = $('#modalTerapkanProduk').find('#ModifierID').val();
                var DeviceNo = $('#modalTerapkanProduk').find('#DeviceNo').val();
                $.ajax({
                    url: "<?= base_url() ?>extra/ajax_count_items_modifier?outlet=<?= $selected_outlet ?>&ModifierID=" + ModifierID + "&DeviceNo=" + DeviceNo,
                    method: "GET",
                    dataType: "JSON",
                    success: function(response) {
                        $('#loadingProduk').hide();
                        selecteditems = response.total;
                        $('#countTerapkanProduk').text(selecteditems);
                        $('#table-terapkan-produk').show();
                    }
                })
            }
        });

        $('.dataTables_filter, .dataTables_length').hide();

        // Event pencarian modal pindahkan produk
        $('#searchBoxItems').keyup(delay(function(e) {
            var value = $(this).val().toLowerCase();
            $("#table-terapkan-produk tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        }, 500));

        // Modal Pindahkan Produk Show
        $('#modalTerapkanProduk').on('show.bs.modal', function() {
            // Show Table of Produk
            var ModifierID = $('#modalTerapkanProduk').find('#ModifierID').val();
            var DeviceNo = $('#modalTerapkanProduk').find('#DeviceNo').val();
            var ajax_url = "<?= base_url() ?>extra/ajax_items_belum_diterapkan?outlet=<?= $selected_outlet ?>&ModifierID=" + ModifierID + "&DeviceNo=" + DeviceNo + "";
            $('#countTerapkanProduk').text('0');
            $('#searchBoxItems').val('');
            // load ajax baru tiap modal terapkan produk terbuka
            $('#loadingProduk').show();
            $('#table-terapkan-produk').hide();
            tableProduk.ajax.url(ajax_url).load();
            $('.dataTables_filter, .dataTables_length').hide();
        });

        // Event selected item
        $('#table-terapkan-produk').on('change', 'tbody tr .select-item', function() {
            var $this = $(this);

            if ($this.is(':checked')) {
                selecteditems = selecteditems + 1;
            } else {
                selecteditems = selecteditems - 1;
            }

            $('#countTerapkanProduk').text(selecteditems);
        });

        // Terapkan Produk
        $('#btnSimpanProduk').on('click', function() {
            var $this = $(this);

            var itemsID = $('#formTerapkanProduk').find('input[name="ItemsID[]"]').map(function() {
                if ($(this).is(":checked")) {
                    return this.value;
                }
            }).get();

            var itemsDeviceNo = $('#formTerapkanProduk').find('input[name="DeviceNo[]"]').map(function() {
                if ($(this).closest('td').find('input[name="ItemsID[]"]').is(":checked")) {
                    return this.value;
                }
            }).get();

            $.ajax({
                method: "POST",
                url: "<?= base_url() . 'extra/ajax_terapkan_produk'; ?>",
                data: {
                    outlet: "<?= $selected_outlet ?>",
                    ModifierID: $('#formTerapkanProduk').find('input[name="ModifierID"]').val(),
                    DeviceNo: $('#formTerapkanProduk').find('input[name="DeviceNo"]').val(),
                    'itemsID[]': itemsID,
                    'itemsDeviceNo[]': itemsDeviceNo,
                },
                dataType: 'JSON',
                beforeSend: function(xhr) {
                    $this.attr('disabled', true);
                    $this.html('<i class="fa fa-refresh fa-spin"></i> Menyimpan');
                },
                success: function(response) {
                    $this.attr('disabled', false);
                    $this.html('Terapkan pada Produk');
                    if (response.status == 200) {
                        $('#modalTerapkanProduk').find('.alert-produk.alert-fixed-danger').hide();
                        $('#modalTerapkanProduk').find('.alert-produk .alert-success').text(response.message);
                        $('#modalTerapkanProduk').find('.alert-produk.alert-fixed-success').show();

                        $('#formTerapkanProduk').find('input[name="ModifierID"]').val('');
                        $('#formTerapkanProduk').find('.select-item').attr('checked', false);

                        setTimeout(function() {
                            $('#modalTerapkanProduk').find('.alert-produk.alert-fixed-success').hide();
                            $('#modalTerapkanProduk').modal('hide');

                            var ajax_url = '<?= base_url() ?>extra/ajaxmodifier?Outlet=<?= $selected_outlet ?>';
                            dataTable.ajax.url(ajax_url).load(function(result) {
                                $('#totalData').text('Total jumlah Pilihan Ekstra : ' + result.recordsTotal);
                            });
                        }, 2000);
                    } else {
                        $('#modalTerapkanProduk').find('.alert-produk.alert-fixed-success').hide();
                        $('#modalTerapkanProduk').find('.alert-produk .alert-danger').text(response.message);
                        $('#modalTerapkanProduk').find('.alert-produk.alert-fixed-danger').show();
                    }
                }
            })
        });


        // Modal Hapus Nama Kategori
        $('#grid-table').on('click', 'tbody tr .btnDeleteModifier', function() {
            var $this = $(this);
            var ModifierID = $this.data('modifier-id');
            var DeviceNo = $this.data('device-no');
            var ModifierName = $this.data('modifier-name');

            $('#modalHapus').find('#delete-modifier').text(ModifierName);
            $('#modalHapus').find('input[name="ModifierID"]').val(ModifierID);
            $('#modalHapus').find('input[name="DeviceNo"]').val(DeviceNo);
            $('#modalHapus').modal('show');
        });

        $('.btnDeleteModifier').on('click', function() {
            var $this = $(this);
            var ModifierID = $this.data('modifier-id');
            var DeviceNo = $this.data('device-no');
            var ModifierName = $this.data('modifier-name');

            $('#modalEditModifier').modal('hide');

            $('#modalHapus').find('#delete-modifier').text(ModifierName);
            $('#modalHapus').find('input[name="ModifierID"]').val(ModifierID);
            $('#modalHapus').find('input[name="DeviceNo"]').val(DeviceNo);
            $('#modalHapus').modal('show');
        });

        $('#btnDelete').on('click', function() {
            var $this = $(this);
            $.ajax({
                method: "POST",
                url: "<?= base_url() . 'extra/ajax_delete_modifier'; ?>",
                data: {
                    ModifierID: $('#modalHapus').find('input[name="ModifierID"]').val(),
                    DeviceNo: $('#modalHapus').find('input[name="DeviceNo"]').val(),
                    outlet: "<?= $selected_outlet ?>"
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
                            $('#alert-update-delete, #alert-update-delete .alert-success').hide();

                            var ajax_url = '<?= base_url() ?>extra/ajaxmodifier?Outlet=<?= $selected_outlet ?>';
                            dataTable.ajax.url(ajax_url).load(function(result) {
                                $('#totalData').text('Total jumlah Pilihan Ekstra : ' + result.recordsTotal);
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

        function countItemsModifier() {
            var ModifierID = $('#modalTerapkanProduk').find('#ModifierID').val();
            var DeviceNo = $('#modalTerapkanProduk').find('#DeviceNo').val();
            $.ajax({
                url: "<?= base_url() ?>extra/ajax_count_items_modifier?outlet=<?= $selected_outlet ?>&ModifierID=" + ModifierID + "&DeviceNo=" + DeviceNo + "",
                method: "GET",
                dataType: "JSON",
                success: function(response) {
                    return response.total;
                }
            })
        }

        // Validation
        $('#formAddModifier').bootstrapValidator({
            message: 'Form Tambah Pilihan Ekstra harus diisi dengan benar',
            fields: {
                ModifierName: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Pilihan Ekstra tidak boleh kosong.'
                        },
                        remote: {
                            onkeyup: false,
                            message: 'Nama Pilihan Ekstra sudah ada.',
                            url: "<?= base_url() ?>extra/ajax_validation_exist",
                            delay: 100,
                            type: "post",
                            data: {
                                Outlet: "<?= $selected_outlet ?>",
                                field: "ModifierName",
                                value: function() {
                                    return $('#formAddModifier').find('input[name="ModifierName"]').val();
                                }
                            }
                        }
                    }
                },
                'ChoiceName[]': {
                    validators: {
                        notEmpty: {
                            message: 'Detail Pilihan Ekstra harus diisi'
                        }
                    }
                }
            }
        });
        $('#formEditModifier').bootstrapValidator({
            message: 'Form Edit Pilihan Ekstra harus diisi dengan benar',
            fields: {
                ModifierName: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Pilihan Ekstra tidak boleh kosong.'
                        },
                        remote: {
                            onkeyup: false,
                            message: 'Nama Pilihan Ekstra sudah ada.',
                            url: "<?= base_url() ?>extra/ajax_validation_exist",
                            delay: 100,
                            type: "post",
                            data: {
                                Outlet: "<?= $selected_outlet ?>",
                                field: "ModifierName",
                                value: function() {
                                    return $('#formEditModifier').find('input[name="ModifierName"]').val();
                                },
                                id: function() {
                                    return $('#formEditModifier').find('input[name="ModifierID"]').val();
                                }
                            }
                        }
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
                window.location = '<?= base_url('extra/itemform?outlet='); ?>' + selected_outlet;
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