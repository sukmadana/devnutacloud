<script src="https://cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
<script src="//cdn.datatables.net/responsive/1.0.7/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/tabletools/2.2.4/js/dataTables.tableTools.min.js"></script>

<script type="text/javascript">
    var $ = jQuery.noConflict();

    $(document).ready(function() {
        var table = $('#grid-item').DataTable({
            bPaginate: false,
            "iDisplayLength": -1,
            "oLanguage": {
                "sLengthMenu": ''
            },
            bSort: false,
            "dom": '<"row" <"col-md-12"<"td-content"rt>>>',
            responsive: false,
        });
        $('#search-item').keyup(function () {
            table.search(this.value).draw();
        });
        
        $("#datestart, #dateend").click(function(){
            $("#datestart, #dateend").change(function(){
                $(".box-widget").html('<div class="alert alert-info">Loading Data....</div>');
                $("#filter-date").submit();
            });
        });
    });


    function getDataOutlet() {
        var outlet = document.getElementById('outlet');
        return outlet.options[outlet.selectedIndex].value;
    }

    function validation() {
        <?php if($visibilityMenu['PurchaseAdd'])
        {   ?>
        var selected = getDataOutlet();
        if (!selected)
            return alert('Pilih outlet terlebih dahulu.');
        <?php if(($options->CreatedVersionCode>=98 || $options->EditedVersionCode>=98)) { ?>
        return document.getElementById('form-add').submit();
        <?php }
        else { ?>
        alert('Aplikasi Nuta di tablet Anda masih versi lama. Silakan update nuta dari playstore.');
        <?php } ?>
        <?php
        } else { ?>
        alert('Anda tidak memiliki hak akses untuk menambah Pembelian.');
        <?php
        } ?>
    }

    function selectOutlet() {
        var selected = getDataOutlet();
        if (!selected)
            return false;
        return location.href = base_url + "journal?outlet=" + selected;
    }

    function deletingJournal(id, outlet, number) {
        swal({
            title: "KONFIRMASI HAPUS",
            text: 'Anda akan menghapus data jurnal dengan nomor transaksi <b>' + number + '</b>. Anda yakin?',
            icon: "warning",
            showCancelButton: true,
            showConfirmButton: true,
            confirmButtonText: "Hapus!",
            cancelButtonText: "Batal",
            confirmButtonColor: "#e649",
            showLoaderOnConfirm: true,
            closeOnConfirm: false,
            closeOnCancel: true,
            dangerMode: true,
            html: true,

        }, function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: '<?= base_url().'journal/deleteJournal'; ?>',
                    type: 'post',
                    data: {id:id,outlet:outlet},
                    dataType: 'json',
                    success: function(data) {
                        swal.close();
                        location.reload();
                    },
                    error: function() {
                        alert('Failed to deleting data journal.');
                    }
                });
            }
        });
    }

</script>