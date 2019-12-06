<script src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/responsive/1.0.7/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/tabletools/2.2.4/js/dataTables.tableTools.min.js"></script>

<script type="text/javascript">
    window.$ = jQuery;
    jQuery(document).ready(function ($) {
        var table = $('#grid-item').DataTable({
            bPaginate: false,
            "iDisplayLength": -1,
            "oLanguage": {
                "sLengthMenu": ''
            },
            bSort: false,
            "dom": '<"row" <"col-md-12"<"td-content"rt>>>',
            responsive: false,
        });
        $('#search-item').keyup(function () {
            table.search(this.value).draw();
        });
    });

    jQuery('.delete-button').bind('click', function () {
        var dialog = confirm($(this).data('message'));

        if (dialog == true) {
            jQuery($(this).data('target')).submit();
            $(".box-widget").hide().after('<div class="alert">Deleting...</div>');
        }
    });

    function getDataOutlet() {
        var outlet = document.getElementById('outlet');
        return outlet.options[outlet.selectedIndex].value;
    }

    function validation() {
        <?php if($visibilityMenu['StockAdd'])
        {   ?>
        var selected = getDataOutlet();
        if (!selected)
            return alert('Pilih outlet terlebih dahulu.');
        <?php if(($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98)) { ?>
        return document.getElementById('form-add').submit();
        <?php }
        else { ?>
        alert('Aplikasi Nuta di tablet Anda masih versi lama. Silakan update nuta dari playstore.');
        <?php } ?>
        <?php
        } else { ?>
        alert('Anda tidak memiliki hak akses untuk menambah Stok Masuk.');
        <?php
        } ?>
    }

    function selectOutlet() {
        var selected = getDataOutlet();
        if (!selected)
            return false;

        return location.href = base_url + "stokmasuk?outlet=" + selected;
    }

    $("#datestart, #dateend").click(function () {
        $("#datestart, #dateend").change(function () {
            $(".box-widget").html('<div class="alert alert-info">Loading Data....</div>');
            $("#filter-date").submit();
        });
    });

</script>