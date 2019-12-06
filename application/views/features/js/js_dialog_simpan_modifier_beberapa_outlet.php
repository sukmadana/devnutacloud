<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 19:49
 */
?>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var simpanItemModal;
        var selectedKategoriToBeDeleted;
        $('#simpan-item-modal').on('show.bs.modal', function (event) {
            simpanItemModal = $(this);
        });

        $('#btn-simpan-item-modal').click(function (e) {
            $(this).addClass('active');
            $(this).prop('disabled', true);
            var selectedOutlets = $('.all-simpan-item-item:checked');
            var idoutletsSaveItem = [];

            for (var a = 0; a < selectedOutlets.length; a++) {
                var outlet = selectedOutlets[a];
                var tag = $(outlet).data('tag').split('#@#');
                var idoutlet = tag[0];
                var nama = tag[1];
                idoutletsSaveItem[a] = idoutlet;
            }
            var namaekstra = $("#namaEkstra").val()
            var bahan = []
            var modifier = []
            var length = $(".masterbahan").length
            var lengthmodifier = document.getElementsByClassName("item-pilihan-ekstra").length
            if (lengthmodifier > 0) {
                for (var iii = 0; iii < lengthmodifier; iii++) {
                    var mod = {}
                    var item = document.getElementsByClassName("item-pilihan-ekstra")[iii]
                    mod.name = item.getElementsByTagName('input')[0].value
                    mod.harga = item.getElementsByTagName('input')[1].value
                    modifier[iii + 1] = mod
                }
            }

            for (var i = 1; i < length; i++) {
                var bahansingle = document.getElementsByClassName("masterbahan")[i]
                var lengthTr = bahansingle.getElementsByClassName("itembahan").length
                var idd = bahansingle.getElementsByClassName("tblbahan")[0]
                idd = $(idd).attr('id')
                idd = idd.replace("grid-tambah-bahan", "")
                if (lengthTr > 0) {
                    var element = []
                    for (var ii = 0; ii < lengthTr; ii++) {
                        var elm = {}
                        var ini = bahansingle.getElementsByClassName("itembahan")[ii]
                        var nama = ini.getElementsByTagName("input")[0].value
                        var qtynya = ini.getElementsByTagName("input")[1].value
                        var satuannya = ini.getElementsByTagName("input")[2].value
                        var hargabeli = ini.getElementsByTagName("input")[3].value
                        elm.name = nama
                        elm.qty = qtynya
                        elm.satuan = satuannya
                        elm.hargabeli = hargabeli
                        element.push(elm)
                    }
                    bahan[idd] = element
                }

            }

            var outlet = '<?=$_GET["outlet"];?>'
            var data = {nama: namaekstra, pilihan : modifier, bahan: bahan, outlet: idoutletsSaveItem, delete: deletedata, cekpilihsatu : $('#checkPilihSatu').val()}

            $.ajax({
                url: '<?=base_url('ajax/saveextra');?>',
                method: 'POST',
                data: data,
                success: function (data) {
                    data = JSON.parse(data)
                    if (data.status === true) {
                        window.location.href = '<?=base_url('extra/index');?>'
                    }
                }

            })
        });
        $('#all-simpan-item-selected').change(function () {
            var selected = $(this).prop('checked');

            $('.all-simpan-item-item').prop('checked', selected);


        });
    });
</script>
