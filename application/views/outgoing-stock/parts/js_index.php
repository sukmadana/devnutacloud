<script type="text/javascript">
    window.$ = jQuery;
    jQuery(document).ready(function ($) {
        var table = $('#grid-item').DataTable({
            bPaginate:false,
            "iDisplayLength": -1,
            "oLanguage": {
                "sLengthMenu": ''
            },
            bSort:false,
            "dom": '<"row" <"col-md-12"<"td-content"rt>>>',
            responsive: false,
        });
        $('#search-item').keyup(function () {
            table.search( this.value ).draw();
        });
        <?php if ($notify):?>
        sendToSocket(NEvent.TRANSFER_STOCKOUT, {source: '<?=$src;?>', outlets: [<?=$selected_outlet;?>]});
        sendToSocket(NEvent.TRANSFER_STOCKOUT_DETAIL, {source: '<?=$src;?>', outlets: [<?=$selected_outlet;?>]});
        <?php endif;?>
    });

    jQuery('.delete-button').bind('click', function(){
        var dialog = confirm($(this).data('message'));

        if (dialog == true) {
            jQuery($(this).data('target')).submit();
        }
    });

    function getDataOutlet() {
        var outlet = document.getElementById('outlet');
        return outlet.options[outlet.selectedIndex].value;
    }

    function validation() {
        var selected = getDataOutlet();
        if (!selected)
            return alert('Pilih outlet terlebih dahulu.');

        return document.getElementById('form-add').submit();
    }

    function selectOutlet() {
        var selected = getDataOutlet();
        if (!selected)
            return false;

        return location.href = base_url+"stokkeluar?outlet="+selected;
    }
</script>