<script type="text/javascript">

	window.$ = jQuery;
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

		return document.getElementById('uang-add').submit();
	}

	function selectOutlet() {
		var selected = getDataOutlet();
		if (!selected)
			return false;

		return location.href = base_url+"uangmasuk/index?outlet="+selected;
	}
</script>