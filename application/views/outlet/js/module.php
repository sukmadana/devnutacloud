<script type="text/javascript">
function changeSwitchery(element, checked) {
  if ( ( element.is(':checked') && checked == false ) || ( !element.is(':checked') && checked == true ) ) {
    element.parent().find('.switchery').trigger('click');
  }
}
jQuery('#stock-parent').on("change" , function() {
    if ( jQuery('#stock-parent:checked').length > 0) {
        jQuery(".stock-bahan").show();
    } else {
        jQuery(".stock-bahan").hide();
        changeSwitchery(jQuery(".stock-bahan"), false);
    }

    if ( (jQuery('#stock-parent:checked').length > 0) && (jQuery('#stock-variation:checked').length > 0) && (jQuery('#stock-bahan:checked').length > 0) ) {
        jQuery(".stock-extra").show();
    } else {
        jQuery(".stock-extra").hide();
        changeSwitchery(jQuery(".stock-extra"), false);
    }
});

jQuery('#stock-variation').on("change" , function() {
    if ( (jQuery('#stock-parent:checked').length > 0) && (jQuery('#stock-variation:checked').length > 0) && (jQuery('#stock-bahan:checked').length > 0) ) {
        jQuery(".stock-extra").show();
    } else {
        jQuery(".stock-extra").hide();
        changeSwitchery(jQuery(".stock-extra"), false);
    }
});

jQuery('#stock-bahan').on("change" , function() {
    if ( (jQuery('#stock-parent:checked').length > 0) && (jQuery('#stock-variation:checked').length > 0) && (jQuery('#stock-bahan:checked').length > 0) ) {
        jQuery(".stock-extra").show();
    } else {
        jQuery(".stock-extra").hide();
        changeSwitchery(jQuery(".stock-extra"), false);
    }
});

jQuery(document).ready(function () {
    if ( jQuery('#stock-parent:checked').length > 0) {
        jQuery(".stock-bahan").show();
    } else {
        jQuery(".stock-bahan").hide();
        changeSwitchery(jQuery(".stock-bahan"), false);
    }

    if ( (jQuery('#stock-parent:checked').length > 0) && (jQuery('#stock-variation:checked').length > 0) && (jQuery('#stock-bahan:checked').length > 0) ) {
        jQuery(".stock-extra").show();
    } else {
        jQuery(".stock-extra").hide();
        changeSwitchery(jQuery(".stock-extra"), false);
    }
});

</script>