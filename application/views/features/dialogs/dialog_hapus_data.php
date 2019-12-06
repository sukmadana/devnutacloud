<script type="text/javascript">

    function deleteDataFunction() {
        event.preventDefault();
        var form = event.target.form;
        swal({
                title: "Apakah anda yakin?",
                text: "Hapus data sampai dengan tanggal <?= formatdateindonesia($date_end); ?> \n dari outlet <?= $OutletInfo->NamaOutlet; ?>",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, Hapus data selamanya!",
                cancelButtonText: "Tidak"
            },
            function(isConfirm) {
                if (isConfirm) {
                    form.submit();
                } else {
                    swal("Dibatalkan", "Data batal di hapus", "error");
                }
            });
    }
</script>
