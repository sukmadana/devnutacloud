<!--suppress ALL -->
<script type="text/javascript">
    var $;
    jQuery(document).ready(function ($) {
        $ = $;

        $('#btn-simpan').click(function (e) {
            $(this).addClass('active');
            $(this).prop('disabled', true);
            var nama = $('#txt-nama').val();
            if (nama.trim() == '') {
                alert('"Nama" tidak boleh kosong');
                $('#btn-simpan').removeClass('active');
                $('#btn-simpan').prop('disabled', false);
                return;
            }
            var email = $('#txt-email').val();
            var telepon = $('#txt-nohp').val();
            var birthday = $('#txt-tgllahir').val();
            var alamat = $('#txt-alamat').val();
            var catatan = $('#txt-catatan').val();

            $.post('<?=base_url('ajaxpelanggan/savesingleoutlet');?>', {
                outlets: '<?=$selected_outlet;?>',
                old_nama: '<?=$form['nama']; ?>',
                nama: nama, 
                alamat: alamat, 
                phone: telepon, 
                email: email, 
                note: catatan,
                birthday: birthday
                }, function (r) {
                    if (r.status === true) {
                        alert('Berhasil Disimpan.');
                        $('#btn-simpan').removeClass('active');
                        window.location = '<?=base_url('pelanggan/daftarpelanggan?outlet='.$selected_outlet);?>';
                    } else {
                        $('#btn-simpan').removeClass('active');
                        $('#btn-simpan').prop('disabled', false);
                        alert('Gagal Disimpan. '  + r.message);
                    }
                }, "json"
            );
        });
    });
</script>
