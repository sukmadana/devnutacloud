<script type="text/javascript">
    var currentModifier;
    var currentModifierID;
    var newModifiers = [];
    var isCurrentModifierOnTheList = false;
</script>
<script type="text/javascript">

    var listModifier =<?php echo json_encode($list_pilihan_ekstra);?>;
    var ListModifierDeleted = [];
    var originalListModifier =<?php echo json_encode($list_pilihan_ekstra);?>;

    var newModifier = {
        "ModifierID": -1,
        "ModifierName": "",
        "PlaceholderEsktra": "Misal: Toping",
        "ChooseOnlyOne": 0,//1=true,0=false
        "CanAddQuantity": 0,//1=true,0=false
        "OldModifierName": "",
        "Pilihan": [
            {
                "NamaPilihan": "",
                "OldNamaPilihan": "",
                "operation": "new",
                "Harga": 0,
                "QtyDibutuhkan": 0,
                "Satuan": "",
                "PlaceholderPilihan": "misal: Keju"
            },
            {
                "NamaPilihan": "",
                "OldNamaPilihan": "",
                "operation": "new",
                "Harga": 0,
                "QtyDibutuhkan": 0,
                "Satuan": "",
                "PlaceholderPilihan": "misal: Coklat"
            },
            {
                "NamaPilihan": "",
                "OldNamaPilihan": "",
                "operation": "new",
                "Harga": 0,
                "QtyDibutuhkan": 0,
                "Satuan": "",
                "PlaceholderPilihan": "misal: Kacang"
            }
        ]
    };
    var selectedEkstraName;
    var selectedEkstraID;
    jQuery(document).ready(function (x) {
        $ = x;
//        changeBisaTambahJumlahPilihan($('#checkBisaTambahJumlahPilihan'));
        var pilihanEkstraModal;

        function renderModifierList() {
            $('.modifier-list-row').remove();
            for (var x = 0; x < listModifier.length; x++) {
                var modifier = listModifier[x];
                var html = '<tr class="modifier-list-row">';
                html += '<td class="tc-center">'
                html += '<input class="i-min-check nuta-modifier" type="checkbox" id="minimal-checkbox-1"';
                html += (modifier.Selected ? 'checked="checked"' : '');
                html += 'data-tag="' + modifier.ModifierName + '">';
                html += '</td>';
                html += '<td>' + modifier.ModifierName + '</td>';
                html += '<td align="right"><a href="#"';
                html += '    onclick="setDialogID(\'' + modifier.ModifierName + '\', \'' + modifier.ModifierID + '\');"';
                html += '   class="ico-cirlce-widget"';
                html += '   data-toggle="modal"';
                html += '   data-target="#pilihan-ekstra-modal"';
                html += '    data-keyboard="false">';
                html += '<span><i class="fa fa-chevron-right"></i></span>';
                html += '   </a></td>';
                html += '</tr>';
                $(html).insertBefore('#btn-tambah-pilihan-ekstra');
            }
            $('.i-min-check').iCheck({
                checkboxClass: 'iradio_minimal',
                radioClass: 'iradio_minimal-pink',
                increaseArea: '30%' // optional
            });
        }

        renderModifierList();


        $('#pilihan-ekstra-modal').on('show.bs.modal', function (event) {
            pilihanEkstraModal = $(this);
            currentModifier = getModifierFromListOrCreateNew(selectedEkstraName);
//            currentModifierID = getModifierFromListOrCreateNew(selectedEkstraID);
            initDialogFormUI(currentModifier);

        });
        $('#tambah-baris-pilihan-ekstra').click(function (e) {
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
            var html = '<tr class="item-pilihan-ekstra" data-op="new">' +
                '<td><input type="text" class="form-control" placeholder="' + newPilihanRow.PlaceholderPilihan + '"' +
                'value="' + newPilihanRow.NamaPilihan + '"/></td>' +
                '<td><div class="input-group"><span class="input-group-addon">Rp.</span><input type="text" class="form-control" placeholder="' + newPilihanRow.Harga + '"' +
                'value="' + (newPilihanRow.Harga == 0 ? '' : newPilihanRow.Harga) + '" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))"/></div></td>' +
                '<td class="kolomBisaTambahJumlahPilihan"><input type="text" class="form-control" placeholder="0" value="' + newPilihanRow.QtyDibutuhkan + '" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))"/></td>' +
                '<td class="kolomBisaTambahJumlahPilihan"><input type="text" class="form-control" placeholder="misal: PCS"' +
                'value="' + newPilihanRow.Satuan + '"/></td >' +
                '<td> <a href = "#" class= "btn btn-danger" onclick = "hapusPilihanEkstra(this,\'\');" > Hapus </a></td></tr>';
            $(html).insertBefore('#row-tambah-pilihan-ekstra');
            currentModifier.Pilihan.push(newPilihanRow);
            changeBisaTambahJumlahPilihan($('#checkBisaTambahJumlahPilihan'));
        });
        $('#btn-simpan-pilihan-ekstra').click(function () {

            var op = (selectedEkstraName == 'new' ? 'new' : 'exist');
            currentModifier.ModifierName = $('#namaEkstra').val();
            currentModifier.ChooseOnlyOne = $('#checkPilihSatu').is(':checked') ? 1 : 0;
            currentModifier.CanAddQuantity = $('#checkBisaTambahJumlahPilihan').is(':checked') ? 1 : 0;
//            if(op == 'new') {
//                currentModifier.Selected = true;
//            }
            currentModifier.Selected = true;
            var pilihans = [];
            $('.item-pilihan-ekstra').each(function (index, tr) {
                var inputNamaPilihan = tr.children[0].children;
                var inputHarga = tr.children[1].children[0].children[1];
                var inputQty = tr.children[2].children;
                var selectSatuan = tr.children[3].children;
                if ($(inputNamaPilihan).val() != '') {
                    pilihans.push({
                        "NamaPilihan": $(inputNamaPilihan).val(),
                        'oldName': currentModifier.Pilihan[index].NamaPilihan,
                        "Harga": $(inputHarga).val(),
                        "QtyDibutuhkan": $(inputQty).val(),
                        "Satuan": $(selectSatuan).val(),
                        "operation": $(tr).data('op'),
                        "modifierID": selectedEkstraName,
                        "realmodifierID": selectedEkstraID
                    });
                }

            });
            currentModifier.Pilihan = pilihans;
            if (!isCurrentModifierOnTheList) {
                listModifier.push(currentModifier);
            }
            renderModifierList();
            $(pilihanEkstraModal).modal('hide');
        });
        $('#hapusmodifier').click(function () {
            console.log(currentModifier);
            if (currentModifier.ModifierName != '') {
                ListModifierDeleted.push(currentModifier.ModifierName);
                listModifier = $.grep(listModifier, function (modifier) {
                    return modifier.ModifierName != currentModifier.ModifierName;
                });
                renderModifierList();
                $(pilihanEkstraModal).modal('hide');
            } else {
                alert('Nama modifier tidak boleh kosong');
            }
        });

    });
    function changeBisaTambahJumlahPilihan(caller) {
        var val = $(caller).is(':checked');
        if (val) {
            $('#headerBisaTambahJumlahPilihan').show();
            $('.kolomBisaTambahJumlahPilihan').children().show();
        } else {
            $('#headerBisaTambahJumlahPilihan').hide();
            $('.kolomBisaTambahJumlahPilihan').children().hide();
        }
    }

    function initDialogFormUI(modifier) {
        $('.item-pilihan-ekstra').remove();
        $('#namaEkstra').val(modifier.ModifierName);
        $('#namaEkstra').attr('placeholder', modifier.PlaceholderEsktra)
        if (modifier.ChooseOnlyOne == 1) {
            if (!$('#checkPilihSatu').is(':checked')) {
                $('#checkPilihSatu').click();
            }
        } else {
            if ($('#checkPilihSatu').is(':checked')) {
                $('#checkPilihSatu').click();
            }
        }
        if (modifier.CanAddQuantity == 1) {
            if (!$('#checkBisaTambahJumlahPilihan').is(':checked')) {
                $('#checkBisaTambahJumlahPilihan').click();
            }
        } else {
            if ($('#checkBisaTambahJumlahPilihan').is(':checked')) {
                $('#checkBisaTambahJumlahPilihan').click();
            }
        }


        for (var i = 0; i < modifier.Pilihan.length; i++) {

            var pilihan = modifier.Pilihan[i];
            var selectSatuan = '<select class="form-control">';
            selectSatuan += '<option value="" data-tag=""></option>';
            for (var x = 0; x < jsonSatuan.length; x++) {
                var selected = jsonSatuan[x].name == pilihan.Satuan ? 'selected' : '';
                selectSatuan += '<option value="' + jsonSatuan[x].name + '" data-tag="' + jsonSatuan[x].name + '"' + selected + '>' + jsonSatuan[x].name + '</option>';
            }

            selectSatuan += '</select>';

            var op = (selectedEkstraName == 'new' ? 'new' : 'exist');
            var html = '<tr class="item-pilihan-ekstra" data-op="' + op + '">';
            html += '<td><input type="text" class="form-control" placeholder="' + pilihan.PlaceholderPilihan + '"';
            html += 'value="' + pilihan.NamaPilihan + '" /></td>';
            html += '<td><div class="input-group"><span class="input-group-addon">Rp.</span><input type="text" class="form-control" placeholder="0"';
            html += ' value="' + (pilihan.Harga == 0 ? '' : pilihan.Harga) + '"';
            html += ' onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))"/></div></td>';
            html += '<td class="kolomBisaTambahJumlahPilihan"><input type="text" class="form-control" placeholder="0" value="' + pilihan.QtyDibutuhkan + '" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))"/></td>';
            html += '<td class="kolomBisaTambahJumlahPilihan"><input type="text" class="form-control" placeholder="misal: PCS"';
            html += 'value="' + pilihan.Satuan + '" /></td >';
            html += '<td> <a href = "#" class= "btn btn-danger" onclick = "hapusPilihanEkstra(this,\'' + pilihan.NamaPilihan + '\');" > Hapus </a></td></tr>';
            $(html).insertBefore('#row-tambah-pilihan-ekstra');


        }
        changeBisaTambahJumlahPilihan($('#checkBisaTambahJumlahPilihan'));

    }

    function getModifierFromListOrCreateNew(theName) {
        //search on collection
        for (var x = 0; x < listModifier.length; x++) {
            if (listModifier[x].ModifierName == theName) {
                isCurrentModifierOnTheList = true;
                return listModifier[x];
            }

        }
        isCurrentModifierOnTheList = false;
        var newOne = $.extend({}, newModifier);
        return newOne;

    }

    function setDialogID(name, id) {
        selectedEkstraName = name;
        selectedEkstraID = id;
    }

    function hapusPilihanEkstra(button, namaPilihan) {
        var tr = $(button).parent().parent();
        $(tr).remove();
    }

    function bindVal(obj, caller) {
        obj = $(caller).val();
    }

</script>