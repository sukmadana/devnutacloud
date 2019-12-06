<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 19:40
 */

?>
<script src="https://ws.nutacloud.com/js/item2018-09-05.js" type="text/javascript"></script>
<script src="<?= base_url('js/nutalibs/variasiharga.js'); ?>" type="text/javascript"></script>
<script type="text/javascript">
	var priceList = [];
    var savedPriceList = <?php echo json_encode($variasi_harga);?>;
    if (savedPriceList.length > 0) {
        priceList = [];
        for (var x = 0; x < savedPriceList.length; x++) {
            var newPrice = new VariasiHarga();
            newPrice.VarianName = savedPriceList[x].VarianName;
            newPrice.SellPrice = savedPriceList[x].SellPrice;
            newPrice.OldVarianName = savedPriceList[x].VarianName;
            priceList.push(newPrice);
        }
    }
    console.log(priceList);
    var $;
    <?php
    echo 'var jsonSatuan=[';
    $strjson = '';
    foreach ($satuans as $k => $v) {
        $strjson .= "{name:'" . $v . "',tag:" . $k . "},";
    }
    echo substr($strjson, 0, strlen($strjson) - 1);
    echo '];';
    ?>
    function hasImage() {
        return window.imageBase64 != '' && window.imageBase64.indexOf('uploads/') == -1 &&
            window.imageBase64.indexOf('no-image-with-text.png') == -1;
    }

    function saveItemFinished(msg) {
        setTimeout(function () {
            alert(msg);
            window.location = '<?=base_url("produk/index?outlet=" . $selected_outlet);?>';
            $('#btn-simpan-single-outlet').removeClass('active');
            $('#btn-simpan-single-outlet').prop('disabled', false);
            $('#btn-simpan-single-outlet').text('Simpan');
            $('#btn-simpan-item-modal').removeClass('active');
            $('#btn-simpan-item-modal').prop('disabled', false);
            $('#btn-simpan-item-modal').text('Simpan');

        }, 1000);

    }

    function isItNewModifier(modifier) {
        console.log(modifier.ModifierName + (modifier.oldName == '' || modifier.ModifierID == -1 ? " is new" : "is exist"));
        return modifier.oldName == '' || modifier.ModifierID == -1;
    }

    function isItModifiedModifier(modifier) {
        var comparedModifier = {};
        for (var x = 0; x < originalListModifier.length; x++) {
            if (originalListModifier[x].OldModifierName == modifier.OldModifierName) {
                comparedModifier = originalListModifier[x];
                break;
            }
        }
        var isPilihanModified = false;
        for (var x = 0; x < comparedModifier.Pilihan.length; x++) {
            var comparedPilihan = comparedModifier.Pilihan[x];
            for (var y = 0; y < modifier.Pilihan.length; y++) {
                var pilihan = modifier.Pilihan[y];
                if (pilihan.NamaPilihan != comparedPilihan.NamaPilihan
                    || pilihan.Harga != comparedPilihan.Harga
                    || pilihan.QtyDibutuhkan != comparedModifier.QtyDibutuhkan
                    || pilihan.Satuan != comparedPilihan.Satuan) {
                    isPilihanModified = true;
                    break;
                }
            }

        }
        return comparedModifier.ModifierID != modifier.ModifierID ||
            comparedModifier.ModifierName != modifier.ModifierName ||
            comparedModifier.ChooseOnlyOne != modifier.ChooseOnlyOne ||
            comparedModifier.CanAddQuantity != modifier.CanAddQuantity ||
            comparedModifier.Pilihan.length != modifier.Pilihan.length || isPilihanModified;

    }

    function goSavingItem(bunchOfPilihanEkstra, masterItemPostData) {
        var itemFormSaver = new ItemFormSaver();
        var arrayOfSavedItemID2 = [];
        console.log('goSaving Item ..');
        console.log("satuan : " + masterItemPostData.namasatuan);
        try {
            console.log("satuan bahan[0] : " + masterItemPostData.bahans[0].satuan);
        } catch (err) {

        }
        console.log(priceList);
        
        itemFormSaver.saveItemGabung('<?=base_url('ajax/savemasteritemgabung');?>', 
            bunchOfPilihanEkstra, masterItemPostData, priceList
        ).then(function (arrayOfSavedItemID) {
            var ids = [];
            var outlets = [];
            for (var x = 0; x < arrayOfSavedItemID.length; x++) {
                var savedItem = arrayOfSavedItemID[x];
                if(savedItem.outlet != null) {
                    console.log(" Di outlet: " + savedItem.outlet + " disimpan dengan id: " + savedItem.saved_id);
                    ids.push(savedItem.saved_id);
                    outlets.push(savedItem.outlet);
                    arrayOfSavedItemID2.push(savedItem);
                }
            }
            if (hasImage()) {
                var extension = $('#extfoto').val();
                var imageItemPostData = new ImageItemPostData();
                imageItemPostData.ids = ids;
                imageItemPostData.ext = extension;
                imageItemPostData.outlets = outlets;
                imageItemPostData.image = window.imageBase64;
                imageItemPostData.source = 'cloud';

                $('#btn-simpan-single-outlet').html('<span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Mengunggah Foto..');
                $('#btn-simpan-item-modal').html('<span class="spinner"><i class="icon-spin fa fa-refresh"></i></span>Mengunggah Foto..');


                return itemFormSaver.saveImage('<?=$ws_host;?>ws/synimagemultioutlet', imageItemPostData);

            } else {
                return -1;
            }

        }).then(function (response) {
            if (response == -1) {
                console.log("Tidak perlu menyimpan gambar, proses simpan selesai");
                return -1;
            } else {
                console.log(response.data[0]);
                console.log("Selesai simpan gambar, Push Gambar ke device via Firebase");
                return itemFormSaver.push_imageitem('<?=base_url();?>ajax/push_imageitem', arrayOfSavedItemID2);
            }
        }).then(function (response) {
            if (response == -1) {
                console.log("Tidak perlu push gambar, proses simpan selesai");
            } else {
                console.log("Selesai Push gambar, proses simpan selesai");
            }
            saveItemFinished('Item berhasil disimpan');
        }).catch(function (error) {
            console.log('Terjadi Kesalahan : ' + error);
            saveItemFinished('Item gagal disimpan. Terjadi Kesalahan : ' + error);
        });
    }

    jQuery(document).ready(function ($) {
        $ = $;
        window.imageBase64 = '<?=$urlfoto;?>';
        window.currentImageRequest = 0;
        window.currentVariasiHargaRequest = 0;
        window.currentPilihanEkstraRequest = 0;
        window.maxSaveItemRequest = 0;
		grandtotal = 0;

        $('.prevent-default').click(function (e) {
            e.preventDefault();
        });
        $('#txt-item').keyup(function () {
            var namaItem = $(this).val();
            $('#label-bahan').html('Bahan yang dibutuhkan untuk membuat 1 pcs ' + namaItem + ' :');

        });
		
        $('input[data-name=hargabeli]').keyup(function () {
			grandtotal = 0;
			hargabeli = $(this).val();
			$(this).closest('tr').find("input:eq(4)").val(hargabeli * $(this).closest('tr').find("input:eq(1)").val());

			$('input[data-name=total]').each(function () {
					grandtotal += parseInt($(this).val());
			});
				console.log(grandtotal);
				$('#totalhpp').val(grandtotal);
				
        });
		
        $('input[data-name=qty]').keyup(function () {
			grandtotal = 0;
			qty = $(this).val();
			$(this).closest('tr').find("input:eq(4)").val(qty * $(this).closest('tr').find("input:eq(3)").val());

			$('input[data-name=total]').each(function () {
					grandtotal += parseInt($(this).val());
			});
				console.log(grandtotal);
				$('#totalhpp').val(grandtotal);

		});

        $('input[name=jenisproduk]').click(function () {
            var val = $('input[name=jenisproduk]:checked').val();
            if (val == 'true') {
                var adaBahanPenyusun = $('#is-bahan-penyusun').is(':checked');
                if (adaBahanPenyusun) {
                    $('#is-bahan-penyusun').click();
                }
                $('#container-bahan-penyusun').show();
                $('#container-harga-jual').show();
            } else if (val == 'false') {
                $('#container-bahan-penyusun').hide();
                $('#container-grid-tambah-bahan').hide();
                $('#container-harga-jual').hide();
            }
        });
        $('#is-bahan-penyusun').change(function (e) {
            var containerGridAdaBahan = $('#container-grid-tambah-bahan');
            var adaBahanPenyusun = $(this).is(':checked');

            if (adaBahanPenyusun) {
                containerGridAdaBahan.show();
                $('#container-harga-beli').hide();
            } else {
                containerGridAdaBahan.hide();
                $('#container-harga-beli').show();
            }

        });
        $('#label-nama-item').click(function (e) {
            alert($('#txt-item').val());
        });
        $('#label-satuan').click(function (e) {
            alert($('#txt-satuan').val());
        });
        $('#tambah-baris-bahan').click(function (e) {
            e.preventDefault()
            var barisbahan = '<tr class="itembahan">' +
                '<td><input type="text" class="form-control typeahead" data-tag="new"/></td>' +
                '<td><input data-name="qty" value="1" type="number" min="0" class="form-control qty"/></td>' +
                '<td><input type="text" class="form-control " data-tag="satuan" value="PCS"/></td>' +
                '<td><input type="text" class="form-control hargabeli" data-name="hargabeli"/></td>' +
                '<td><input readonly type="text" class="form-control" data-name="total"/></td>' +				
                '<td><a class="btn btn-default btn-hapus-bahan" ><span class="fa fa-trash"></span></a></td>' +
                '<td><a class="btn btn-default btn-copy-bahan" ><span class="fa fa-copy"></span></a></td>' +
                '</tr>';
            $(barisbahan).insertBefore('#row-tambah');

			/* load ulang */
			$('input[data-name=hargabeli]').keyup(function () {
				grandtotal = 0;
				hargabeli = $(this).val();
				$(this).closest('tr').find("input:eq(4)").val(hargabeli * $(this).closest('tr').find("input:eq(1)").val());

				$('input[data-name=total]').each(function () {
						grandtotal += parseInt($(this).val());
				});
					console.log(grandtotal);
					$('#totalhpp').val(grandtotal);
					
			});
			
			$('input[data-name=qty]').keyup(function () {
				grandtotal = 0;
				qty = $(this).val();
				$(this).closest('tr').find("input:eq(4)").val(qty * $(this).closest('tr').find("input:eq(3)").val());

				$('input[data-name=total]').each(function () {
						grandtotal += parseInt($(this).val());
				});
					console.log(grandtotal);
					$('#totalhpp').val(grandtotal);

			});		
				
            $('.btn-hapus-bahan').unbind('click', tombolHapusBahanClick);
            $('.btn-copy-bahan').unbind('click', tombolCopyBahanClick);
            attachEventHapusBarisBahan();
            attachEventCopyBahan();
            bindAutocomplete();
        });

        var deletebahans = [];

        function tombolHapusBahanClick() {

            //this = button > td > tr
            // satu satun a baris tidak boleh dihapus
            var jumlahBaris = $('#grid-tambah-bahan tbody').children().length;
            if (jumlahBaris > 3) {
                var td = $(this).parent();
                var tr = td.parent();
                var index = $('#grid-tambah-bahan tbody').children().index(tr);
                var idbahan = $($($(tr).children()[0]).children()[0]).data('tag');
                if (idbahan != 'new') {
                    deletebahans[index] = tr;
                }
                $(tr).remove();
            } else {
                alert('Tidak bisa dihapus karena baris terakhir.');
            }
        }

        function tombolCopyBahanClick() {
            var itemFormSaver = new ItemFormSaver();
            var td = $(this).parent();
            var tr = td.parent();
            var namabahan = $($($(tr).children()[0]).children()[0]).val();
            var namabahanDanOutlet = [];
            var namaBahanPostData = new NamaBahanPostData();
            namaBahanPostData.outlet = <?=$selected_outlet;?>;
            namaBahanPostData.nama = namabahan;
            namabahanDanOutlet.push(namaBahanPostData);
            console.log(namabahanDanOutlet);

            itemFormSaver.copyBahan('<?=base_url('ajax/getCopyBahan');?>', {namabahan: namabahanDanOutlet}).then(function (response) {
//                console.log(response);
                for (var x = 0; x < response.length; x++) {
                    console.log(response[x]);


                    var barisbahan = '<tr class="itembahan">' +
                        '<td><input type="text" class="form-control typeahead" data-tag="new" value="' + response[x].ItemName + '"/></td>' +
                        '<td><input type="number" min="0" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))" class="form-control" value="' + response[x].QtyNeed + '"/></td>' +
                        '<td><input type="text" class="form-control typeaheadsatuan" data-tag="satuan" value="' + response[x].Satuan + '"/></td>' +
                        '<td><input type="text" class="form-control hargabeli" data-name="hargabeli" value="'+ response[x].PurchasePrice + '"/></td>' +
                        '<td><input readonly type="text" class="form-control" data-name="total" value = "'+ response[x].QtyNeed*response[x].PurchasePrice + '"/></td>' +
                        '<td><a class="btn btn-default btn-hapus-bahan" ><span class="fa fa-trash"></span></a></td>' +
                        '<td><a class="btn btn-default btn-copy-bahan" ><span class="fa fa-copy"></span></a></td>' +
                        '</tr>';
                    $(barisbahan).insertBefore('#row-tambah');

                    $('.btn-hapus-bahan').unbind('click', tombolHapusBahanClick);
                    $('.btn-copy-bahan').unbind('click', tombolCopyBahanClick);
                    attachEventHapusBarisBahan();
                    attachEventCopyBahan();
                    bindAutocomplete();


                }
            });
        }

        function attachEventHapusBarisBahan() {
            $('.btn-hapus-bahan').click(tombolHapusBahanClick);
        }

        function attachEventCopyBahan() {
            $('.btn-copy-bahan').click(tombolCopyBahanClick);
        }

        attachEventHapusBarisBahan();
        attachEventCopyBahan();


        function changeimg(str) {
            if (typeof str === "object") {
                str = str.target.result;
                window.imageBase64 = str.replace("data:image/jpeg;base64,", ""); // file reader jpg
                window.imageBase64 = window.imageBase64.replace("data:image/png;base64,", "");// file reader png


            }
            $('#prevImage').attr('src', str);
            $('#btnSimpan').prop('disabled', false);
        }

        $("#foto").change(function () {
            var fileObj = this,
                file;
            if (fileObj.files) {
                file = fileObj.files[0];
                if (file.size > 2097152) {
                    alert("Ukuran gambar tidak boleh lebih dari 2 MB");
                    $(fileObj).val("");
                    $('#foto').filestyle('clear');
                    $('.badge').remove();
                    return;
                }
            }
            var path = $(this).val();
            if (path.lastIndexOf(".") > 0) {
                var ext = path.substring(path.lastIndexOf(".") + 1, path.length);
                $('#extfoto').val(ext);
                if (fileObj.files) {
                    file = fileObj.files[0];
                    var fr = new FileReader;
                    fr.onloadend = changeimg;
                    fr.readAsDataURL(file)
                } else {
                    file = fileObj.value;
                    changeimg(file);
                }
            } else {
                $('#extfoto').val('');
                alert('Foto tidak valid');
            }

        });
        $('#btn-hapus-gambar').click(function () {
            window.imageBase64 = '';
            $('#prevImage').attr('src', '');
            $('#foto').filestyle('clear');
            $('.badge').remove();
        });
        setEnabledDisabledBtnKategori();
        $('#list-kategori').change(function () {

            setEnabledDisabledBtnKategori();
        });
        function setEnabledDisabledBtnKategori() {
            var selectedKategori = $('#list-kategori').val();
            var btnEdit = $('#btn-edit-kategori');
            var btnHapus = $('#btn-hapus-kategori');

            if (selectedKategori == '') {
                btnEdit.attr('class', 'btn btn-default');
                btnEdit.prop('disabled', true);
                btnHapus.attr('class', 'btn btn-default');
                btnHapus.prop('disabled', true);
            } else {
                btnEdit.attr('class', 'btn btn-primary');
                btnEdit.prop('disabled', false);
                btnHapus.attr('class', 'btn btn-primary');
                btnHapus.prop('disabled', false);
            }
        }

        function setEnabledDisabledBtnSatuan() {
            var btnHapus = $('#btn-hapus-satuan');
            var length = $('#list-satuan').children().length;
            var btnEdit = $('#btn-edit-satuan');
            var btnHapus = $('#btn-hapus-satuan');
            if (length <= 1) {
                btnHapus.attr('class', 'btn btn-default');
                btnHapus.prop('disabled', true);
                btnEdit.attr('class', 'btn btn-default');
                btnEdit.prop('disabled', true);
            } else {
                btnHapus.attr('class', 'btn btn-primary');
                btnHapus.prop('disabled', false);
                btnEdit.attr('class', 'btn btn-primary');
                btnEdit.prop('disabled', false);
            }
            if (length > 0) {
                btnEdit.attr('class', 'btn btn-primary');
                btnEdit.prop('disabled', false);
            }
        }


        $('#list-satuan').change(function () {
                setEnabledDisabledBtnSatuan();
            }
        );
        setEnabledDisabledBtnSatuan();
        $('#outlet').change(function () {
            window.location = '<?=$modeform == 'edit' ? base_url("produk/itemform") . "?id=" . urlencode($form['nama item']) . "&outlet="
                    : base_url("produk/itemform") . "?outlet=";?>' + $(this).val();
        });


        $('#btn-simpan-single-outlet').click(function (e) {
            $(this).addClass('active');
            $(this).prop('disabled', true);
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
                    //var Satuan = $(selectSatuan).find(":selected").data('tag');
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
						hargabeli: hargaBeli,
                        satuan: Satuan
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
                pilihanEkstra.outlets = [<?=$selected_outlet;?>];
                console.log('_deletedMdf');
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
                console.log(op);
                if (op != 'nothing') {
                    var pilihanEkstra = new PilihanEkstraPostData();
                    pilihanEkstra.outlets = [<?=$selected_outlet;?>];
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
            console.log(bunchOfPilihanEkstra)

            //untuk Save Item
            var masterItemPostData = new MasterItemPostData();
            masterItemPostData.selectedoutlet = <?=$selected_outlet;?>;
            masterItemPostData.itemname = namaitem;
            masterItemPostData.oldnamakategori = $('#list-kategori :selected').data('tag');
            masterItemPostData.namakategori = $('#list-kategori :selected').val();//multi outle dari id berubah ke nama
            masterItemPostData.namasatuan = $('#txt-satuan').val();
            masterItemPostData.hargajual = $('#txt-harga-jual').val();
            masterItemPostData.hargabeli = $('#txt-harga-beli').val();
            masterItemPostData.isproduk = $('input[name=jenisproduk]:checked').val();
            masterItemPostData.punyabahan = adabahan;
            masterItemPostData.bahans = bahan;
            masterItemPostData.mode = '<?=$modeform;?>';
            masterItemPostData.olditemname = '<?=addslashes($form['nama item']); ?>';
            masterItemPostData.idoutlets = [<?=$selected_outlet;?>];
            masterItemPostData.deletegambar = (window.imageBase64 == '');
            masterItemPostData.modifiers = mdfs;


            goSavingItem(bunchOfPilihanEkstra, masterItemPostData);

        });
        $('#form-item').keypress(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        $('#form-item').keyup(function (e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        <?php
        if ($form['punya bahan'] == 'true') {?>
        $('#is-bahan-penyusun').trigger('change');
        <?php
        }
        ?>

        var substringMatcher = function (strs) {
            return function findMatches(q, cb) {
                var matches, substringRegex;

                // an array that will be populated with substring matches
                matches = [];

                // regex used to determine if a string contains the substring `q`
                substrRegex = new RegExp(q, 'i');

                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each(strs, function (i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });

                cb(matches);
            };
        };

        function bindAutocomplete() {
            var selectedIdSatuan = 0;
            var hargabeli = 0;
            $('.typeahead').typeahead({
                source: function (query, process) {
                    itemnames = [];
                    map = [];
                    var data = [
                        <?php
                        $json = '';
                        foreach ($autocompletebahan as $row) {
                            $json .= "{ItemName:'" . str_replace("'","\'",$row->ItemName) . "',Satuan:'" . $row->Unit . "',HargaBeli:'" . $row->PurchasePrice . "'},";
                        }
                        echo substr($json, 0, strlen($json) - 1);
                        ?>
                    ];
                    $.each(data, function (i, item) {
                        map[item.ItemName] = item;
                        itemnames.push(item.ItemName);
                    });

                    process(itemnames);
                },
                updater: function (item) {
                    selectedIdSatuan = map[item].Satuan;
                    hargabeli = map[item].HargaBeli;
                    return item;
                },
                matcher: function (item) {
                    if (item.toLowerCase().indexOf(this.query.trim().toLowerCase()) != -1) {
                        return true;
                    } else {
                        selectedIdSatuan = 0;
                        hargabeli=0;
                    }
                },
                sorter: function (items) {
                    return items.sort();
                },
                highlighter: function (item) {
                    var regex = new RegExp('(' + this.query + ')', 'gi');
                    return item.replace(regex, "<strong>$1</strong>");
                },
            });
            var onChange = function (event) {
                var bahan = event.target.value;
                var tr = $(this).parent().parent();
                if (selectedIdSatuan != 0) {
                    console.log("selected satuan onchange : "+selectedIdSatuan);
                    var opt = tr.find('input[data-tag="satuan"]');
                    console.log( $(opt).val());
                    $(opt).val(selectedIdSatuan);
                    var hbt = tr.find('input[data-name="hargabeli"]');
                    $(hbt).val(hargabeli);
			var grandtotal = 0;
			var tmpqty = tr.find('input[data-name="qty"]').val();
			var tmphb = tr.find('input[data-name="hargabeli"]').val();
			tr.find("input:eq(4)").val(tmpqty * tmphb);

			$('input[data-name=total]').each(function () {
					grandtotal += parseInt($(this).val());
			});
                        console.log(grandtotal);
                        $('#totalhpp').val(grandtotal);
//                    $(opt).parent().attr('disabled', 'disabled');
                } else {
                    var firstOption = tr.find('select').children()[0];
                    $(firstOption).attr('selected', 'selected');
                    $(firstOption).parent().removeAttr('disabled', '');
                }
            };
            $('.typeahead').on('change', onChange);

        }

        bindAutocomplete();


    });


</script>
