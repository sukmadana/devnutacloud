<script async type="text/javascript">
    jQuery(document).ready(function ($) {
        //o.o
    $.fn.datepicker.dates['ID'] = {
        days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"],
        daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"],
        daysMin: ["Mi", "Sn", "Sl", "Ra", "Ka", "Ju", "Sa"],
        months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
        monthsShort: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"],
        today: "Hari ini",
        clear: "Hapus",
        format: "mm/dd/yyyy",
        titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
        weekStart: 0
        };
    $('#datestart').datepicker({
    orientation: "bottom",
    calendarWeeks: true,
    autoclose: true,
    todayHighlight: true,
    todayBtn: 'linked',
    language: 'ID',
    format: 'd MM yyyy'
    }).on('changeDate', function (e) {
    var date = e.date;
    var day = ('0' + date.getDate()).slice(-2);
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var year = date.getFullYear();

    $('input[name="date_start"]').val(year + '-' + month + '-' + day);
    });

    <?php
    $exDatestart = explode('-', $date_start);
    $tglStart = intval($exDatestart[2]) < 10 ? '0' . $exDatestart[2] : $exDatestart[2];
    $blnStart = intval($exDatestart[1]) - 1;
    $tahunStart = $exDatestart[0];
    ?>
    $('#datestart').datepicker('update', <?='new Date(' . $tahunStart . ',' . $blnStart . ',' . $tglStart . ')';?>);
    })
;
</script>