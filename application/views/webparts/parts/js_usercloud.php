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

	function selectinge() {
        var selected = getDataOutlet();
        if (!selected)
            return location.href = base_url+"perusahaan/usertablet";

        return location.href = base_url+"perusahaan/usertablet?outlet="+selected;
    }
</script>