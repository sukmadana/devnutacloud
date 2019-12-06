<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 17/05/2016
 * Time: 11:21
 */
?>
<script type="text/javascript">
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
    });


</script>
