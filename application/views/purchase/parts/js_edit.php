<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/css/jquery.datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/css/chosen.min.css">
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
<script src="<?php echo base_url(); ?>/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript">

    var $=jQuery.noConflict();


    var items = <?= json_encode($items) ?>;
    var id = <?= count($purchase_detail) ?>;;
    var elements;
    var row_items = 0;
	
	var table = $("#dynamic-table");
	$(".selectpicker").select2();
 

    function resetElement() {
        elements = {
            root: $("#compiling-form"),
            tr: $("<tr></tr>"),
            td: $("<td></td>"),
            select: $("<select data-show-subtext=\"true\" data-live-search=\"true\" data-placeholder=\"Pilih item...\"></select>").addClass('form-control').addClass('selectpicker').attr('name', 'item-name[]'),
            input: $('<div class="input-group">'+"<input type=\"number\" step=\".0001\" name=\"item-total[]\" class=\"form-control\" min=\"0\">"+'<div class="input-group-addon satuan"></div></div>'),
            button: $("<button type=\"button\" class=\"btn btn-default\"><span class=\"glyphicon glyphicon-trash\" aria-hidden=\"true\"></span></button>"),
            price: $("<input type=\"number\" name=\"price[]\" class=\"form-control\" min=\"0\" step=\"0.01\" required>"),
            diskon: $(
                '<div class="row">'
                +'<div style="padding-right:3px;width: calc(100% - 97px);float:left;margin-left:15px">'
                +"<input type=\"number\" name=\"diskon[]\" class=\"form-control\" min=\"0\">"
                +'</div>'
                +'<div style="padding-left:3px;width:67px;float:right;margin-right:15px">'
                +"<select class=\"form-control\" name=\"jenis_diskon[]\"><option value=\"%\">%</option><option value=\"Rp\">Rp</option></select>"
                +'</div>'
                +'</div>'
            ),
            sum: $("<input type=\"text\" name=\"sum[]\" class=\"form-control\" readonly min=\"0\"  required>"),
            detailid: $("<input type=\"text\" name=\"detailid[]\" value=\"0\" class=\"form-control\" >"),
            detaildevno: $("<input type=\"text\" name=\"detaildevno[]\" value=\"0\" class=\"form-control\" >"),
            selectPembayaran: $("#pembayaran"),
        }
    }

    function getPrice(itemId) {
        for (key in items) {
            var keyitem = items[key].ItemID + '.' + items[key].DeviceNo;
            //console.log(items[key].ItemID == itemId);
            if (keyitem == itemId) {
                return items[key].PurchasePrice
            }
        }
        return 33;
    }

    function getUnit(itemId) {
        for (key in items) {
            var keyitem = items[key].ItemID + '.' + items[key].DeviceNo;
            //console.log(items[key].ItemID == itemId);
            if (keyitem == itemId) {
                return items[key].Unit
            }
        }
        return 33;
    }

    function preLoad(selected_item = 1, total = 1) {
        resetElement();
        elements.tr.attr('data-id', ++id);
        elements.button.on('click', {targetId: id}, function (e) {
            $("tr[data-id=" + e.data.targetId + "]").detach();
            row_items--;
        });

        var optionKosong = $("<option></option>");
        optionKosong.appendTo(elements.select);
        
        $.each(items, function (key, val) {
            var keyitem = val.ItemID + '.' + val.DeviceNo;
            var option = $("<option>" + val.ItemName + "</option>").attr('value', keyitem);
            if (selected_item == keyitem) {
                option.attr('selected', '');
            }
            option.appendTo(elements.select);

            elements.select.on('change', function () {
                $('#price-' + $(this).data('id')).val(getPrice($(this).val()));
                $('#total-' + $(this).data('id')).val($('#price-' + $(this).data('id')).val() * $('.input-value[data-id=' + $(this).data('id') + ']').val());
                $("#jumlah-"+$(this).data('id')).find(".satuan").text(getUnit($(this).val()));
            });
        });

        // elements.input.on('change', function () {
        //     hitung_subtotal($(this).data('id'));
        // });





        var prepend_input = '<div class="input-group">';
        var append_input = '<div id="unit-'+ id +'" class="input-group-addon satuan"></div></div>';

        //console.log(elements.input.attr('value', total));


        $("<td></td>").append(elements.select.attr('data-id', id)).appendTo(elements.tr);
        $("<td></td>").append(
            elements.input
                .attr('value', total)
                .addClass('input-value')
                .attr('data-id', id)
        )
            .attr('id','jumlah-'+id)
            .appendTo(elements.tr);
        $("<td></td>").append(elements.price.attr('id', 'price-' + id)).appendTo(elements.tr);
        $("<td></td>").append(elements.diskon.attr('id', 'diskon-' + id)).appendTo(elements.tr);
        $("<td></td>").append(elements.sum.attr('id', 'total-' + id)).appendTo(elements.tr);
        $("<td style=\"display: none\"></td>").append(elements.detailid.attr('id', 'detailid-' + id)).appendTo(elements.tr);
        $("<td style=\"display: none\"></td>").append(elements.detaildevno.attr('id', 'detaildevno-' + id)).appendTo(elements.tr);
        $("<td></td>").append(elements.button).appendTo(elements.tr);


        row_items++;
    }

    function hitung_subtotal(id){
        console.log(id);
        var hitung_error = 0;
        var total_diskon = 0;
        var jenis_diskon = $("#diskon-"+id).find("select").val();
        var diskon_value = parseFloat($("#diskon-"+id).find("input").val());

        var harga_awal = parseFloat($('#price-' + id).val());
        console.log('#price-' + id);
        console.log(harga_awal);

        if(diskon_value){

            if (jenis_diskon=="%") {
                if (diskon_value > 100) {
                    set_alert('Dskon tidak boleh lebih dari 100% atau ganti terlebih dahulu jenis diskonnya menjadi rupiah', id);
                    hitung_error = 1;
                } else {
                    harga_awal -= harga_awal * diskon_value / 100;
                    hitung_error = 0;
                }
            } else if (jenis_diskon=="Rp") {
                set_alert('', id, false);
                hitung_error = 0;
                total_diskon = diskon_value;
            }
        }

        var harga = harga_awal - total_diskon;
        var quantity = parseFloat($("#jumlah-"+id).find('input').val());

        var subtotal = (harga * quantity).toFixed(4);

        if (hitung_error == 0) {
            $('#total-' + id).val(parseFloat(subtotal));
        }



        //hitung grandtotal
        var grandtotal = 0;
        $.each($("#compiling-form").find("input[name='sum[]']"), function() {
            grandtotal+=parseFloat($(this).val());
        });

        //diskon final
        var diskon_final = 0;
        var diskon_final_value = parseFloat($("input[name='diskon_final']").val());
        var jenis_diskon_final = $("select[name='jenis_diskon_final']").val();
        var total_diskon_final = 0;

        if (diskon_final_value) {
            if (jenis_diskon_final=="%") {
                if (diskon_final_value>100) {
                    set_alert('Dskon tidak boleh lebih dari 100% atau ganti terlebih dahulu jenis diskonnya menjadi rupiah', id);
                    hitung_error = 1;
                } else {
                    total_diskon_final = grandtotal * diskon_final_value / 100;
                    hitung_error = 0;
                }

            } else if (jenis_diskon_final=="Rp") {
                set_alert('', id, false);
                total_diskon_final = diskon_final_value;
                hitung_error = 0;
            }
        }

        //set value
        grandtotal -= total_diskon_final;
        if(hitung_error == 0){
            $("input[name='grand-total']").val(grandtotal);
            $("input[name='grand-total2']").val(grandtotal);
        }
    }

    function set_alert(message,row_id,status=true){
        if (status) {
            $("#alert-box .alert").text(message);
            $("#alert-box").show();
            // $("tr[data-id='"+id+"']").addClass("warning");
        } else {
            $("#alert-box .alert").text("");
            $("#alert-box").hide();
        }
    }

    // diskon final onchange
    $("input[name='diskon_final'], select[name='jenis_diskon_final']").change(function(){
        $.each($("#compiling-form tr"), function() {
            var data_id = $(this).attr('data-id');
            // console.log(data_id);
            hitung_subtotal( data_id );
        });
    });

    function onchange_item(){
        var item_selector = "select[name='item-name[]']";
        var price_selector = "input[name='price[]']";
        var qty_selector = "input[name='item-total[]']";
        var diskon_selector = "input[name='diskon[]']";
        var jenis_diskon_selector = "select[name='jenis_diskon[]']";

        var selector_row = [item_selector,price_selector,qty_selector,diskon_selector,jenis_diskon_selector];

        $.each(selector_row, function() {
            var this_selector = this;
            $("#compiling-form "+this_selector).change(function(){
                var row_id = $(this).closest("tr").attr('data-id');
                // console.log($(this).val());.
                hitung_subtotal( row_id );
            });
        });
    }

    function purcaseLoad() {
        resetElement();
        elements.selectPembayaran.bind('change', function () {
            var pembayaran = $(this).val();
            if (pembayaran == "") {
                $('#paymentContainer').html("");
            } else {
                $('#paymentContainer').html($("#" + pembayaran).html());

                if (pembayaran == 'kartu') {
                    var totalKartu = purchase.totalPayment();
                    $('#belanjakartu').val(totalKartu);
                    console.log(totalKartu);
                } else if (pembayaran == 'campuran') {
                    var totalCampuran = purchase.totalPayment();
                    $('#belanjacampuran').val(totalCampuran);
                }
            }
        });

    }

    function render_row(selected_item = 0, total = 0) {
        preLoad(selected_item, total);

        elements.tr.appendTo(elements.root);
        onchange_item();
        $.each($("#compiling-form tr select"), function() {
			$(this).select2();
		});
    }

    function render_rowawal(selected_item = 0, total = 0) {
//        preLoad(selected_item, total);
//
//        elements.tr.appendTo(elements.root);
        $.each($("#compiling-form tr"), function() {
            var data_id = $(this).attr('data-id');
            // console.log(data_id);
            hitung_subtotal( data_id );
        });
        onchange_item();
//        $.each($("#compiling-form tr"), function() {
//			hitung_subtotal( $(this).attr('data-id') );
//		});
    }

    $(".hapus-item").click(function(){
    	var confirm_info = confirm('Hapus Purchase Detail?');
			if (confirm_info) {
				var id_item = $(this).attr('data-id');
				$.post('<?=base_url('ajax/delete_purchase_detail');?>', {
					o: <?= $purchase->DeviceID ?>,
	                p: <?= $purchase->TransactionID ?>,
	                d: id_item
	            }, function (data) {
	                var jsonData = JSON.parse(data);
	                if (jsonData.status=="OK") {
	                    alert('Item Berhasil Dihapus');
	                    $('#compiling-form tr[data-id="'+id_item+'"]').remove();
	                } else{
	                    alert('Item Gagal Dihapus');
	                }
	            });
		}
    });

    <?php if (isset($_SESSION['notif'])): ?>
    alert("<?= $_SESSION['notif'] ?>");
    window.location.href = "<?php echo base_url(); ?>pembelian/?outlet=<?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>";
    <?php endif ?>
</script>
