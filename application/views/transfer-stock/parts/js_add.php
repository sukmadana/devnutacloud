<script type="text/javascript">
    var $ = jQuery.noConflict();
    init_function();


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
            if (item.ItemID == id) {
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
        $("#tambah-item").click(function () {
            row_item = '<tr data-id="' + (tr_number += 1) + '">' + template_tr + '</tr>';
            $("#dynamic-table").append(row_item);
            table_event();
        });
    }

    

    function table_event() {
        // delete item
        $("#dynamic-table tr").each(function () {
            $(this).find('.hapus-item').click(function () {
                $(this).closest('tr').remove();
            });
        });

        //on selected item
        $('#dynamic-table select[name="item-name[]"]').change(function () {
            var id = $(this).closest('tr').attr('data-id');
            var item_single = get_item_by_id($(this).val());
            $("#dynamic-table tr[data-id='" + id + "']").find('.satuan-sistem').text(item_single.Unit);
        });

        
    }

    $("select[name='to_outlet']").change(function(){
        window.location.href = "<?php echo base_url(); ?>transferstok/form?outlet=<?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>&ds=<?= $date_start ?>&de=<?= $date_end ?>&outlet_tujuan="+$(this).val();
        $(".widget-block").html("<center>Loading...</center>");
    });

    $("select[name='outlet']").change(function(){
        $(".widget-block").html("<center>Loading...</center>");
    });

    <?php if (isset($_SESSION['notif'])): ?>
    alert("<?= $_SESSION['notif'] ?>");
    window.location.href = "<?php echo base_url(); ?>transferstok?outlet=<?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>";
    <?php endif ?>
</script>

