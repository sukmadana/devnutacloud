<?php

/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 18/11/16
 * Time: 19:40
 */

?>
<!--<script src="--><? //= base_url('js/nutalibs/itetambah-baris-pilihan-ekstram.js'); 
                    ?>
<!--" type="text/javascript"></script>-->

<script type="text/javascript">
    var $;
    var deletedata = []

    function bindAutocomplete() {
        (function($) {
            var selectedIdSatuan = 0;
            var hargabeli = 0;
            $('.typeahead').typeahead({
                source: function(query, process) {
                    console.log('aaaa');
                    itemnames = [];
                    map = [];
                    var data = [
                        <?php
                        $json = '';
                        foreach ($autocompletebahan as $row) {
                            $json .= "{ItemName:'" . str_replace("'", "\'", $row->ItemName) . "',Satuan:'" . $row->Unit . "',HargaBeli:'" . $row->PurchasePrice . "'},";
                        }
                        echo substr($json, 0, strlen($json) - 1);
                        ?>
                    ];
                    $.each(data, function(i, item) {
                        map[item.ItemName] = item;
                        itemnames.push(item.ItemName);
                    });

                    process(itemnames);
                },
                updater: function(item) {
                    selectedIdSatuan = map[item].Satuan;
                    hargabeli = map[item].HargaBeli;
                    return item;
                },
                matcher: function(item) {
                    if (item.toLowerCase().indexOf(this.query.trim().toLowerCase()) != -1) {
                        return true;
                    } else {
                        selectedIdSatuan = 0;
                        hargabeli = 0;
                    }
                },
                sorter: function(items) {
                    return items.sort();
                },
                highlighter: function(item) {
                    var regex = new RegExp('(' + this.query + ')', 'gi');
                    return item.replace(regex, "<strong>$1</strong>");
                },
            });
            var onChange = function(event) {
                var classname = event.target.className
                var bahan = event.target.value;
                classname = classname.split(' ')
                var cla2 = classname[3].replace("elm", "")
                classname = classname[2].replace("from", "")
                var elm = document.getElementsByClassName('row' + classname)[0]
                elm = elm.getElementsByClassName("kolomBisaTambahJumlahPilihan")[0]
                elm = elm.getElementsByTagName("input")[0]
                if (cla2 == '1') {
                    elm.value = bahan
                } else {
                    var indx = elm.value.includes('...')
                    if (indx == false) {
                        elm.value = elm.value + '...'
                    }
                }
                var tr = $(this).parent().parent();
                if (selectedIdSatuan != 0) {
                    console.log("selected satuan onchange : " + selectedIdSatuan);
                    var opt = tr.find('input[data-tag="satuan"]');
                    console.log($(opt).val());
                    $(opt).val(selectedIdSatuan);
                    var hbt = tr.find('input[data-name="hargabeli"]');
                    $(hbt).val(hargabeli);
                    var grandtotal = 0;
                    var tmpqty = tr.find('input[data-name="qty"]').val();
                    var tmphb = tr.find('input[data-name="hargabeli"]').val();
                    tr.find("input:eq(4)").val(tmpqty * tmphb);

                    $(this).closest('table').find("input[data-name=total]").each(function() {
                        grandtotal += parseInt($(this).val());
                    });
                    console.log(grandtotal);
                    $(this).closest('table').find("input[id=totalhpp]").val(grandtotal);
                    //                    $(opt).parent().attr('disabled', 'disabled');
                } else {
                    var firstOption = tr.find('select').children()[0];
                    $(firstOption).attr('selected', 'selected');
                    $(firstOption).parent().removeAttr('disabled', '');
                }
            };
            $('.typeahead').on('change', onChange);
            $('input[data-name=qty]').keyup(function() {
                grandtotal = 0;
                qty = $(this).val();
                $(this).closest('tr').find("input:eq(4)").val(qty * $(this).closest('tr').find("input:eq(3)").val());

                $(this).closest('table').find("input[data-name=total]").each(function() {
                    grandtotal += parseInt($(this).val());
                });
                $(this).closest('table').find("input[id=totalhpp]").val(grandtotal);

            });
            $('input[data-name=hargabeli]').keyup(function() {
                grandtotal = 0;
                hargabeli = $(this).val();
                $(this).closest('tr').find("input:eq(4)").val(hargabeli * $(this).closest('tr').find("input:eq(1)").val());

                $(this).closest('table').find("input[data-name=total]").each(function() {
                    grandtotal += parseInt($(this).val());
                });
                console.log(grandtotal);
                $(this).closest('table').find("input[id=totalhpp]").val(grandtotal);

            });
        })(jQuery)
    };
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

    function hps(e, id) {
        (function($) {
            if (id != '') {
                deletedata.push(id)
            }
            $("." + e).remove()
        })(jQuery)
    }

    function hapusdetail(e) {
        (function($) {
            var i = e.replace("item", "")
            $(".row" + i).remove()
            if ($("#masterbahan" + i).length > 0) {
                $("#masterbahan" + i).remove()
            }
        })(jQuery)
    }

    function saveItemFinished(msg) {
        setTimeout(function() {
            alert(msg);
            window.location = '<?= base_url("produk/index?outlet=" . $selected_outlet); ?>';
            $('#btn-simpan-single-outlet').removeClass('active');
            $('#btn-simpan-single-outlet').prop('disabled', false);
            $('#btn-simpan-single-outlet').text('Simpan');
            $('#btn-simpan-item-modal').removeClass('active');
            $('#btn-simpan-item-modal').prop('disabled', false);
            $('#btn-simpan-item-modal').text('Simpan');

        }, 1000);

    }

    function addBahan(e, dd = false) {
        var id = $(e).attr('id')
        id = id.replace("btnbahan", "")
        var nama = ""
        var namas = $(".row" + id)
        nama = namas[0].getElementsByTagName("input")[0].value;
        console.log(nama);
        console.log(id);
        if ($('#tambahbaris' + id).length < 1) {
            var newbahan = $("#masterbahan").clone();
            $(newbahan).removeClass("hidden");
            $(newbahan).attr('id', 'masterbahan' + id);
            console.log(newbahan[0]);
            newbahan[0].getElementsByTagName("p")[0].innerHTML = "Bahan yang dibutuhkan untuk membuat 1 pcs <b>" + nama + "</b>: ";
            newbahan[0].getElementsByClassName("itembahan")[0].className = "itembahan" + id + "1";
            newbahan[0].getElementsByClassName('typeahead')[0].className += " from" + id + " elm1";
            newbahan[0].getElementsByClassName('btntambah')[0].id = "tambahbaris" + id;
            newbahan[0].getElementsByClassName('btntambah')[0].setAttribute('href', 'javascript:void(0)');
            newbahan[0].getElementsByClassName('btntambah')[0].setAttribute('onclick', 'tmbhBrs(id)');
            newbahan[0].getElementsByClassName('btnselesai')[0].id = "closebaris" + id;
            newbahan[0].getElementsByClassName('btnselesai')[0].setAttribute('href', 'javascript:void(0)');
            newbahan[0].getElementsByClassName('btnselesai')[0].setAttribute('onclick', 'closebaris(id)');
            newbahan[0].getElementsByClassName('btn-hapus-bahan')[0].setAttribute('href', 'javascript:void(0)');
            newbahan[0].getElementsByClassName('btn-hapus-bahan')[0].setAttribute('onclick', "hps('itembahan" + id + "1', '')");
            newbahan[0].getElementsByClassName("row-tambahs")[0].id = "row-tambah" + id;
            newbahan[0].getElementsByTagName('table')[0].id = "grid-tambah-bahan" + id;
            $("#copyhere").append(newbahan[0]);
            bindAutocomplete();
        } else {
            $("#masterbahan" + id).removeClass("hidden")
        }
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
                if (pilihan.NamaPilihan != comparedPilihan.NamaPilihan ||
                    pilihan.Harga != comparedPilihan.Harga ||
                    pilihan.QtyDibutuhkan != comparedModifier.QtyDibutuhkan ||
                    pilihan.Satuan != comparedPilihan.Satuan) {
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

        itemFormSaver.saveItemGabung('<?= base_url('ajax/savemasteritemgabung'); ?>',
            bunchOfPilihanEkstra, masterItemPostData, priceList
        ).then(function(arrayOfSavedItemID) {
            var ids = [];
            var outlets = [];
            for (var x = 0; x < arrayOfSavedItemID.length; x++) {
                var savedItem = arrayOfSavedItemID[x];
                if (savedItem.outlet != null) {
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


                return itemFormSaver.saveImage('<?= $ws_host; ?>ws/synimagemultioutlet', imageItemPostData);

            } else {
                return -1;
            }

        }).then(function(response) {
            if (response == -1) {
                console.log("Tidak perlu menyimpan gambar, proses simpan selesai");
                return -1;
            } else {
                console.log(response.data[0]);
                console.log("Selesai simpan gambar, Push Gambar ke device via Firebase");
                return itemFormSaver.push_imageitem('<?= base_url(); ?>ajax/push_imageitem', arrayOfSavedItemID2);
            }
        }).then(function(response) {
            if (response == -1) {
                console.log("Tidak perlu push gambar, proses simpan selesai");
            } else {
                console.log("Selesai Push gambar, proses simpan selesai");
            }
            saveItemFinished('Item berhasil disimpan');
        }).catch(function(error) {
            console.log('Terjadi Kesalahan : ' + error);
            saveItemFinished('Item gagal disimpan. Terjadi Kesalahan : ' + error);
        });
    }

    jQuery(document).ready(function($) {
        $ = $;
        window.imageBase64 = '<?= $urlfoto; ?>';
        window.currentImageRequest = 0;
        window.currentVariasiHargaRequest = 0;
        window.currentPilihanEkstraRequest = 0;
        window.maxSaveItemRequest = 0;
        grandtotal = 0;

        $('.prevent-default').click(function(e) {
            e.preventDefault();
        });
        $('#txt-item').keyup(function() {
            var namaItem = $(this).val();
            $('#label-bahan').html('Bahan yang dibutuhkan untuk membuat 1 pcs ' + namaItem + ' :');

        });

        $('input[data-name=hargabeli]').keyup(function() {
            grandtotal = 0;
            hargabeli = $(this).val();
            $(this).closest('tr').find("input:eq(4)").val(hargabeli * $(this).closest('tr').find("input:eq(1)").val());

            $('input[data-name=total]').each(function() {
                grandtotal += parseInt($(this).val());
            });
            console.log(grandtotal);
            $('#totalhpp').val(grandtotal);

        });

        $('input[data-name=qty]').keyup(function() {
            grandtotal = 0;
            qty = $(this).val();
            $(this).closest('tr').find("input:eq(4)").val(qty * $(this).closest('tr').find("input:eq(3)").val());

            $('input[data-name=total]').each(function() {
                grandtotal += parseInt($(this).val());
            });
            console.log(grandtotal);
            $('#totalhpp').val(grandtotal);

        });

        $('input[name=jenisproduk]').click(function() {
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
        $('#is-bahan-penyusun').change(function(e) {
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
        $('#label-nama-item').click(function(e) {
            alert($('#txt-item').val());
        });
        $('#label-satuan').click(function(e) {
            alert($('#txt-satuan').val());
        });

        $('a[id^="tambah-baris-bahan"]').click(function(e) {
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
            $('input[data-name=hargabeli]').keyup(function() {
                grandtotal = 0;
                hargabeli = $(this).val();
                $(this).closest('tr').find("input:eq(4)").val(hargabeli * $(this).closest('tr').find("input:eq(1)").val());

                $('input[data-name=total]').each(function() {
                    grandtotal += parseInt($(this).val());
                });
                console.log(grandtotal);
                $('#totalhpp').val(grandtotal);

            });

            $('input[data-name=qty]').keyup(function() {
                grandtotal = 0;
                qty = $(this).val();
                $(this).closest('tr').find("input:eq(4)").val(qty * $(this).closest('tr').find("input:eq(3)").val());

                $('input[data-name=total]').each(function() {
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

        // $('#tambah-baris-bahan').click(function (e) {
        //     e.preventDefault()
        //     var barisbahan = '<tr class="itembahan">' +
        //         '<td><input type="text" class="form-control typeahead" data-tag="new"/></td>' +
        //         '<td><input data-name="qty" value="1" type="number" min="0" class="form-control qty"/></td>' +
        //         '<td><input type="text" class="form-control " data-tag="satuan" value="PCS"/></td>' +
        //         '<td><input type="text" class="form-control hargabeli" data-name="hargabeli"/></td>' +
        //         '<td><input readonly type="text" class="form-control" data-name="total"/></td>' +
        //         '<td><a class="btn btn-default btn-hapus-bahan" ><span class="fa fa-trash"></span></a></td>' +
        //         '<td><a class="btn btn-default btn-copy-bahan" ><span class="fa fa-copy"></span></a></td>' +
        //         '</tr>';
        //     $(barisbahan).insertBefore('#row-tambah');
        //
        //  /* load ulang */
        //  $('input[data-name=hargabeli]').keyup(function () {
        //      grandtotal = 0;
        //      hargabeli = $(this).val();
        //      $(this).closest('tr').find("input:eq(4)").val(hargabeli * $(this).closest('tr').find("input:eq(1)").val());
        //
        //      $('input[data-name=total]').each(function () {
        //              grandtotal += parseInt($(this).val());
        //      });
        //          console.log(grandtotal);
        //          $('#totalhpp').val(grandtotal);
        //
        //  });
        //
        //  $('input[data-name=qty]').keyup(function () {
        //      grandtotal = 0;
        //      qty = $(this).val();
        //      $(this).closest('tr').find("input:eq(4)").val(qty * $(this).closest('tr').find("input:eq(3)").val());
        //
        //      $('input[data-name=total]').each(function () {
        //              grandtotal += parseInt($(this).val());
        //      });
        //          console.log(grandtotal);
        //          $('#totalhpp').val(grandtotal);
        //
        //  });
        //
        //     $('.btn-hapus-bahan').unbind('click', tombolHapusBahanClick);
        //     $('.btn-copy-bahan').unbind('click', tombolCopyBahanClick);
        //     attachEventHapusBarisBahan();
        //     attachEventCopyBahan();
        //     bindAutocomplete();
        // });

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
            namaBahanPostData.outlet = <?= $selected_outlet; ?>;
            namaBahanPostData.nama = namabahan;
            namabahanDanOutlet.push(namaBahanPostData);
            console.log(namabahanDanOutlet);

            itemFormSaver.copyBahan('<?= base_url('ajax/getCopyBahan'); ?>', {
                namabahan: namabahanDanOutlet
            }).then(function(response) {
                //                console.log(response);
                for (var x = 0; x < response.length; x++) {
                    console.log(response[x]);


                    var barisbahan = '<tr class="itembahan">' +
                        '<td><input type="text" class="form-control typeahead" data-tag="new" value="' + response[x].ItemName + '"/></td>' +
                        '<td><input type="number" min="0" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))" class="form-control" value="' + response[x].QtyNeed + '"/></td>' +
                        '<td><input type="text" class="form-control typeaheadsatuan" data-tag="satuan" value="' + response[x].Satuan + '"/></td>' +
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
                window.imageBase64 = window.imageBase64.replace("data:image/png;base64,", ""); // file reader png


            }
            $('#prevImage').attr('src', str);
            $('#btnSimpan').prop('disabled', false);
        }

        $("#foto").change(function() {
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
        $('#btn-hapus-gambar').click(function() {
            window.imageBase64 = '';
            $('#prevImage').attr('src', '');
            $('#foto').filestyle('clear');
            $('.badge').remove();
        });
        setEnabledDisabledBtnKategori();
        $('#list-kategori').change(function() {

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


        $('#list-satuan').change(function() {
            setEnabledDisabledBtnSatuan();
        });
        setEnabledDisabledBtnSatuan();
        $('#outlet').change(function() {
            window.location = '<?= $modeform == 'edit' ? base_url("produk/itemform") . "?id=" . urlencode($form['nama item']) . "&outlet="
                                    : base_url("produk/itemform") . "?outlet="; ?>' + $(this).val();
        });


        $('#btn-simpan-single-outlet').click(function(e) {
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
                $('.itembahan').each(function(index, tr) {
                    var inputNamaBahan = tr.children[0].children;
                    var inputQty = tr.children[1].children;
                    var selectSatuan = tr.children[2].children;
                    var hargaBeli = tr.children[3].children; // purchasePrice
                    var namaBahan = $(inputNamaBahan).val();
                    var Qty = $(inputQty).val();
                    var Satuan = $(selectSatuan).val();
                    var hargaBeli = $(hargaBeli).val();
                    //var Satuan = $(selectSatuan).find(":selected").data('tag');
                    <?php if ($modeform == 'edit') { ?>
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
            $('.nuta-modifier:checked').each(function(index, object) {
                mdfs.push($(object).data('tag'));
            })


            //untuk Save modifier
            var bunchOfPilihanEkstra = [];
            for (var x = 0; x < ListModifierDeleted.length; x++) {
                var _deletedMdf = ListModifierDeleted[x];
                var pilihanEkstra = new PilihanEkstraPostData();
                pilihanEkstra.outlets = [<?= $selected_outlet; ?>];
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
                for (var j = 0; j < mdfs.length; j++) {
                    if (mdfs[j] == _newMdf.ModifierName) {
                        op = 'exist';
                        break;
                    }
                }
                console.log(op);
                if (op != 'nothing') {
                    var pilihanEkstra = new PilihanEkstraPostData();
                    pilihanEkstra.outlets = [<?= $selected_outlet; ?>];
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
            masterItemPostData.selectedoutlet = <?= $selected_outlet; ?>;
            masterItemPostData.itemname = namaitem;
            masterItemPostData.oldnamakategori = $('#list-kategori :selected').data('tag');
            masterItemPostData.namakategori = $('#list-kategori :selected').val(); //multi outle dari id berubah ke nama
            masterItemPostData.namasatuan = $('#txt-satuan').val();
            masterItemPostData.hargajual = $('#txt-harga-jual').val();
            masterItemPostData.hargabeli = $('#txt-harga-beli').val();
            masterItemPostData.isproduk = $('input[name=jenisproduk]:checked').val();
            masterItemPostData.punyabahan = adabahan;
            masterItemPostData.bahans = bahan;
            masterItemPostData.mode = '<?= $modeform; ?>';
            masterItemPostData.olditemname = '<?= $form['nama item']; ?>';
            masterItemPostData.idoutlets = [<?= $selected_outlet; ?>];
            masterItemPostData.deletegambar = (window.imageBase64 == '');
            masterItemPostData.modifiers = mdfs;


            goSavingItem(bunchOfPilihanEkstra, masterItemPostData);

        });
        $('#form-item').keypress(function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        $('#form-item').keyup(function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
        <?php
        if ($form['punya bahan'] == 'true') { ?>
            $('#is-bahan-penyusun').trigger('change');
        <?php
        }
        ?>

        var substringMatcher = function(strs) {
            return function findMatches(q, cb) {
                var matches, substringRegex;

                // an array that will be populated with substring matches
                matches = [];

                // regex used to determine if a string contains the substring `q`
                substrRegex = new RegExp(q, 'i');

                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each(strs, function(i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });

                cb(matches);
            };
        };

        bindAutocomplete();

        $('a[id^="tambahbaris"]').click(function() {
            console.log('wwwww')
        })

        $('#tambahBaris').click(function(e) {
            e.preventDefault()
            var newPilihanRow = {
                "NamaPilihan": "",
                "Harga": 0,
                "QtyDibutuhkan": 0,
                "Satuan": "",
                "PlaceholderPilihan": "misal: Kacang"
            };
            var selectSatuan = '<select class="form-control">';
            for (var x = 0; x < jsonSatuan.length; x++) {
                selectSatuan += '<option value="' + jsonSatuan[x].name + '" data-tag="' + jsonSatuan[x].name + '">' + jsonSatuan[x].name + '</option>';
            }
            selectSatuan += '</select>';
            var iterasi = $('.item-pilihan-ekstra').length + 1
            var l = "item" + iterasi
            l = "hapusdetail('" + l + "')"
            var html = '<tr class="item-pilihan-ekstra row' + iterasi + '" data-op="new">' +
                '<td><input type="text" class="form-control" placeholder="' + newPilihanRow.PlaceholderPilihan + '"' +
                'value="' + newPilihanRow.NamaPilihan + '"/></td>' +
                '<td><div class="input-group"><span class="input-group-addon">Rp.</span><input type="text" class="form-control" placeholder="' + newPilihanRow.Harga + '"' +
                'value="' + (newPilihanRow.Harga == 0 ? '' : newPilihanRow.Harga) + '" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))"/></div></td>' +
                '<td class="kolomBisaTambahJumlahPilihan">' +
                '<div class="input-group mb-3">' +
                '  <input type="text" class="form-control" placeholder="" aria-label="" aria-describedby="basic-addon1">' +
                '  <div class="input-group-addon">' +
                '    <a href="javascript:void(0)" id="btnbahan' + iterasi + '" onclick="addBahan(this,\'\');">Bahan</a>' +
                '  </div>' +
                '</div>' +
                '</td>' +
                '<td> <a href = "#" class= "btn btn-danger" onclick= "' + l + '" > Hapus </a></td></tr>';
            $(html).insertBefore('#row-tambah-pilihan-ekstra');
            // currentModifier.Pilihan.push(newPilihanRow);
            // changeBisaTambahJumlahPilihan($('#checkBisaTambahJumlahPilihan'));
        });


        $("#btnSimpan").click(function() {
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

            var outlet = '<?= $_GET["outlet"]; ?>'
            var data = {
                nama: namaekstra,
                pilihan: modifier,
                bahan: bahan,
                outlet: [outlet],
                delete: deletedata,
                cekpilihsatu: $('#checkPilihSatu').val()
            }

            $.ajax({
                url: '<?= base_url('ajax/saveextra'); ?>',
                method: 'POST',
                data: data,
                success: function(data) {
                    data = JSON.parse(data)
                    if (data.status === true) {
                        window.location.href = '<?= base_url('extra/index?outlet=' + outlet); ?>'
                    }
                }

            })
        })

    });

    function tmbhBrs(e) {
        var id = e
        id = e.replace("tambahbaris", "")
        var elm = document.getElementById("grid-tambah-bahan" + id)
        elm = elm.getElementsByClassName("typeahead").length + 1
        var k = "itembahan" + id + elm
        k = "hps('" + k + "', '')"
        var barisbahan = '<tr class="itembahan itembahan' + id + elm + '">' +
            '<td><input type="text" class="form-control typeahead from' + id + ' elm' + elm + '" data-tag="new"/></td>' +
            '<td><input data-name="qty" value="1" type="number" min="0" class="form-control qty"/></td>' +
            '<td><input type="text" class="form-control " data-tag="satuan" value="PCS"/></td>' +
            '<td><input type="text" class="form-control hargabeli" data-name="hargabeli"/></td>' +
            '<td><input readonly type="text" class="form-control" data-name="total"/></td>' +
            '<td><a class="btn btn-default btn-hapus-bahan" href="javascript:void(0)" onclick="' + k + '"><span class="fa fa-trash"></span></a> </td>' +
            '</tr>';
        $(barisbahan).insertBefore('#row-tambah' + id);
        bindAutocomplete();
    }

    function closebaris(e) {
        var id = e
        id = e.replace("closebaris", "")
        $("#masterbahan" + id).addClass("hidden")
    }
</script>