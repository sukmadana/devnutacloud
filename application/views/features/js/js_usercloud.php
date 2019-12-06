<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="/js/datatables.custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {

        var next_state_input = 'text';
        $('.show-password').on('click', function() {
            $('input[name="password"]').prop('type', next_state_input);
            if (next_state_input == 'text') {
                next_state_input = 'password';
                $('.show-password').html('<i class="fa fa-eye-slash"></i>');
            } else {
                next_state_input = 'text';
                $('.show-password').html('<i class="fa fa-eye"></i>');
            }
        });

        var dataTable = $('#grid-item').DataTable({
            responsive: false,
            scrollX: true,
            dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12'i><'col-sm-12'p>>",
            "ordering": false,
            "info": false,
            "pageLength": 10
        });

        $('.dataTables_filter, .dataTables_length').hide();

        $('#searchBox').keyup(delay(function(e) {
            dataTable.search($(this).val()).draw();
        }, 500));

        $("ul.nav-tabs a").click(function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('.btn-tab-level2').on('click', function() {
            var $this = $(this);
            var tab_id = $this.data('tab-id');

            $('.tab-pane-level2').hide("slow");
            $(tab_id).show("slow");
        });

        $('.switch_access').on('change', function() {
            var $this = $(this);
            var el = '#hidden-' + $this.data('tag');
            if ($this.prop('checked')) {
                $(el).val("on")
            } else {
                $(el).val("off")
            }
        });

        $('#grid-item').on('change', 'tbody tr td .switch_outlet', function() {
            var $this = $(this);
            var el = '#hidden-outlet-' + $this.data('tag');
            if ($this.prop('checked')) {
                $(el).val("on")
            } else {
                $(el).val("off")
            }
        });
    });

    function delay(callback, ms) {
        var timer = 0;
        return function() {
            var context = this,
                args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function() {
                callback.apply(context, args);
            }, ms || 0);
        };
    }
</script>