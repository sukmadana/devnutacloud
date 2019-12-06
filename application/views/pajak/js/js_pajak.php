<script type="text/javascript" src="/js/jquery.inputmask.bundle.js"></script>
<script type="text/javascript" src="/js/bootstrap-select.min.js"></script>
<script type="text/javascript">
var xx;
jQuery(document).ready(function($) {

    var outletTable = $('#tax-table').removeAttr('width').DataTable({
        paging: false,
        responsive: false,
        scrollX: true,
        language: {
            lengthMenu: '_MENU_',
            search: '',
            searchPlaceholder: 'Cari'
        },
        dom: "<'row mb-20'<'col-sm-4'l><'col-sm-8 text-right' <'dataTable-col' f> <'dataTable-col'B> <'dataTable-col' <'addbutton'>>>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'><'col-sm-7'p>>",
        columnDefs: [
            { className: "outlet_class", targets: "_all" },
            { targets: 0, orderable: true, search: false},
            { targets: 1, orderable: false, search: false},
            { targets: 2, orderable: false, search: false},
            { targets: 3, orderable: false, search: false},
            { targets: 4, orderable: false, search: false},
            {
                render: function (data, type, full, meta) {
                    return "<div class='text-wrap width-150'>" + data + "</div>";
                },
                targets: '_all'
            },
        ],
        order: [[ 0, 'asc' ]],
        createdRow: function ( row, data, index ) {
            $('td', row).eq(4).addClass('datatable-col-action');
        }
    });

    $('.dataTables_filter input[type="search"]').css(
        {'font-size':'14px'}
    );

    function myFunction() {
        // Declare variables
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById('myInput');
        filter = input.value.toUpperCase();
        ul = document.getElementById("myUL");
        li = ul.getElementsByTagName('li');

        // Loop through all list items, and hide those who don't match the search query
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }

    $('input[type="checkbox"]').change(function(e) {
        var checked = $(this).prop("checked"),
            container = $(this).parent(),
            siblings = container.siblings();

        container.find('input[type="checkbox"]').prop({
            indeterminate: false,
            checked: checked
        });

        function checkSiblings(el) {

            var parent = el.parent().parent(),
                all = true;

            el.siblings().each(function() {
                let returnValue = all = ($(this).children('input[type="checkbox"]').prop("checked") === checked);
                return returnValue;
            });


            if (all && checked) {
                parent.children('input[type="checkbox"]').prop({
                    indeterminate: false,
                    checked: false
                });

                checkSiblings(parent);

            } else if (all && !checked) {
                parent.children('input[type="checkbox"]').prop("checked", checked);
                parent.children('input[type="checkbox"]').prop("indeterminate", (parent.find('input[type="checkbox"]:checked').length > 0));
                checkSiblings(parent);

            } else {
                el.parents("li").children('input[type="checkbox"]').prop({
                    indeterminate: true,
                    checked: false
                });

            }

        }

        checkSiblings(container);
    });

    $('#outlet').change(function() {
        var val = $(this).val();

        window.location = '<?= base_url('pajak/index?outlet='); ?>'+val;

    });

    $('select').selectpicker({
        liveSearch: true
    });


    // Modal Add Diskon
    $('#modalAddTax').on('click', 'input[name="r_percent"]', function() {
        $('#formAddTax').find('input[name="TaxPercent"]').val($(this).val());
        var taxExist = $('#formAddTax').find('input[name="TaxPercent"]').val();

        if ($(this).val() == 'yes') {
            $('#formAddTax').find('input[name="TaxPercent"]').inputmask('Regex', {
                regex: "[0-9,%]+",
                showMaskOnFocus: false,
                showMaskOnHover: false,
                clearIncomplete: true
            });

            $('#formAddTax').find('input[name="TaxPercent"]').val(taxExist + '%');
        } else {
            $('#formAddTax').find('input[name="TaxPercent"]').inputmask('Regex', {
                regex: "[0-9,]+",
                showMaskOnFocus: false,
                showMaskOnHover: false,
                clearIncomplete: true
            });
        }
    });

    $('#modalAddTax').on('click', '#btnSimpan', function() {
        var $this = $(this);
        var validator = $('#formAddTax').data('bootstrapValidator');
        validator.validate();
        var discountLength = $('#formAddTax').find('input[name="TaxPercent"]').val().length;
        if (validator.isValid() && discountLength > 0) {
            jQuery.ajax({
                method: "POST",
                url: "<?= base_url() ?>pajak/ajax_insert_tax",
                data: {
                    Outlet: "<?= $selected_outlet ?>",
                    TaxName: $('#formAddTax').find('input[name="TaxName"]').val(),
                    TaxPercent: $('#formAddTax').find('input[name="TaxPercent"]').val(),
                    PriceIncludeTax: $('#formAddTax').find('input[name="PriceIncludeTax"]:checked').val(),
                    ApplyToAllItems: $('#formAddTax').find('input[name="ApplyToAllItems"]').val(),
                    ApplicableCategories: $("#formAddTax input[name='ApplicableCategories[]']:checked").map(function() {
                        return $(this).val();
                    }).get(),
                    ApplicableItems: $("#formAddTax input[name='ApplicableItems[]']:checked").map(function() {
                        return $(this).val();
                    }).get(),
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
                        $('#modalAddTax').find('.alert-fixed-danger').hide();
                        $('#modalAddTax').find('.alert-fixed-success .alert-success').text(response.message);
                        $('#modalAddTax').find('.alert-fixed-success').show();

                        $('#modalAddTax').find('input[type="text"]').val('');
                        $('#modalAddTax').find('input[type="hidden"]').val('');

                        setTimeout(function() {
                            $('#modalAddTax').find('.alert-fixed-success').hide();
                            $('#modalAddTax').modal('hide');
                            window.location = '<?= base_url('pajak/index?outlet='); ?><?= $selected_outlet ?>';
                        }, 1000);
                    } else {
                        $('#modalAddTax').find('.alert-fixed-success').hide();
                        $('#modalAddTax').find('.alert-fixed-danger .alert-danger').text(response.message);
                        $('#modalAddTax').find('.alert-fixed-danger').show();
                        window.location = '<?= base_url('pajak/index?outlet='); ?><?= $selected_outlet ?>';
                    }
                }
            });
        }
    });

    $('#modalAddTax').on('show.bs.modal', function (event) {
        $('input:checkbox').removeAttr('checked');
        $('input#ApplyToAllItems').siblings().remove();
        new Switchery($('input#ApplyToAllItems')[0], { size: 'small' });
        $("#ApplyToAllItems").trigger("click");
        $("#categoryItemGroup").hide();
    })

    // Hapus Pajak

    $('#btnDelete').on('click', function() {
        var $this = $(this);
        $.ajax({
            method: "POST",
            url: "<?= base_url() . 'pajak/ajax_delete_tax'; ?>",
            data: {
                TaxID: $('#modalHapus').find('input[name="TaxID"]').val(),
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
                        window.location = '<?= base_url('pajak/index?outlet='); ?><?= $selected_outlet ?>';
                    }, 1000);
                } else {
                    $('#alert-update-delete .alert-success').hide();
                    $('#alert-update-delete').find('.alert-danger').html('<p>' + response.message + '</p>');
                    $('#alert-update-delete, #alert-update-delete .alert-danger').show();
                    setTimeout(function() {
                        $('#alert-update-delete, #alert-update-delete .alert-danger').hide();
                        window.location = '<?= base_url('pajak/index?outlet='); ?><?= $selected_outlet ?>';
                    }, 1000);
                }
            }
        })
    });


    $('#modalEditTax').on('show.bs.modal', function (event) {
        document.getElementById("formEditTax").reset();
        var button = $(event.relatedTarget) 
        var TaxID = button.data('tax-id');
        var TaxName = button.data('tax-name');
        var TaxPercent = button.data('tax-percent');
        var PriceIncludeTax = button.data('price-include-tax');
        var ApplyToAllItems = button.data('apply-to-all-items');
        var ApplicableCategories = button.data('applicable-categories').toString();
        var ApplicableCategoriesArray = ApplicableCategories.split(',');

        $('input:checkbox').removeAttr('checked');
        $(this).val('check all'); 
        ApplicableCategoriesArray.forEach(function(entry) {
            var replaced = entry.substring(entry.indexOf(".") + 1);
            $("#category-"+replaced).prop("checked", true);
        });

        var ApplicableItems = button.data('applicable-items').toString();
        var ApplicableItemsArray = ApplicableItems.split(',');
        ApplicableItemsArray.forEach(function(entry) {
            var replaced = entry.substring(entry.indexOf(".") + 1);
            $("#item-"+replaced).prop("checked", true);
        });
        $('input#ApplyToAllItemsEdit').siblings().remove();
        new Switchery($('input#ApplyToAllItemsEdit')[0], { size: 'small' });
        $("#ApplyToAllItemsValEdit").val('off');
        $("#categoryItemGroupEdit").show();
        $("#modalEditTax input[name='TaxID']").val(TaxID);
        $("#modalEditTax input[name='TaxName']").val(TaxName);
        $("#modalEditTax input[name='TaxPercent']").val(TaxPercent);
        if (PriceIncludeTax == "0") {
            $("#PriceIncludeTax0").prop("checked", true);
        } else {
            $("#PriceIncludeTax1").prop("checked", true);
        }
        if (ApplyToAllItems == "1") {
            var switchery = $("#ApplyToAllItemsValEdit").val();
            if (switchery == 'off') {
                $("#ApplyToAllItemsEdit").trigger("click");
            }
        }

    })

    $('#modalEditTax').on('click', '#btnSimpanEdit', function() {
        var $this = $(this);
        var validator = $('#formEditTax').data('bootstrapValidator');
        validator.validate();
        var discountLength = $('#formEditTax').find('input[name="TaxPercent"]').val().length;
        if (validator.isValid() && discountLength > 0) {
            jQuery.ajax({
                method: "POST",
                url: "<?= base_url() ?>pajak/ajax_update_tax",
                data: {
                    Outlet: "<?= $selected_outlet ?>",
                    TaxID: $('#formEditTax').find('input[name="TaxID"]').val(),
                    TaxName: $('#formEditTax').find('input[name="TaxName"]').val(),
                    TaxPercent: $('#formEditTax').find('input[name="TaxPercent"]').val(),
                    PriceIncludeTax: $('#formEditTax').find('input[name="PriceIncludeTax"]:checked').val(),
                    ApplyToAllItems: $('#formEditTax').find('input[name="ApplyToAllItems"]').val(),
                    ApplicableCategories: $("#formEditTax input[name='ApplicableCategories[]']:checked").map(function() {
                        return $(this).val();
                    }).get(),
                    ApplicableItems: $("#formEditTax input[name='ApplicableItems[]']:checked").map(function() {
                        return $(this).val();
                    }).get(),
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
                        $('#modalEditTax').find('.alert-fixed-danger').hide();
                        $('#modalEditTax').find('.alert-fixed-success .alert-success').text(response.message);
                        $('#modalEditTax').find('.alert-fixed-success').show();

                        $('#modalEditTax').find('input[type="text"]').val('');
                        $('#modalEditTax').find('input[type="hidden"]').val('');

                        setTimeout(function() {
                            $('#modalEditTax').find('.alert-fixed-success').hide();
                            $('#modalEditTax').modal('hide');
                            window.location = '<?= base_url('pajak/index?outlet='); ?><?= $selected_outlet ?>';
                        }, 1000);
                    } else {
                        $('#modalEditTax').find('.alert-fixed-success').hide();
                        $('#modalEditTax').find('.alert-fixed-danger .alert-danger').text(response.message);
                        $('#modalEditTax').find('.alert-fixed-danger').show();
                        window.location = '<?= base_url('pajak/index?outlet='); ?><?= $selected_outlet ?>';
                    }
                }
            });
        }
    });

    // Validation
    $('#formAddTax').bootstrapValidator({
        message: 'Form Tambah Pajak harus diisi dengan benar',
        fields: {
            TaxName: {
                validators: {
                    notEmpty: {
                        message: 'Nama Pajak tidak boleh kosong.'
                    },
                }
            },
            TaxPercent: {
                validators: {
                    integer: {
                        message: 'Pajak harus angka.'
                    },
                }
            },
            PriceIncludeTax: {
                validators: {
                    notEmpty: {
                        message: 'Harga Jual tidak boleh kosong.'
                    },
                }
            },
        }
    });

    $('#formEditTax').bootstrapValidator({
        message: 'Form Tambah Pajak harus diisi dengan benar',
        fields: {
            TaxName: {
                validators: {
                    notEmpty: {
                        message: 'Nama Pajak tidak boleh kosong.'
                    },
                }
            },
            TaxPercent: {
                validators: {
                    integer: {
                        message: 'Pajak harus angka.'
                    },
                }
            },
            PriceIncludeTax: {
                validators: {
                    notEmpty: {
                        message: 'Harga Jual tidak boleh kosong.'
                    },
                }
            },
        }
    });
});

