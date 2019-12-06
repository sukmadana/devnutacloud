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

            var namaitem = $('#txt-item').val();
            if (namaitem.trim() == '') {
                alert('Item tidak boleh kosong.');
                $('#btn-simpan-single-outlet').removeClass('active');
                $('#btn-simpan-single-outlet').prop('disabled', false);
                return;
            }
            var adabahan = $('#is-bahan-penyusun').is(':checked');
            var bahan = [];
            if (adabahan) {
                var jumlahBahan = $('.itembahan').length;
                $('.itembahan').each(function (index, tr) {
                    var inputNamaBahan = tr.children[0].children;
                    var inputQty = tr.children[1].children;
                    var selectSatuan = tr.children[2].children;
                    var hargaBeli = tr.children[3].children; // purchasePrice
                    var namaBahan = $(inputNamaBahan).val();
                    var Qty = $(inputQty).val();
                    var Satuan = $(selectSatuan).val();
                    var hargaBeli = $(hargaBeli).val();
                    <?php if($modeform == 'edit'){?>
                    var idtag = $(inputNamaBahan).data('tag');
                    bahan[index] = {
                        nama: namaBahan,
                        qty: Qty,
                        satuan: Satuan,
                        hargabeli: hargaBeli,
                        id: idtag
                        //satuan: selectSatuan
                    };
                    <?php } else { ?>
                    bahan[index] = {
                        nama: namaBahan,
                        qty: Qty,
                        satuan: Satuan,
                        hargabeli: hargaBeli
                        //satuan: selectSatuan
                    };
                    <?php } ?>

                });
            }
            var mdfs = [];
            $('.nuta-modifier:checked').each(function (index, object) {
                mdfs.push($(object).data('tag'));
            })


            //untuk Save modifier
            var bunchOfPilihanEkstra = [];
            for (var x = 0; x < ListModifierDeleted.length; x++) {
                var _deletedMdf = ListModifierDeleted[x];
                var pilihanEkstra = new PilihanEkstraPostData();
                pilihanEkstra.outlets = idoutletsSaveItem;
                pilihanEkstra.ModifierName = _deletedMdf;
                pilihanEkstra.oldName = _deletedMdf;
                pilihanEkstra.ChooseOnlyOne = false;
                pilihanEkstra.CanAddQuantity = false;
                pilihanEkstra.operation = 'delete';
                pilihanEkstra.Pilihan = [];
                pilihanEkstra.PlaceholderEsktra = pilihanEkstra.ModifierName;
                bunchOfPilihanEkstra.push(pilihanEkstra);
            }
            for (var x = 0; x < listModifier.length; x++) {
                var _newMdf = listModifier[x];
                var op = 'nothing';
                for(var j = 0; j < mdfs.length; j++) {
                    if(mdfs[j] == _newMdf.ModifierName) {
                        op = 'exist';
                        break;
                    }
                }

                if (op != 'nothing') {
                    var pilihanEkstra = new PilihanEkstraPostData();
                    pilihanEkstra.outlets = idoutletsSaveItem;
                    pilihanEkstra.ModifierName = _newMdf.ModifierName;
                    pilihanEkstra.oldName = _newMdf.OldModifierName;
                    pilihanEkstra.ChooseOnlyOne = _newMdf.ChooseOnlyOne;
                    pilihanEkstra.CanAddQuantity = _newMdf.CanAddQuantity;
                    pilihanEkstra.operation = op;
                    pilihanEkstra.Pilihan = _newMdf.Pilihan;
                    pilihanEkstra.PlaceholderEsktra = pilihanEkstra.ModifierName;
                    bunchOfPilihanEkstra.push(pilihanEkstra);
                    console.log(pilihanEkstra);
                } else {
                    console.log('Tidak ada perubahan data di modifiers ' + _newMdf.ModifierName);
                }
            }
            console.log(bunchOfPilihanEkstra);

            //untuk Save Item
            var masterItemPostData = new MasterItemPostData();
            masterItemPostData.selectedoutlet = <?=$selected_outlet;?>;
            masterItemPostData.itemname = namaitem;
            masterItemPostData.oldnamakategori = $('#list-kategori :selected').data('tag');//multi outle dari id berubah ke nama
            masterItemPostData.namakategori = $('#list-kategori :selected').val();//multi outle dari id berubah ke nama
            masterItemPostData.namasatuan = $('#txt-satuan').val();
            masterItemPostData.hargajual = $('#txt-harga-jual').val();
            masterItemPostData.hargabeli = $('#txt-harga-beli').val();
            masterItemPostData.isproduk = $('input[name=jenisproduk]:checked').val();
            masterItemPostData.punyabahan = adabahan;
            masterItemPostData.bahans = bahan;
            masterItemPostData.mode = '<?=$modeform;?>';
            masterItemPostData.olditemname = '<?=addslashes($form['nama item']); ?>';
            masterItemPostData.idoutlets = idoutletsSaveItem;
            masterItemPostData.deletegambar = (window.imageBase64 == '');
            masterItemPostData.modifiers = mdfs;


            goSavingItem(bunchOfPilihanEkstra, masterItemPostData);

        });
        $('#all-simpan-item-selected').change(function () {
            var selected = $(this).prop('checked');

            $('.all-simpan-item-item').prop('checked', selected);


        });
    });
</script>
