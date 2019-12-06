<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.16/datatables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.2.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="/js/datatables.custom.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.5/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="/js/switchery.js"></script>

<script>
    jQuery(function($) {
        var ajax = {
            url : window.base_url + 'perusahaan/ajaxgetperangkat',
            data: function(d) {},
            type: "post",
            dataType: "json",
            error: function(){  // error handling
                alert("Cannot fetch data perangkat");
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
                data.header = ["Outlet ID", "Nama Outlet", "Alamat Outlet", "Device No", "Terakhir dipakai", "Tindakan"];
                data.body = _.map(r.data, function(v,k) { return _.values(_.omit(v, 'OutletID', 'NamaOutlet', 'AlamatOutlet', 'DeviceNo', 'TerakhirDipakai', 'IsActive')) });
            });
        }
        
        var cdt = $('#grid-item').DataTable( {
            dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "columns": [
                {"data": "NamaOutlet"},
                {"data": "AlamatOutlet"},
                {"data": "DeviceNo"},
                {"data": "TerakhirDipakai"},
                {"data": "IsActive", "render": function(dt, type, row, meta) {
                    return "<input PerusahaanNo='" + row.PerusahaanNo + "' OutletID='" + row.OutletID + "' DeviceNo='" + row.DeviceNo + "' type='checkbox' class='switch-small'" + (dt === "1" ? "checked" : "") + " />";
                }},
            ],
            "order": [[ 0, "ASC" ], [ 2, "ASC" ]],
            "processing": true,
            "serverSide": true,
            "paging": true,
            //"pageLength": 8,
            "ajax": ajax,
            "drawCallback": function( settings ) {
                var sw_small = Array.prototype.slice.call($('#grid-item .switch-small'));
                sw_small.forEach(function (html) {
                    var switchery = new Switchery(html, {
                        size: 'small',
                        color: '#66bb6a',
                        jackColor: '#fff',
                        secondaryColor: '#eee',
                        jackSecondaryColor: '#fff'
                    });
                });

                $('#grid-item .switch-small').unbind("change").bind("change", function() {
                    var perusahaanNo = $(this).attr("PerusahaanNo");
                    var outletID = $(this).attr("OutletID");
                    var deviceNo = $(this).attr("DeviceNo");
                    var isActive = $(this).prop("checked");

                    $.post(window.base_url + 'perusahaan/ajaxtindakan', {
                        PerusahaanNo: perusahaanNo,
                        OutletID: outletID,
                        DeviceNo: deviceNo,
                        IsActive: isActive,
                    }, function() {
                        cdt.ajax.reload();
                    });
                });
            }
        } );

        cdt.on("draw", function() {
            // $(".btnDelete").bind("click", function() {
            //     if (confirm("Hapus pelanggan ini? jika ya dihapus, jika tidak biarkan.")) {
            //         var btnDelete = $(this);

            //         var fd = new FormData();
            //         fd.append("nama", btnDelete.attr("p1"))
            //         fd.append("outlets", btnDelete.attr("p2"));
                    
            //         $.ajax({
            //             url : window.base_url + 'pelanggan/deletebyname',
            //             data: fd,
            //             processData: false,
            //             contentType: false,
            //             type: "post",
            //             dataType: "json",
            //             success: function(json) {
            //                 if (json.status === true) {
            //                     cdt.ajax.reload();
            //                 } else {
            //                     alert(json.message);
            //                 }
            //             },
            //             error: function(err) {  // error handling
            //                 alert("Cannot fetch data pelanggan");
            //             }
            //         });
            //     }


            // })
        });
    });
</script>