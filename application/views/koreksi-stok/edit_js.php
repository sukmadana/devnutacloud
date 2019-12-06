<script type="text/javascript">
    var $ = jQuery.noConflict();
    table_event();

    var table = $("#dynamic-table");
    table.find("tr:not(.deleted)").find(".selectpicker").select2();

    // populate items
    var items;
    $.post("<?=base_url() . 'ajax/getitembyoutlet2';?>", {
        "o": <?= $koreksi_stok->DeviceID  ?>,
    }, function (data) {
        items = JSON.parse(data);
        $("#main-content").show();
        $("#loading-content").hide();
    });

    function get_item_by_id(id) {
        var item_data;
        $.each(items, function (key, item) {
            var keyitem = item.ItemID + '.' + item.ItemDeviceNo;
            if (keyitem == id) {
                item_data = item;
            }
        });
        return item_data;
    }

    function table_event() {
        //on change qty aktual
        $('#dynamic-table input[name="qty-aktual[]"]').unbind('change').change(function () {
            var id = $(this).closest('tr').attr('data-id');
            hitung_selisih(id);
        });

        //on selected item
        $('#dynamic-table select[name="item[]"]').unbind('change').change(function () {debugger;
            var id = $(this).closest('tr').attr('data-id');
            var item_single = get_item_by_id($(this).val());
            $("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-sistem[]"]').val(item_single.SistemQty);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-sistem').text(item_single.Unit);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-aktual').text(item_single.Unit);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-selisih').text(item_single.Unit);
            hitung_selisih(id);
        });

        // delete item
        $('#dynamic-table .hapus-item').unbind('click').click(function () {
            var tr = $(this).closest("tr");
            tr.addClass("deleted");
            tr.find("input[name='detail_deleted[]']").val(1);
        });
    }

    var del_id = -1;
    $('#tambah-item').click(function () {
        var tr = table.find("tr[data-id=-1]");

        var newRow = tr.clone(true, true);
        newRow.attr("data-id", --del_id)
        newRow.removeClass("deleted");
        newRow.find("input[name='detail_added[]']").val(1);
        newRow.find(".selectpicker").select2();

        $('#dynamic-table > tbody').append(newRow);
    });

    function hitung_selisih(id) {
        var qty_sistem = parseFloat($("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-sistem[]"]').val());
        var qty_aktual = parseFloat($("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-aktual[]"]').val());
        if (!qty_sistem) {
            qty_sistem = 0;
        }
        if (!qty_aktual) {
            qty_aktual = 0;
        }

        $("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-selisih[]"]').val(qty_aktual - qty_sistem);
    }
</script>