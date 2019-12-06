<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="/js/datatables.custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script type="text/javascript">
    var $ = jQuery.noConflict();

    $(document).ready(function() {
        var table = $('#grid-item').DataTable({
            bPaginate:false,
            "iDisplayLength": -1,
            "oLanguage": {
                "sLengthMenu": ''
            },
            bSort:true,
            "dom": '<"row" <"col-md-12"<"td-content"rt>>>',
            responsive: false,
        });

        $('#search-item').keyup(function () {
            table.search( this.value ).draw();
        });
    });


    function deletingAccount(id, name, dft) {
        
        if (dft == 1) {
            var content = document.createElement('div');
            content.innerHTML = 'Data akun <b>' + name + '</b> tidak dapat dihapus karena menjadi default aplikasi.';
            swal({
                title: 'HAPUS AKUN',
                content: content,
                icon: 'error',
                dangerMode: true
            });
            return;
        }

        var content = document.createElement('div');
        content.innerHTML = 'Anda akan menghapus data akun <b>' + name + '</b>. Anda yakin?';
        swal({
            title: 'KONFIRMASI HAPUS',
            content: content,
            dangerMode: true,
            icon: 'info',
            closeOnClickOutside: false,
            buttons: {
                cancel: {
                    text: "Batal",
                    value: null,
                    visible: true,
                    className: "",
                    closeModal: true,
                },
                confirm: {
                    text: "Hapus",
                    value: true,
                    visible: true,
                    className: "",
                    closeModal: false
                }
            }
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    url: '<?= base_url().'chartofaccount/delete'; ?>',
                    type: 'post',
                    data: {id:id},
                    dataType: 'json',
                    success: function(data) {
                        if (data == true) {
                            window.location = '<?= base_url().'chartofaccount/view'; ?>';
                        }else{
                            swal('GAGAL', 'Akun yang sudah digunakan di jurnal tidak dapat dihapus.', 'error');
                        }
                    },
                    error: function() {
                        swal('FAILED', 'Failed to deleting data akun.', 'error');
                    },
                    complete: function() {
                        swal.close();
                    }
                });
            }
        });
    }

</script>