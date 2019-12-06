<script>
    jQuery(document).ready(function($) {
        function format(value) {
            return value;
        }

        var outletTable = $('#outlet-table').removeAttr('width').DataTable({
            paging: true,
            responsive: false,
            scrollX: true,
            language: {
                lengthMenu: '_MENU_',
                search: '',
                searchPlaceholder: 'Cari'
            },
            dom: "<'row mb-20'<'col-sm-4'l><'col-sm-8 text-right' <'dataTable-col' f> <'dataTable-col'B> <'dataTable-col' <'addbutton'>>>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",
            buttons: [
                { 
                    extend: 'print', 
                    className: 'btn btn-ghost-white btn-small-size',
                    title: 'DAFTAR OUTLET',
                    exportOptions: {
                        columns: [ 1, 2, 3, 4, 5, 6 ]
                    },
                    customize: function ( win ) {
                        $(win.document.body).find('h1').css({'text-align':'center', 'font-size':'20px'});
                        $(win.document.body).css( 'font-size', '12px' );
                        $(win.document.body).find('th').attr('style', 'padding: 5px !important; font-size: 12px;');
                        $(win.document.body).find('td').attr('style', 'padding: 5px !important; font-size: 12px;');
                    }
                },
            ],
            columnDefs: [
                { className: "outlet_class", targets: "_all" },
                { targets: 0, orderable: false, seearch: false},
                { targets: 7, orderable: false, seearch: false},
                {
                    render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-150'>" + data + "</div>";
                    },
                    targets: '_all'
                },
            ],
            order: [[ 1, 'asc' ]],
            createdRow: function ( row, data, index ) {
                $('td', row).eq(7).addClass('datatable-col-action');
            }
        });

        $('#outlet-table').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = dataTable.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                row.child(format(tr.data('child-value'))).show();
                tr.addClass('shown');
            }
        });

        
        
        $("#outlet-table").DataTable().rows().every(function() {
            var tr = $(this.node());
            this.child(format(tr.data('child-value'))).show();
            tr.addClass('shown');
        });

        $('.dataTables_filter input[type="search"]').css(
            {'font-size':'14px'}
        );

        outletTable.on( 'order.dt search.dt', function () {
            outletTable.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw()

        <?php if ($OutletNew): ?>
            $("div.addbutton").html('<a href="<?= base_url('perusahaan/newoutlet') ?>" class="btn btn-primary">Tambah Outlet</a>');
        <?php endif ?>

        $('#outlet-table thead th').removeClass('outlet_class');
        $("span.outlet-label").parents('td').addClass("outlet-td-child");


    })
</script>