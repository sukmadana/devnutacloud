<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="/js/datatables.custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

<script>
    jQuery(function($) {
        var selectedOutlet = <?=$selected_outlet?>;

        var ajax = {
            url : window.base_url + 'perusahaan/ajaxusertablet',
            data: function(d) {
                d.outlet = selectedOutlet;
            },
            type: "post",
            dataType: "json",
            error: function(){  // error handling
                alert("Cannot fetch data user");
            }
        };

        var _link = document.createElement( 'a' );
        var _relToAbs = function( el ) {
            var url;
            var clone = $(el).clone()[0];
            var linkHost;

            if ( clone.nodeName.toLowerCase() === 'link' ) {
                _link.href = clone.href;
                linkHost = _link.host;

                // IE doesn't have a trailing slash on the host
                // Chrome has it on the pathname
                if ( linkHost.indexOf('/') === -1 && _link.pathname.indexOf('/') !== 0) {
                    linkHost += '/';
                }

                clone.href = _link.protocol+"//"+linkHost+_link.pathname+_link.search;
            }

            return clone.outerHTML;
        };

        var customizeData = function (cdt, data ) {
            if (cdt === undefined)
                return;
            var order = cdt.order();
            var ctrOrder = 0;
            var fd = new FormData();

            fd.append('outlet', selectedOutlet);

            for (var i=0; i<order.length; i++) {
                fd.append( 'order[' + ctrOrder + '][column]', order[0][0] );
                fd.append( 'order[' + ctrOrder + '][dir]', order[0][1] );
                ctrOrder++;
            }

            var tmp = jQuery.extend(true, {}, ajax);
            tmp.data = fd;
            tmp.processData = false;
            tmp.contentType = false;
            tmp.async = false;

            $.ajax(tmp).done(function(r) {
                data.header = ["Nama", "Email", "No. HP", "Tgl Lahir", "Alamat"];
                data.body = _.map(r.data, function(v,k) { return _.values(_.omit(v, 'CustomerID', 'Varian', 'DeviceID', 'PerusahaanNo', 'DeviceNo')) });
            });
        }

        $("#outlet").bind("change", function() {
            selectedOutlet = $(this).val();
            cdt.ajax.reload();
        });

        $("#btnTambah").bind("click", function() {
            <?php if($visibilityMenu['CustomerAdd']) { ?>
            if (selectedOutlet === undefined || selectedOutlet === 0) {
                return alert('Pilih outlet terlebih dahulu.');
            }

            $('#pelanggan-add').submit();
            <?php } ?>
        });

        var cdt = $('#grid-item').DataTable( {
            dom: "<'row tb5'<'col-sm-12'B'>><'row'>" +
                "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
                {
                    extend: 'csvHtml5',
                    customizeData: customizeData,
                    title: 'Daftar User'
                }, {
                    extend: 'excelHtml5',
                    customizeData: customizeData,
                    title: 'Daftar User'
                }, {
                    extend: 'pdfHtml5',
                    customizeData: customizeData,
                    title: 'Daftar User'
                },
                {
                    text: 'Print',
                    action: function (e, dt, button, config)
                    {
                        var that = this;

                        that.processing( true );

                        var data = dt.buttons.exportData( config.exportOptions );
                        var addRow = function ( d, tag ) {
                            var str = '<tr>';

                            for(var key in d) {
                                var dKey = d[key];
                                if (key === "CustomerID" || key === "Varian" || key === "DeviceID" || key === "PerusahaanNo" || key === "DeviceNo")
                                    continue;
                                if (dKey === "ID" || dKey === "Varian" || dKey === "DeviceID" || dKey === "PerusahaanNo" || key === "DeviceNo")
                                    continue;
                                str += '<'+tag+'>'+dKey+'</'+tag+'>';
                            }

                            return str + '</tr>';
                        };

                        var html = '<table class="'+dt.table().node().className+'">';
                        html += '<thead>'+ addRow( _.reject(data.header, function(d) { return d === "Edit/Delete"; }), 'th' ) +'</thead>';

                        var order = dt.order();
                        var ctrOrder = 0;
                        var fd = new FormData();

                        fd.append("outlet", selectedOutlet);

                        for (var i=0; i<order.length; i++) {
                            fd.append( 'order[' + ctrOrder + '][column]', order[0][0] );
                            fd.append( 'order[' + ctrOrder + '][dir]', order[0][1] );
                            ctrOrder++;
                        }

                        var tmp = jQuery.extend(true, {}, ajax);
                        tmp.data = fd;
                        tmp.processData = false;
                        tmp.contentType = false;

                        $.ajax(tmp).done(function(r) {
                            html += '<tbody>';
                            for(var key in r.data) {
                                var value = r.data[key];
                                html += addRow( value, 'td' );
                            }
                            html += '</tbody>';

                            // Open a new window for the printable table
		                    var win = window.open( '', '' );
                            var title = config.title === undefined ? "" : config.title;

                            if ( typeof title === 'function' ) {
                                title = title();
                            }

                            if ( title.indexOf( '*' ) !== -1 ) {
                                title= title.replace( '*', $('title').text() );
                            }

                            var head = '<title>'+title+'</title>';
                            $('style, link').each( function () {
                                head += _relToAbs( this );
                            } );

                            //$(win.document.head).html( head );
                            win.document.head.innerHTML = head; // Work around for Edge
                            win.document.body.innerHTML = html;

                            that.processing( false );
                        });
                    }
                }
            ],
            "columns": [
                {"data": "CustomerName"},
                {"data": "CustomerEmail"},
                {"data": "CustomerPhone"},
                {"data": "Birthday"},
                {"data": "CustomerAddress"},
                <?php if ($visibilityMenu['CustomerEdit'] || $visibilityMenu['CustomerDelete']) { ?>
                {
                    data: null,
                    className: "center",
                    render: function(o) {
                        var str = '';
                        str += '<a target="_blank" href="<?=base_url('laporan/riwayatpelanggan')?>?customer=' + o.CustomerID + '.' + o.DeviceNo + '&outlet=' + selectedOutlet + '&devno=' + o.DeviceNo + '&usedate=0"><button class="btn btn-default">Lihat Riwayat</button></a>&nbsp;';
                        <?php if ($visibilityMenu['CustomerEdit']) {?>
                        str += '<a href="<?=base_url('pelanggan/form')?>?id=' + o.CustomerID + '&outlet=' + selectedOutlet + '&devno=' + o.DeviceNo + '"><button class="btn btn-default">Edit</button></a>&nbsp;';
                        <?php } ?>
                        <?php if ($visibilityMenu['CustomerDelete']) {?>
                        str += '<button class="btn btn-default btnDelete" p1="' + o.CustomerName + '" p2="' + selectedOutlet + '">Delete</button>';
                        <?php } ?>
                        return str;
                    }
                }
                <?php } ?>
            ],
            "order": [[ 1, "ASC" ]],
            "processing": true,
            "serverSide": true,
            "paging": true,
            //"pageLength": 8,
            "ajax": ajax
        } );

        cdt.on("draw", function() {
            $(".btnDelete").bind("click", function() {
                if (confirm("Hapus pelanggan ini? jika ya dihapus, jika tidak biarkan.")) {
                    var btnDelete = $(this);

                    var fd = new FormData();
                    fd.append("nama", btnDelete.attr("p1"))
                    fd.append("outlets", btnDelete.attr("p2"));

                    $.ajax({
                        url : window.base_url + 'pelanggan/deletebyname',
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: "post",
                        dataType: "json",
                        success: function(json) {
                            if (json.status === true) {
                                cdt.ajax.reload();
                            } else {
                                alert(json.message);
                            }
                        },
                        error: function(err) {  // error handling
                            alert("Cannot fetch data pelanggan");
                        }
                    });
                }


            })
        });
    });
</script>
