<?php
/**
 * Created by PhpStorm.
 * User: Husnan
 * Date: 05/09/2016
 * Time: 10:42
 */ ?>
<div class="modal fade" id="rincian-hpp-dialog" tabindex="-1" role="dialog" aria-labelledby="rincian-hpp-dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <label id="rincian-hpp-title" style="text-align:center;width:100%"></label>
                <div id="rincian-hpp-container" class="table-responsive">
                </div>
                <div id="loading-container" class="loadmask">
                    <div style="left: 50%;top: 50%;transform: translate(-50%,50%);" class="loadmask-msg">
                        <div class="clearfix">
                            <div class="w-loader"></div>
                            <span class="w-mask-label">Loading..<span></span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:0px">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
    var rincianHppDialog;
    var detailid;
    $(document).on('ready', function () {
        Number.prototype.formatMoney = function (places, symbol, thousand, decimal) {
            places = !isNaN(places = Math.abs(places)) ? places : 2;
            symbol = symbol !== undefined ? symbol : "$";
            thousand = thousand || ",";
            decimal = decimal || ".";
            var number = this,
                negative = number < 0 ? "-" : "",
                i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;
            return symbol + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
        };

        $('#rincian-hpp-dialog').on('show.bs.modal', function (event) {
            rincianHppDialog = $(this);
            var dtId = $(event.relatedTarget).data('id');
            var qty = $(event.relatedTarget).data('qty');
            var name = $(event.relatedTarget).data('name');
            $("#rincian-hpp-title").html("Rincian HPP untuk " + qty + " " + name);
            var outlet =<?=$selected_outlet;?>;
            $.post('<?=base_url('ajax/getrincianhpp');?>', {
                o: outlet,
                i: dtId,
            }, function (data) {
                var jsondata = JSON.parse(data);
                var dom = "<table cellpadding=\"50\" cellspacing=\"50\" class=\"table table-bordered table-striped\">" +
                    "<tr> <th>Item Bahan</th> <th>Qty</th> <th>Harga Beli</th> <th>Jumlah</th> </tr> ";
                var total = 0;
                for (var x = 0; x < jsondata.length; x++) {
                    var data = jsondata[x];
                    var jumlah = Number(data.Jumlah);
                    var qty = Number(data.Qty);
                    var hargabeli = Number(data.HargaBeli);
                    dom += "<tr><td>" + data.ItemName + "</td><td>" + qty.formatMoney(0, '', '.', ',') + "</td><td>" + hargabeli.formatMoney(0, 'Rp. ', '.', ',') + "</td><td>" + jumlah.formatMoney(0, 'Rp. ', '.', ',') + "</td></tr>";
                    total += jumlah;
                }
                dom += "<tr><td colspan='3' align='center'>Jumlah HPP</td><td>" + total.formatMoney(0, 'Rp. ', '.', ',') + "</td></tr>"
                dom += "</table>";
                $('#loading-container').hide();
                $('#rincian-hpp-container').html(dom);
            });

        });
    });

</script>
