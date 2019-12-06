<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="/js/datatables.custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript">
$(document).ready(function(){

  var dataTable = $('#grid-item').DataTable({
    "processing":true,
    "serverSide":true,
    "order":[],
    "lengthMenu": [[10, 25, 50], [10, 25, 50]],
    "ajax":{
      url: window.base_url + 'perusahaan/ajaxusercloud',
      type:"POST"
    },
    "columnDefs":[
      {
        "targets":[0, 6],  // sesuaikan order table dengan jumlah column
        "orderable":false,
      },
    ],
  });

  $('.dataTables_filter, .dataTables_length').hide();

  $('#length_change').val(dataTable.page.len());
  $('#length_change').change( function() {
    dataTable.page.len( $(this).val() ).draw();
});

  $('#searchBox').keyup(function(){
    dataTable.search($(this).val()).draw();
  });
})
</script>