function showDeleteModal(data) {
    var $this = jQuery(data);
    var TaxID = $this.data('tax-id');
    jQuery('#modalHapus').find('input[name="TaxID"]').val(TaxID);
    jQuery('#modalHapus').modal('show');
}

var labels = jQuery('#myUL label');

jQuery('#filter').keyup(function() {
    var valThis = jQuery(this).val().toLowerCase();

    if (valThis == "") {
        labels.parent().show();
    } else {
        labels.each(function() {
            var label = jQuery(this);
            var text = label.text().toLowerCase();
            if (text.indexOf(valThis) > -1) {
                label.parents('li').show()
            } else {
                label.parent().hide();
            }
        });
    };
});

jQuery('#filterEdit').keyup(function() {
    var valThis = jQuery(this).val().toLowerCase();

    if (valThis == "") {
        labels.parent().show();
    } else {
        labels.each(function() {
            var label = jQuery(this);
            var text = label.text().toLowerCase();
            if (text.indexOf(valThis) > -1) {
                label.parents('li').show()
            } else {
                label.parent().hide();
            }
        });
    };
});

document.getElementById('ApplyToAllItems').addEventListener('change', (e) => {
    this.checkboxValue = e.target.checked ? 'on' : 'off';
    jQuery('#ApplyToAllItemsVal').val(this.checkboxValue)
    if (this.checkboxValue == 'on') {
        jQuery("#categoryItemGroup").hide();
    } else {
        jQuery("#categoryItemGroup").show();
    }
})


document.getElementById('ApplyToAllItemsEdit').addEventListener('change', (e) => {
    this.checkboxValue = e.target.checked ? 'on' : 'off';
    jQuery('#ApplyToAllItemsValEdit').val(this.checkboxValue)
    if (this.checkboxValue == 'on') {
        jQuery("#categoryItemGroupEdit").hide();
    } else {
        jQuery("#categoryItemGroupEdit").show();
    }
})

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