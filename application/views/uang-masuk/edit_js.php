<script type="text/javascript">
    var $ = jQuery.noConflict();
    init_function();

    var items;
    $.post("<?=base_url() . 'ajax/getrekeningbyoutlet';?>", {
        "o": <?= $selected_outlet  ?>,
    }, function (data) {
        items = JSON.parse(data);
        $("#main-content").show();
        $("#loading-content").hide();
    });

    function init_function() {
        table_event();
    }

    function table_event() {

    }

    <?php if (isset($_SESSION['notif'])): ?>
    alert("<?= $_SESSION['notif'] ?>");
    window.location.href = "<?php echo base_url(); ?>uangmasuk?outlet=<?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>";
    <?php endif ?>
</script>

