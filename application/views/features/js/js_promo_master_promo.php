<script type="text/javascript">
    var xx;
    jQuery(document).ready(function ($) {
        xx = $;
        $('#outlet').change(function () {
            var val = $(this).val();

            window.location = '<?=base_url('promo/listpromo?outlet=');?>' + val;

        });
    });
    function redirectTonewPromo() {
        var selected_outlet = xx('#outlet').val();
        if (selected_outlet == -999) {
            alert('Pilih outlet terlebih dahulu.');
        } else {
            window.location = '<?=base_url('promo/promoform?outlet=');?>' + selected_outlet;
        }

    }
</script>
