<script type="text/javascript">
    /*
    ############################################################################################################
    START CRUD SUPPLIER
    ############################################################################################################
    */
    var data_supplier;
    var id_suppp;
    var nama_supplier_info = '';

    $("select[name='supplier']").change(function(){
        id_suppp = $(this).val();

        if (id_suppp) {
            selected_supplier(id_suppp);
        } else {
            $("#edit_supplier, #hapus_supplier").attr('disabled','disabled');
        }
    });

    function load_supplier(id=null){
        $("select[name='supplier']").html('<option></option>');
            $.post('<?=base_url('ajax/get_supplier_outlet');?>', {
                idoutlet: <?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>
            }, function (data) {
                var jsonData = JSON.parse(data);
                jsonData.forEach(function(item) {
                    var selected_item='';
                    if (item.SupplierID+'.'+item.DeviceNo==id) {
                        selected_item = 'selected';
                    }
                    $("select[name='supplier']").append('<option value="'+item.SupplierID+'.'+item.DeviceNo+'" '+selected_item+'>'+item.Nama+'</option>');
                });
            });
        $("textarea[id='alamat_supplier']").val("");
        if (id) {selected_supplier(id);}
    }

    function selected_supplier(id_suppp){
            console.log(id_suppp);
            $.post('<?=base_url('ajax/get_supplier');?>', {
                id_supp: id_suppp,
                idoutlet: <?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>
            }, function (data) {
                var jsonData = JSON.parse(data);
                data_supplier = jsonData;
                $("textarea[id='alamat_supplier']").val(jsonData.SupplierAddress);
                nama_supplier_info = jsonData.SupplierName;
                if (jsonData.SupplierID) {
                    //enable button
                    $("#edit_supplier, #hapus_supplier").removeAttr('disabled');
                } else{
                    $("#edit_supplier, #hapus_supplier").attr('disabled','disabled');
                }
            });
    }


    var mode_name = '';

    $("#tambah_supplier").click(function(){
        mode_name = 'new';
        $(".modal-title").text("Tambah Supplier");
        $("input[name='supplier_name'], textarea[name='supplier_alamat'], input[name='supplier_telepon'], input[name='supplier_email'], textarea[name='supplier_catatan']").val("");
        $('#modal_supplier').modal('show');
    });

    $("#edit_supplier").click(function(){
        mode_name = 'edit';
        $(".modal-title").text("Edit Supplier");
        $('#modal_supplier').modal('show');
        if (data_supplier) {
            $("input[name='supplier_name']").val(data_supplier.SupplierName);
            $("textarea[name='supplier_alamat']").val(data_supplier.SupplierAddress);
            $("input[name='supplier_telepon']").val(data_supplier.SupplierPhone);
            $("input[name='supplier_email']").val(data_supplier.SupplierEmail);
            $("textarea[name='supplier_catatan']").val(data_supplier.Note);
        }
    });

    $("#hapus_supplier").click(function(){

        var idno = id_suppp ? id_suppp.split('.') : '';

        var confirm_info = confirm('Hapus Supplier: '+nama_supplier_info+' ?');
        if (confirm_info) {
            $.post('<?=base_url('ajax/delete_supplier');?>', {
                    id: idno[0],
                    devno: idno[1],
                    outlet: <?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>
            }, function (data) {
                var jsonData = JSON.parse(data);
                if (jsonData.status=="OK") {
                    alert('Supplier Berhasil Dihapus');
                    load_supplier();
                }
            });
        }
    });


    $("#simpan_supplier").click(function(){

        var idno = id_suppp ? id_suppp.split('.') : '';

        var supplier_name = $("input[name='supplier_name']").val();
        var supplier_alamat = $("textarea[name='supplier_alamat']").val();
        var supplier_telepon = $("input[name='supplier_telepon']").val();
        var supplier_email = $("input[name='supplier_email']").val();
        var supplier_catatan = $("textarea[name='supplier_catatan']").val();

        $.post('<?=base_url('ajax/savesupplier');?>', {
                nama: supplier_name,
                alamat: supplier_alamat,
                telepon: supplier_telepon,
                email: supplier_email,
                catatan: supplier_catatan,
                mode: mode_name,
                id_supp: idno[0],
                devno : idno[1],
                idoutlet: <?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>
        }, function (data) {
            var jsonData = JSON.parse(data);

            if (jsonData.status == "OK") {
                alert('Data Berhasil Disimpan');
                $('#modal_supplier').modal('hide');
                load_supplier(id_suppp);
            } else {
                alert('Data Gagal Disimpan');
            }

        });

    });
     /*
    ############################################################################################################
    END CRUD SUPPLIER
    ############################################################################################################
    */
</script>
