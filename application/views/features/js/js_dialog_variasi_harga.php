<script type="text/javascript">
</script>
<script type="text/javascript">
    var $;
    function renderPriceList() {
        var hargajual = $('#txt-harga-jual').val();

        if (priceList.length > 0) {
            if (hargajual != priceList[0].SellPrice) {
                priceList[0].SellPrice = hargajual;
            }
        }
        $('.item-variasi-harga').remove();
        for (var x = 0; x < priceList.length; x++) {
            $(priceList[x].toHtml()).insertBefore('#row-tambah-variasi-harga');
        }
    }
    jQuery(document).ready(function (y) {
        $ = y;
        var dialogVariasiHarga;
        var dummy = function () {
            var hargajual = $('#txt-harga-jual').val();
            var firstrow = new VariasiHarga();
            firstrow.VarianName = "Regular";
            firstrow.SellPrice = (hargajual == "" ) ? 0 : parseInt(hargajual);
            priceList.push(firstrow);
            priceList.push(new VariasiHarga());
            priceList.push(new VariasiHarga());
        };

        var bindHtmlFormToData = function () {
            $('.item-variasi-harga').each(function (index, tr) {
                console.log(" Index : " + index);
                console.log(" PriceList length : " + priceList.length);
                var inputNamaVariasiHarga = tr.children[0].children;
                var inputHargaVariasiHarga = tr.children[1].children;
                var namaVariasiHarga = $(inputNamaVariasiHarga).val();
                var hargaVariasiHarga = $(inputHargaVariasiHarga).val();
                var reguler = (index == 0) ? 1 : 0;
                var name = $(tr).data('name');
                priceList[index].ItemID = '<?=isset($iditem) ? $iditem : "null";?>';
                priceList[index].VarianName = namaVariasiHarga;
                priceList[index].SellPrice = hargaVariasiHarga;
                priceList[index].IsRegular = reguler;
                priceList[index].OldVarianName = name;
            });
        };
        $('#variasi-harga-modal').on('show.bs.modal', function (event) {
            dialogVariasiHarga = $(this);
            $('#loading-variasi-harga-container').show();
            $('#variasi-harga-container').hide();
            if (priceList.length == 0) {
                console.log('list varian belum ada');
                $.post('<?=base_url('ajax/getvariasiharga');?>', {
                        outlet: <?=$selected_outlet;?>,
                        itemid:'<?=isset($iditem) ? $iditem : "new";?>'
                    },
                    function (data) {
                        $('#loading-variasi-harga-container').hide();
                        $('#variasi-harga-container').show();
                        var savedPriceList = JSON.parse(data);
                        if (savedPriceList.length > 0) {
                            priceList = [];
                            for (var x = 0; x < savedPriceList.length; x++) {
                                var newPrice = new VariasiHarga();
                                newPrice.VarianName = savedPriceList[x].VarianName;
                                newPrice.SellPrice = savedPriceList[x].SellPrice;
                                newPrice.OldVarianName = savedPriceList[x].VarianName;
                                priceList.push(newPrice);
                            }
                        } else if (priceList.length <= 0) {
                            dummy();
                        } else {
                            var hj = $('#txt-harga-jual').val();
                            priceList[0].SellPrice = (hj == "" ) ? 0 : parseInt(hj);
                        }
                        renderPriceList();
                    });
            } else {
                console.log('list varian sudah ada');
                $('#loading-variasi-harga-container').hide();
                $('#variasi-harga-container').show();
                renderPriceList();
            }
        });
        $('#variasi-harga-modal').on('hide.bs.modal', function (event) {
            $('#txt-harga-jual').val(priceList[0].SellPrice);
        });
        $('#tambah-baris-variasi-harga').click(function (e) {
            var newPrice = new VariasiHarga();
            priceList.push(newPrice);
            $(newPrice.toHtml()).insertBefore('#row-tambah-variasi-harga');
            e.preventDefault();

        });
        var removeEmptyData = function () {
            var a = priceList.filter(function (el) {
                return el.VarianName !== '';
            });
            priceList = a;
        };
        $('#btn-simpan-variasi-harga').click(function (e) {
            bindHtmlFormToData();
            removeEmptyData();
//            new BulkDataSaver().saveVariasiHarga('<?//=base_url();?>//', priceList,<?//=$selected_outlet;?>//);
            dialogVariasiHarga.modal('hide');
            console.log(priceList);
            renderPriceList();
        });


    });
    function hapusVariasiHarga(namaVariasiHarga) {
        if (namaVariasiHarga !== '') {
            var deletedPrice;
            var priceListExcludeDeleted = priceList.filter(function (el) {
                var isNotDeleted = el.VarianName !== namaVariasiHarga;
                el.ItemID =<?=isset($iditem) ? $iditem : "'new'";?>;
                if (isNotDeleted == false) {
                    deletedPrice = el;
                }
                return isNotDeleted;
            });
            console.log(deletedPrice);
            priceList = priceListExcludeDeleted;
            renderPriceList();
        } else {
            alert('Nama variasi harga tidak boleh kosong.');
        }
    }

</script>
