<script type="text/javascript">

    jQuery(document).ready(function($) {
        var multiple, termqty, termitems, termtotal, termcategory, getdiscounttype, getdiscountvalue, getitemqty,getitemid,termitemnames,termcategorynames,getitemname;

        var namapromo = $('#nama-promo');
        var jenis_promo = $('#jenis-promo');
        var datestart = $('#date-start');
        var dateend = $('#date-end');
        var jamstart = $('#promo-jam-mulai');
        var jamend = $('#promo-jam-selesai');
        var hari = $('#hari-promo');

        // Promo 1
        var p1jumlahitem = $('#p1-jumlah-item');
        var p1item = $('#p1-item');
        var p1diskontipe = $('#p1-diskon-promo-tipe');
        var p1diskonvalue = $('#p1-diskon-promo-value');

        // Promo 2
        var p2minharga = $('#p2-min-harga');
        var p2diskontipe = $('#p2-diskon-promo-tipe');
        var p2diskonvalue = $('#p2-diskon-promo-value');
        var p2multiply = $('#p2-multiply');

        // Promo 3
        var p3jumlahitembeli = $('#p3-jumlah-item-beli');
        var p3itembeli = $('#p3-item-beli');
        var p3freejumlahitem = $('#p3-free-jumlah-item');
        var p3freeitem = $('#p3-free-item');
        var p3multiply = $('#p3-multiply');

        $('select.select-hari-promo').select2();

        
            var modeX = "<?=$modeform?>";
            if (modeX === "edit") {
                $('#promo-jam-mulai').timepicker({
                    timeFormat: 'HH:mm',
                    interval: 1,
                    minTime: '00',
                    maxTime: '23:59',
                    defaultTime: "<?= $form['jammulai']; ?>",
                    startTime: '00:00',
                    dynamic: false,
                    dropdown: false,
                    scrollbar: false
                });

                $('#promo-jam-selesai').timepicker({
                    timeFormat: 'HH:mm',
                    interval: 1,
                    minTime: '00',
                    maxTime: '23:59',
                    defaultTime: "<?= $form['jamend']; ?>",
                    startTime: '00:00',
                    dynamic: false,
                    dropdown: false,
                    scrollbar: false
                });
            } else {
                $('#promo-jam-mulai').timepicker({
                    timeFormat: 'HH:mm',
                    interval: 1,
                    minTime: '00',
                    maxTime: '23:59',
                    defaultTime: "00:00",
                    startTime: '00:00',
                    dynamic: false,
                    dropdown: false,
                    scrollbar: false
                });

                $('#promo-jam-selesai').timepicker({
                    timeFormat: 'HH:mm',
                    interval: 1,
                    minTime: '00',
                    maxTime: '23:59',
                    defaultTime: "23:59",
                    startTime: '00:00',
                    dynamic: false,
                    dropdown: false,
                    scrollbar: false
                });
            }

        validateDiskon(p1diskonvalue,p1diskontipe);
        validateDiskon(p2diskonvalue,p2diskontipe);
        isiDiskon();

        applyMultiSelect([p1item, p3itembeli, p3freeitem]);

        cekPromo(jenis_promo.val(),$);

        jenis_promo.change(function() {
            cekPromo(jenis_promo.val(),$);
        });

        loadData();

        $('#btn-simpan-single-outlet').click(function() {
            if (!validate())
                return;

            var d = [0,0,0,0,0,0,0];
            var day = hari.val();
            for (var i = 0; i < day.length; i++) {
                var res = day[i].split("-");
                d[res[0]] = res[1];
            }

            $.post('<?=base_url('ajax/savemasterpromooutletsxxxx');?>',{
                'mode' : '<?= $modeform; ?>',
                'oldname' : '<?= $form['oldname']; ?>',
                'namapromo' : namapromo.val(),
                'idoutlet' : $('#outlet').val(),
                'jenispromo' : jenis_promo.val(),
                'datestart' : datestart.val(),
                'dateend' : dateend.val(),
                'jamstart' : jamstart.val(),
                'jamend' : jamend.val(),
                'hari' : d,
                'termqty' : termqty,
                'termitems' : termitems,
                'termcategory' : termcategory,
                'termtotal' : termtotal,
                'getdiscounttype' : getdiscounttype,
                'getdiscountvalue' : getdiscountvalue,
                'getitemqty' : getitemqty,
                'getitemid' : getitemid,
                'multiple' : multiple,
                'termitemnames' : termitemnames,
                'termcategorynames' : termcategorynames,
                'getitemname' : getitemname
            },function(data, status){
                var obj = JSON.parse(data);
                if (obj.msg == "OK") {
                    alert('Data Berhasil Disimpan');
                    window.location = '<?=base_url("promo/listpromo?outlet=" . $selected_outlet);?>';
                } else {
                    alert(obj.msg.replace("<br>", "\n").replace("<br>", "\n").replace("<br>", "\n"));
                }
            });
        });

        function cekPromo(jenis_promo) {
            p1item.multiselect('deselectAll', false).multiselect('refresh');
            p3itembeli.multiselect('deselectAll', false).multiselect('refresh');
            p3freeitem.multiselect('deselectAll', false).multiselect('refresh');

            switch (jenis_promo) {
                case '1':
                    $('#promo-1').css({
                        display: 'block'
                    });
                    $("#promo-2").css({
                        display: 'none'
                    });
                    $("#promo-3").css({
                        display: 'none'
                    });
                    break;
                case '2' :
                    $('#promo-2').css({
                        display: 'block'
                    });
                    $("#promo-1").css({
                        display: 'none'
                    });
                    $("#promo-3").css({
                        display: 'none'
                    });
                break;
                case '3':
                    $('#promo-3').css({
                        display: 'block'
                    });
                    $("#promo-2").css({
                        display: 'none'
                    });
                    $("#promo-1").css({
                        display: 'none'
                    });
                break;
                default:
            }
        }

        function isiDiskon() {
            if (<?= $form['jenispromo']; ?> == '1') {
                var cate = [];
                <?php foreach($category as $c) { ?>
                    cate.push('<?= $c->CategoryName; ?>');
                <?php } ?>
                var items = '<?= $form['termitems']; ?>';
                var res = items.split(",");
                for (var i = 0; i < cate.length; i++) {
                    var optgroup = $('#p1-item optgroup[label="'+cate[i]+'"]');
                    for (var j = 0; j < res.length; j++) {
                        var option = optgroup.find('option[value="'+res[j]+'"]');
                        option.attr('selected', true);
                    }
                }
            }

            if (<?= $form['jenispromo']; ?> == '3') {
                var cate = [];
                <?php foreach($category as $c) { ?>
                    cate.push('<?= $c->CategoryName; ?>');
                <?php } ?>
                var items = '<?= $form['termitems']; ?>';
                var res = items.split(",");
                for (var i = 0; i < cate.length; i++) {
                    var optgroup = $('#p3-item-beli optgroup[label="'+cate[i]+'"]');
                    for (var j = 0; j < res.length; j++) {
                        var option = optgroup.find('option[value="'+res[j]+'"]');
                        option.attr('selected', true);
                    }
                }
            }

            if (<?= $form['jenispromo']; ?> == '3') {
                var cate = [];
                <?php foreach($category as $c) { ?>
                    cate.push('<?= $c->CategoryName; ?>');
                <?php } ?>
                var items = '<?= $form['itemid']; ?>';
                var res = items.split(",");
                for (var i = 0; i < cate.length; i++) {
                    var optgroup = $('#p3-free-item optgroup[label="'+cate[i]+'"]');
                    for (var j = 0; j < res.length; j++) {
                        var option = optgroup.find('option[value="'+res[j]+'"]');
                        option.attr('selected', true);
                    }
                }
            }
        }

        function validateDiskon(value,tipe) {
            value.keydown(function() {
                if (tipe.val() == '1') {
                    if ($(this).val() > 100) {
                        alert('Value max. 100');
                        $(this).val('');
                    }
                } else{
                    if ($(this).val() > 100000000) {
                        alert('Value max 100.000.000');
                        $(this).val('');
                    }
                }
            });
        }

        function validate() {
            if (namapromo.val() == '') {
                alert('nama promo harus diisi!');
                namapromo.focus();
                return false;
            } else if (jamstart.val() == '') {
                alert('jam promo harus diisi!');
                jamstart.focus();
                return false;
            } else if (jamend.val() == '') {
                alert('jam promo harus diisi!');
                jamend.focus();
                return false;
            } else if (hari.val() == null) {
                alert('hari harus diisi!');
                hari.focus();
                hari.parent().addClass('ui-state-error')
                return false;
            } else {
                var js = jamstart.val().split(':');
                var je = jamend.val().split(':');
                if (parseInt(js[0]+js[1]) > 2400) {
                    alert('Jam promo tidak boleh melebihi 24:00');
                    jamstart.focus();
                    return false;
                } else if (parseInt(je[0]+je[1]) > 2400) {
                    alert('Jam promo tidak boleh melebihi 24:00');
                    jamend.focus();
                    return false;
                }
                if(jamstart.val() == jamend.val()) {
                    alert('Jam selesai promo tidak boleh sama dengan jam mulai promo');
                    jamend.focus();
                    return false;
                }
                else if(jamstart.val() > jamend.val()) {
                    alert('Jam selesai promo tidak bisa melewati tengah malam.\nSaran : Pecah promo menjadi dua.\nPromo pertama : '
                        + jamstart.val() + "-23:59\nPromo kedua : 00:00-" + jamend.val());
                    jamend.focus();
                    return false;
                }

                if (jenis_promo.val() == '1') {
                    termqty = p1jumlahitem.val();
                    var items = [];
                    p1item.find("option.item:selected").each(function() {
                        var el = $(this);
                        var selector = 'option.category[value="' + el.attr("ref").split(".").join("\\.") + '"]';
                        var elCategory = el.closest("select").find(selector);
                        if (elCategory.length > 0 && !elCategory.prop('selected')) {
                            var elval = el.val();
                            items.push(elval.replace('item',''));
                        }
                    });
                    //alert(termitemnames);
                    termitems = items.join(',');
                    getdiscounttype = p1diskontipe.val();
                    getdiscountvalue = p1diskonvalue.val();
                    var categories = [];
                    p1item.find("option.category:selected").each(function() {
                        categories.push($(this).val());
                    });
                    termcategory = categories.join(',');
                    if (termqty == '') {
                        alert("Jumlah item harus diisi!");
                        p1jumlahitem.focus();
                        return false;
                    } else if (termitems == '' && termcategory == '') {
                        alert('Produk tidak boleh kosong!');
                        p1item.focus();
                        return false;
                    } else if (getdiscountvalue == '') {
                        alert('Jumlah diskon harus diisi!');
                        p1diskonvalue.focus();
                        return false;
                    }
                    termtotal = 0;
                    getitemqty = 0;
                    getitemid = 0;
                    multiple = 0;
                } else if (jenis_promo.val() == '2') {
                    termtotal = p2minharga.val();
                    getdiscounttype = p2diskontipe.val();
                    getdiscountvalue = p2diskonvalue.val();
                    //multiple = p2multiply.val();
                    if(p2multiply.attr('checked')) {
                        multiple = "on";
                    } else {
                        multiple = "off";
                    }
                    if (termtotal == '') {
                        alert("Total transaksi harus diisi!");
                        p2minharga.focus();
                        return false;
                    } else if (getdiscountvalue == '') {
                        alert('Jumlah diskon harus diisi!');
                        p2diskonvalue.focus();
                        return false;
                    }
                    termcategory = "";
                    termitems = "";
                    getitemqty = 0;
                    getitemid = 0;
                    termqty = 0;
                } else if (jenis_promo.val() == '3') {
                    termqty = p3jumlahitembeli.val();
                    var items = [];
                    p3itembeli.find("option.item:selected").each(function() {
                        var el = $(this);
                        var selector = 'option.category[value="' + el.attr("ref").split(".").join("\\.") + '"]';
                        var elCategory = el.closest("select").find(selector);
                        if (elCategory.length > 0 && !elCategory.prop('selected')) {
                            var elval = el.val();
                            items.push(elval.replace('item',''));
                        }
                    });
                    termitems = items.join(',');
                    var categories = [];
                    p3itembeli.find("option.category:selected").each(function() {
                        categories.push($(this).val());
                    });
                    termcategory = categories.join(',');
                    getitemqty = p3freejumlahitem.val();
                    getitemid = p3freeitem.val();
                    //multiple = p3multiply.val();
                    if(p3multiply.attr('checked')) {
                        multiple = "on";
                    } else {
                        multiple = "off";
                    }
                    if (termqty == '') {
                        alert("Jumlah item yang dibeli harus diisi!");
                        p3jumlahitembeli.focus();
                        return false;
                    } else if (termitems == '' && termcategory == '') {
                        alert('Item yang dibeli harus dipilih!');
                        p3itembeli.focus();
                        return false;
                    } else if (getitemqty == '') {
                        alert('Produk tidak boleh kosong!');
                        p3freejumlahitem.focus();
                        return false;
                    } else if (getitemid == '') {
                        alert('Item yang didapatkan harus diisi!');
                        p3freeitem.focus();
                        return false;
                    }
                    termtotal = 0;
                    getdiscounttype = 0;
                    getdiscountvalue = 0;
                }
            }

            return true;
        }

        function loadData() {
            var mode = "<?=$modeform?>";
            if (mode === "edit") {
                if (jenis_promo.val() == '1') {
                    var termCategories = "<?=$form['termcategories']?>";
                    var termItems = "<?=$form['termitems']?>";

                    var categories = termCategories.split(',');
                    if (categories.length > 0) {
                        for (var i = 0; i < categories.length; i++) {
                            var c = categories[i];
                            c = c.split(".").join("\\.");
                            p1item.find("option.category[value='" + c + "']").each(function() {
                                $(this).prop('selected', true); 
                            });
                            p1item.find("option.item[ref='" + c + "']").each(function() {
                                $(this).prop('selected', true); 
                            });
                        }
                    }
                    var items = termItems.split(',');
                    if (items.length > 0) {
                        for (var i = 0; i < items.length; i++) {
                            var c = items[i];
                            c = c.split(".").join("\\.");
                            p1item.find("option.item[value='item" + c + "']").each(function() {
                                $(this).prop('selected', true); 
                            });
                        }
                    }

                    p1item.multiselect('refresh');
                    
                    p1jumlahitem.val("<?=$form['termqty']?>");
                } else if (jenis_promo.val() == '3') {
                    var termCategories = "<?=$form['termcategories']?>";
                    var termItems = "<?=$form['termitems']?>";

                    var categories = termCategories.split(',');
                    if (categories.length > 0) {
                        for (var i = 0; i < categories.length; i++) {
                            var c = categories[i];
                            c = c.split(".").join("\\.");
                            p3itembeli.find("option.category[value='" + c + "']").each(function() {
                                $(this).prop('selected', true); 
                            });
                            p3itembeli.find("option.item[ref='" + c + "']").each(function() {
                                $(this).prop('selected', true); 
                            });
                        }
                    }
                    var items = termItems.split(',');
                    if (items.length > 0) {
                        for (var i = 0; i < items.length; i++) {
                            var c = items[i];
                            c = c.split(".").join("\\.");
                            p3itembeli.find("option.item[value='item" + c + "']").each(function() {
                                $(this).prop('selected', true); 
                            });
                        }
                    }

                    p3itembeli.multiselect('refresh');


                    var itemid = "<?=$form['itemid']?>";
                    p3freeitem.find("option[value='" + itemid + "']").prop('selected', true);
                    p3freeitem.multiselect('refresh');

                    p3jumlahitembeli.val("<?=$form['termqty']?>");
                    p3freejumlahitem.val("<?=$form['itemqty']?>");
                    
                }
            }
        }

        function applyMultiSelect(arr) {
            for (var i = 0; i < arr.length; i++) {
                var input = arr[i];
                input.multiselect({
                    enableClickableOptGroups: true,
                    enableFiltering: true,
                    nonSelectedText : '--- Pilih Produk ---',
                    buttonText: function(options, select){
                        if (options.length === 0) {
                            return '--- Pilih Produk ---';
                        } else {
                            var labels = [];
                            options.each(function() {
                                if ($(this).attr('label') !== undefined) {
                                    labels.push($(this).attr('label'));
                                }
                                else {
                                    labels.push($(this).html());;
                                }
                            });
                            if (labels.length < 5)
                                return labels.join(' / ') + ' ';
                            
                            return labels.length + " selected";
                        }
                    },
                    onChange: function(element, checked) {
                        var el = $(element);
                        if (el.hasClass('category')) {
                            var selector = 'option.item[ref="' + el.val().split(".").join("\\.") + '"]';
                            var select = el.closest("select");
                            select.find(selector).each(function() {
                                $(this).prop('selected', checked); 
                            });
                            select.multiselect('refresh');   
                        } else if (el.hasClass('item')) {
                            var ref = el.attr('ref');
                            var selector = 'option.category[value="' + ref.split(".").join("\\.") + '"]';
                            var select = el.closest("select");
                            select.find(selector).each(function() {
                                if ($(this).prop('selected')) {
                                    $(this).prop('selected', false);
                                }
                            });
                            select.multiselect('refresh');
                        }
                    }
                });
            }
        }

    });
</script>
