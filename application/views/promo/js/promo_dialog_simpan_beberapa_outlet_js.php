<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var token = ''
        const messaging = firebase.messaging();
        const tokenDivId = 'token_div';
        const permissionDivId = 'permission_div';
        document.getElementById('myProgress').style.display = "none";
        document.getElementById('outletzz').style.display = "none";
        messaging.onTokenRefresh(function() {
            messaging.getToken().then(function(refreshedToken) {
                setTokenSentToServer(false);
                sendTokenToServer(refreshedToken);
                showToken(refreshedToken)
                resetUI();
            }).catch(function(err) {
                showToken('Unable to retrieve refreshed token ', err);
            });
        });
        messaging.onMessage(function(payload) {
            var notif = payload.notification.body
            if (typeof notif === 'string') {
                notif = JSON.parse(notif)
            }
            if (notif.prosentase == 0) {
                $('#btn-simpan-promo-modal .load').show();
                $('#btn-simpan-promo-modal').attr('disabled', 'disabled');
            }
            document.getElementById('outletzz').innerText = notif.firebasebody;
            document.getElementById("myBar").style.width = notif.prosentase + '%';
        });
        messaging.requestPermission()
            .then(function() {
                resetUI()
            })
            .catch(function(err) {
                console.log('Unable to get permission to notify. ', err);
            });

        function resetUI() {
            messaging.getToken().then(function(currentToken) {
                if (currentToken) {
                    sendTokenToServer(currentToken);

                } else {
                    setTokenSentToServer(false);
                }
            }).catch(function(err) {
                setTokenSentToServer(false);
            });
        }
        function showToken(currentToken) {
            token = currentToken
        }
        function sendTokenToServer(currentToken) {
            token = currentToken
            if (!isTokenSentToServer()) {
                console.log('Sending token to server...');
                setTokenSentToServer(true);
            }
        }

        function isTokenSentToServer() {
            return window.localStorage.getItem('sentToServer') === '1';
        }

        function setTokenSentToServer(sent) {
            window.localStorage.setItem('sentToServer', sent ? '1' : '0');
        }

        function requestPermission() {
            messaging.requestPermission().then(function() {
                resetUI();
            }).catch(function(err) {
                console.log('Unable to get permission to notify.', err);
            });
        }

        function deleteToken() {
            messaging.getToken().then(function(currentToken) {
                messaging.deleteToken(currentToken).then(function() {
                    setTokenSentToServer(false);
                    resetUI();
                }).catch(function(err) {
                    console.log('Unable to delete token. ', err);
                });
            }).catch(function(err) {
                console.log('Error retrieving Instance ID token. ', err);
                showToken('Error retrieving Instance ID token. ', err);
            });

        }
        resetUI();



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

        $('#all-simpan-promo-selected').change(function () {
            var selected = $(this).prop('checked');

            $('.all-simpan-promo-item').prop('checked', selected);


        });

        $('#btn-simpan-promo-modal').click(function() {
            if (namapromo.val() == '') {
                alert('nama promo harus diisi!');
                namapromo.focus();
                return;
            } else if (jamstart.val() == '') {
                alert('jam promo harus diisi!');
                jamstart.focus();
                return;
            } else if (jamend.val() == '') {
                alert('jam promo harus diisi!');
                jamend.focus();
                return;
            } else if (hari.val() == null) {
                alert('hari harus diisi!');
                hari.focus();
                return;
            } 
                var js = jamstart.val().split(':');
                var je = jamend.val().split(':');
                if (parseInt(js[0]+js[1]) > 2400) {
                    alert('Jam promo tidak boleh melebihi 24:00');
                    jamstart.focus();
                    return;
                } else if (parseInt(je[0]+je[1]) > 2400) {
                    alert('Jam promo tidak boleh melebihi 24:00');
                    jamend.focus();
                    return;
                }
                if(jamstart.val() == jamend.val()) {
                    alert('Jam selesai promo tidak boleh sama dengan jam mulai promo');
                    jamend.focus();
                    return;
                }
                else if(jamstart.val() > jamend.val()) {
                    alert('Jam selesai promo tidak bisa melewati tengah malam.\nSaran : Pecah promo menjadi dua.\nPromo pertama : '
                        + jamstart.val() + "-23:59\nPromo kedua : 00:00-" + jamend.val());
                    jamend.focus();
                    return;
                }
            var selectedOutlets = $('.all-simpan-promo-item:checked');
            var idoutletsSaveItem = [];

            for (var a = 0; a < selectedOutlets.length; a++) {
                var outlet = selectedOutlets[a];
                var tag = $(outlet).data('tag').split('#@#');
                var idoutlet = tag[0];
                var nama = tag[1];
                idoutletsSaveItem[a] = idoutlet;
            }

            var d = [0,0,0,0,0,0,0];
            var day = hari.val();
            var multiple, termqty, termitems, termtotal, termcategory, getdiscounttype, getdiscountvalue, getitemqty,getitemid,termitemnames,termcategorynames,getitemname;
            if (jenis_promo.val() == '1') {
                termqty = p1jumlahitem.val();
                var items = [];
                termitemnames = [];
                p1item.find("option.item:selected").each(function() {
                    var el = $(this);
                    var selector = 'option.category[value="' + el.attr("ref").split(".").join("\\.") + '"]';
                    var elCategory = el.closest("select").find(selector);
                    if (elCategory.length > 0 && !elCategory.prop('selected')) {
                        var elval = el.val();
                        items.push(elval.replace('item',''));  
                        termitemnames.push(el.text());
                    }
                });
                termitems = items.join(',');
                getdiscounttype = p1diskontipe.val();
                getdiscountvalue = p1diskonvalue.val();
                var categories = [];
                    termcategorynames = [];
                p1item.find("option.category:selected").each(function() {
                    categories.push($(this).val());
                        termcategorynames.push($(this).text());
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
                if(p2multiply.attr('checked')) {
                    multiple = "on";
                } else {
                    multiple = "off";
                }
                if (termtotal == '') {
                    alert("Total transaksi harus diisi!");
                    p2minharga.focus();
                    return;
                } else if (getdiscountvalue == '') {
                    alert('Jumlah diskon harus diisi!');
                    p2diskonvalue.focus();
                    return;
                }
                termcategory = "";
                termitems = "";
                getitemqty = 0;
                getitemid = 0;
                termqty = 0;
            } else if (jenis_promo.val() == '3') {
                termqty = p3jumlahitembeli.val();
                var items = [];
                termitemnames = [];
                p3itembeli.find("option.item:selected").each(function() {
                    var el = $(this);
                    var selector = 'option.category[value="' + el.attr("ref").split(".").join("\\.") + '"]';
                    var elCategory = el.closest("select").find(selector);
                    if (elCategory.length > 0 && !elCategory.prop('selected')) {
                        var elval = el.val();
                        items.push(elval.replace('item',''));
                        termitemnames.push(el.text());
                    }
                });
                termitems = items.join(',');
                getitemqty = p3freejumlahitem.val();
                getitemid = p3freeitem.val();
                getitemname = "";
                p3freeitem.find("option:selected").each(function() {
                    getitemname = $(this).text();
                });
                if(p3multiply.attr('checked')) {
                    multiple = "on";
                } else {
                    multiple = "off";
                }
                var categories = [];
                termcategorynames = [];
                p3itembeli.find("option.category:selected").each(function() {
                    categories.push($(this).val());
                    termcategorynames.push($(this).text());
                });
                termcategory = categories.join(',');
                if (termqty == '') {
                    alert("Jumlah item yang dibeli harus diisi!");
                    p3jumlahitembeli.focus();
                    return false;
                } else if (termitems == '' && termcategory == '') {
                    alert('Produk tidak boleh kosong!');
                    p3itembeli.focus();
                    return false;
                } else if (getitemqty == '') {
                    alert('Jumlah item yang didapatkan harus diisi!');
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

            for (var i = 0; i < day.length; i++) {
                var res = day[i].split("-");
                d[res[0]] = res[1];
            }


        	document.getElementById('myProgress').style.display = "block";
        	document.getElementById('outletzz').style.display = "block";

            // cekProgres('aaa');
            $.post('<?=base_url('ajax/savemasterpromooutletsxxxx');?>',{
                'mode' : '<?= $modeform; ?>',
                'oldname' : '<?= $form['oldname']; ?>',
                'namapromo' : namapromo.val(),
                'idoutlet' : idoutletsSaveItem,
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
                'getitemname' : getitemname,
                'token': token
            },function(data, status){
                var obj = JSON.parse(data);
                if (obj.msg == "OK") {
                    alert('Data Berhasil Disimpan');
                    window.location = '<?=base_url("promo/listpromo?outlet=" . $selected_outlet);?>';
                } else {
            		document.getElementById('outletzz').innerText = "";
            		document.getElementById("myBar").style.width = '0%';
        			document.getElementById('myProgress').style.display = "none";
        			document.getElementById('outletzz').style.display = "none";
                	$('#btn-simpan-promo-modal .load').hide();
            		$('#btn-simpan-promo-modal').prop('disabled', false);
                    alert(obj.msg.replace("<br>", "\n").replace("<br>", "\n").replace("<br>", "\n"));
                }
            });
        });
    });
</script>
