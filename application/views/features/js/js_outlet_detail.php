<script async src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
    function deleteDataFunction() {
        event.preventDefault();
        var form = event.target.form;
        swal({
            title: "Apa kamu yakin ?",
            text: "Data akan terhapus permanen,\n dan kamu tidak bisa mengembalikannya",
            type: "warning",
            reverseButtons: true,
            cancelButtonColor: "#fff",
            cancelButtonText: "Batal",
            confirmButtonColor: "#00ae2b",
            confirmButtonText: "Yakin",
            showCancelButton: true,
            focusConfirm:false
        },function(isConfirm) {
            if (isConfirm) {
                form.submit();
            } else {
                swal("Dibatalkan", "Data batal di hapus", "error");
            }
        })
    }
</script>