<script type="text/javascript">
    var $ = jQuery.noConflict();
    init_function();

    // populate items
    var items;
    $.post("<?=base_url() . 'ajax/getitembyoutlet2';?>", {
        "o": <?= $selected_outlet  ?>,
    }, function(data) {
        items = JSON.parse(data);
        $("#main-content").show();
        $("#loading-content").hide();
    });

    function get_item_by_id(id) {
        var item_data;
        $.each(items, function(key, item) {
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
        var tr_number = 1;
        $("#tambah-item").click(function() {
            row_item = '<tr data-id="' + (tr_number += 1) + '">' + template_tr + '</tr>';
            row_item = row_item.replace("pilihitem1", "pilihitem" + (tr_number));
            $("#dynamic-table").append(row_item);
            //$(row_item)'select.form-control'
            $("#pilihitem" + (tr_number)).select2();
            table_event();
        });
    }

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

    function table_event() {
        // delete item
        $("#dynamic-table tr").each(function() {
            $(this).find('.hapus-item').click(function() {
                $(this).closest('tr').remove();
            });
        });

        //on selected item
        $('#dynamic-table select[name="item[]"]').change(function() {
            var id = $(this).closest('tr').attr('data-id');
            var item_single = get_item_by_id($(this).val());
            $("#dynamic-table tr[data-id='" + id + "']").find('input[name="qty-sistem[]"]').val(item_single.SistemQty);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-sistem').text(item_single.Unit);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-aktual').text(item_single.Unit);
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-selisih').text(item_single.Unit);
            hitung_selisih(id);
        });

        //on change qty aktual
        $('#dynamic-table input[name="qty-aktual[]"]').change(function() {
            var id = $(this).closest('tr').attr('data-id');
            hitung_selisih(id);
        });
    }
    
    $('.form-store').submit(function() {
        $(this).find(':submit').prop('disabled', true);
    });

    <?php if (isset($_SESSION['notif'])): ?>
    alert("<?= $_SESSION['notif'] ?>");
    window.location.href = "<?php echo base_url(); ?>koreksistok?outlet=<?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>";
    <?php endif ?>
</script>

