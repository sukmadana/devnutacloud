/**
 * Created by Husnan on 21/02/2017.
 */
function VariasiHarga() {
}
function BulkDataSaver() {
}
jQuery(document).ready(function ($) {
    VariasiHarga.prototype.ItemID = null;
    VariasiHarga.prototype.VarianName = '';
    VariasiHarga.prototype.SellPrice = 0;
    VariasiHarga.prototype.IsRegular = 0;
    VariasiHarga.prototype.OldVarianName = '';
    VariasiHarga.prototype.toHtml = function () {
        var barisvariasiharga = '<tr class="item-variasi-harga" data-name="' + this.OldVarianName + '">' +
            '<td> <input type = "text" class = "form-control" placeholder = "' + this.VarianName + '" value="' + this.VarianName + '"/></td>' +
            '<td> <input type = "number"  min="0" class = "form-control" placeholder = "Rp. ' + this.SellPrice + '" value="' + (this.SellPrice == 0 ? '' : this.SellPrice ) + '" onkeypress="return ((event.charCode >= 48 && event.charCode <= 57)||(event.keyCode == 8 ||event.keyCode == 46))"/></td>' +
            '<td><a href="#" class="btn btn-danger" onclick="hapusVariasiHarga(\'' + this.VarianName + '\')">Hapus</a></td>' +
            '</tr>';
        return barisvariasiharga;
    }
    VariasiHarga.prototype.delete = function (url, o) {
        $.post(url, {
                outlet: o,
                nama: VarianName,
                itemid: ItemID,
            },
            function (data) {
            });
    }

    BulkDataSaver.prototype.saveVariasiHarga = function (url, arrayOfVariasiHarga, arrayOufOutlet, arrayOufItemId, arrayOfSavedItemID) {
        var jumlahVariasiHarga = $('.item-variasi-harga').length;
        var variasiHarga = {};
        console.log(arrayOfSavedItemID);

        for (var x = 0; x < arrayOfVariasiHarga.length; x++) {
            variasiHarga[x] = {
                nama: arrayOfVariasiHarga[x].VarianName,
                harga: arrayOfVariasiHarga[x].SellPrice,
                reguler: arrayOfVariasiHarga[x].IsRegular,
                oldname: arrayOfVariasiHarga[x].OldVarianName
            };

        }
        return new Promise(function (resolve, reject) {
            $.post(url, {
                    outlets: arrayOufOutlet,
                    variasiharga: variasiHarga,
                    itemids: arrayOufItemId,
                    outletdanitem: arrayOfSavedItemID
                },
                function (data) {
                    var json = JSON.parse(data);
                    if (json.status) {
                        resolve({ids: arrayOufItemId, outlets: arrayOufOutlet});
                    } else {
                        reject(json);
                    }
                }
            );
        });

    }

});

