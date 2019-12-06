<script type="text/javascript">
    var $ = jQuery.noConflict();
    init_function();

    var table = $("#dynamic-table");
    table.find("tr:not(.deleted)").find(".selectpicker").select2();

    // populate items
    var items;
    $.post("<?=base_url() . 'ajax/getitembyoutlet2';?>", {
        "o": <?= $selected_outlet  ?>,
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


    function init_function() {
        table_event();
        var template_tr = $("#dynamic-table tr[data-id='1']").html();

        //add row
        var tr_number = 2;
        $("#tambah-item").click(function () {
            row_item = '<tr data-id="' + tr_number + '">' + template_tr + '</tr>';
            table.append(row_item);

            table.find("tr[data-id='" + tr_number + "'] .selectpicker").select2();
            tr_number++;
            
            table_event();
        });
    }

    function hitung_selisih(id) {
        var qty_sistem = parseFloat($("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-sistem[]"]').val());
        var qty_selisih = parseFloat($("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-selisih[]"]').val());
        if (!qty_sistem) {
            qty_sistem = 0;
        }
        if (!qty_selisih) {
            qty_selisih = 0;
        }

        $("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-aktual[]"]').val(qty_sistem - qty_selisih);
    }

    function table_event() {
        // delete item
        $("#dynamic-table tr").each(function () {
            $(this).find('.hapus-item').click(function () {
                $(this).closest('tr').remove();
            });
        });

        //on selected item
        $('#dynamic-table select[name="item[]"]').change(function () {
            var id = $(this).closest('tr').attr('data-id');
            var item_single = get_item_by_id($(this).val());
            $("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-sistem[]"]').val(item_single.SistemQty);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-sistem').text(item_single.Unit);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-aktual').text(item_single.Unit);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-selisih').text(item_single.Unit);
            hitung_selisih(id);
        });

        //on change qty aktual
        $('#dynamic-table input[name="qty-selisih[]"]').change(function () {
            var id = $(this).closest('tr').attr('data-id');
            hitung_selisih(id);
        });
    }
    
    $('.form-store').submit(function() {
        $(this).find(':submit').prop('disabled', true);
    });

    <?php if (isset($_SESSION['notif'])): ?>
    alert("<?= $_SESSION['notif'] ?>");
    window.location.href = "<?php echo base_url(); ?>stokkeluar?outlet=<?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>";
    <?php endif ?>
</script>

