<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/css/jquery.datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/css/chosen.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>/js/jquery.datetimepicker.full.min.js"></script>
<script type="text/javascript">
	var items = <?= json_encode($items) ?>;
	var id = 0;
	var elements;
	var row_items = 0;

	$.datetimepicker.setLocale('id');
	$("input[name=datetime]").datetimepicker({
		format : 'Y-m-d H:i',
		lang : 'id'
	});	

	$(".button-focus").click(function(){
		$("input[name=datetime]").focus();
	});

	$('.form-store').submit(function(e){
		if (!row_items) {
			e.preventDefault();
			return alert("Tidak bisa disimpan karena tidak ada item stok");
		}
	});

	validator_form(".datetime", "Tanggal tidak boleh kosong");
	validator_form("textarea", "Catatan tidak boleh kosong ");

	function validator_form(selector, message, callback = function(){}) {
		document.querySelectorAll(selector).forEach( function(element, index) {
			element.oninvalid = function(e) {
				if (callback(e)) 
					return ;

				e.target.setCustomValidity("");
				if (!e.target.validity.valid) {
					e.target.setCustomValidity(message);
				}
			}

			element.oninput = function(e) {
				e.target.setCustomValidity("");
			}
		});
	}


	function resetElement() {
		elements = {
			root : $("#compiling-form"),
			tr : $("<tr></tr>"),
			td : $("<td></td>"),
			select : $("<select data-placeholder=\"Pilih item...\"></select>").addClass('form-control').addClass('select').attr('name', 'item-name[]'),
			input : $("<input type=\"number\" name=\"item-total[]\" class=\"form-control\" min=\"0\" required>"),
            input2 : $("<textarea style=\"height: 38px\" name=\"item-keterangan[]\" class=\"form-control\" min=\"0\" required>"),
			button : $("<button type=\"button\" class=\"btn btn-default\">Hapus</button>"),
		}
	}

	function preLoad(selected_item = 1, total = 1, keterangan = ""){
		resetElement();
		elements.tr.attr('data-id', ++id);
		elements.button.on('click', { targetId : id }, function(e){
			$("tr[data-id="+ e.data.targetId +"]").detach();
			row_items--;
		});

		$.each(items, function(key, val) {
            var keyitem = val.ItemID + '.' + val.DeviceNo;
			var option = $("<option>" + val.ItemName + "</option>").attr('value', keyitem)
			if (selected_item == keyitem) {
				option.attr('selected', '');
			}
			option.appendTo(elements.select);
		});

		$("<td></td>").append(elements.select).appendTo(elements.tr);
		$("<td></td>").append(elements.input.attr('value', total).addClass('input-value')).appendTo(elements.tr);
        $("<td></td>").append(elements.input2.attr('value', keterangan).addClass('input-value')).appendTo(elements.tr);
		$("<td></td>").append(elements.button).appendTo(elements.tr);

		row_items++;
		setTimeout(function() {
			validator_form('.input-value', "Jumlah item tidak boleh minus", function(e){
				if (e.target.value == "") {
					e.target.setCustomValidity("Jumlah harus angka");
					return true;
				}
			});
		}, 100)
	}

	function render_row(selected_item = 0, total = 0, keterangan=""){
		preLoad(selected_item, total, keterangan);
		elements.tr.appendTo(elements.root);
		$('.select').chosen({
			width : '100%',
			no_results_text : 'Tidak ditemukan',
		});
	}

<?php if (isset($_SESSION['notif'])): ?>
    alert("<?= $_SESSION['notif'] ?>");
	window.location.href = "/stokmasuk/?outlet=<?= isset($deviceid) ? $deviceid : $_GET['outlet'] ?>&notify=1&src=Save";

<?php endif ?>
</script>
