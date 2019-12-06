/**
 * Created by Husnan on 21/03/2017.
 */
function MasterItemPostData() {
}
function ImageItemPostData() {
}
function PilihanEkstraPostData() {
}
function NamaBahanPostData() {
}
function ItemFormSaver() {
}
jQuery(document).ready(function ($) {

    MasterItemPostData.prototype.selectedoutlet = 0;
    MasterItemPostData.prototype.itemname = '';
    MasterItemPostData.prototype.oldnamakategori = '';
    MasterItemPostData.prototype.namakategori = '';
    MasterItemPostData.prototype.oldnamasatuan = '';
    MasterItemPostData.prototype.namasatuan = '';
    MasterItemPostData.prototype.hargajual = 0;
    MasterItemPostData.prototype.hargabeli = 0;
    MasterItemPostData.prototype.isproduk = true;
    MasterItemPostData.prototype.punyabahan = false;
    MasterItemPostData.prototype.bahans = null;
    MasterItemPostData.prototype.mode = '';
    MasterItemPostData.prototype.olditemname = '';
    MasterItemPostData.prototype.idoutlets = null;
    MasterItemPostData.prototype.deletegambar = false;
    MasterItemPostData.prototype.modifiers = null;

    ImageItemPostData.prototype.id = null;//item id
    ImageItemPostData.prototype.ext = '';
    ImageItemPostData.prototype.outlets = null;
    ImageItemPostData.prototype.image = null;
    ImageItemPostData.prototype.source = 'cloud';

    PilihanEkstraPostData.prototype.outlets = null;
    PilihanEkstraPostData.prototype.ModifierName = '';
    PilihanEkstraPostData.prototype.oldName = '';//old modifier name
    PilihanEkstraPostData.prototype.ChooseOnlyOne = false;
    PilihanEkstraPostData.prototype.CanAddQuantity = false;
    PilihanEkstraPostData.prototype.operation = '';
    PilihanEkstraPostData.prototype.Pilihan = '';

    NamaBahanPostData.prototype.outlet = null;
    NamaBahanPostData.prototype.nama = '';


    ItemFormSaver.prototype.savePilihanEkstra = function (url, pilihanEkstraPostData) {
        return new Promise(function (resolve, reject) {
            $.post(url, pilihanEkstraPostData,
                function (data) {
                    data = JSON.parse(data);
                    if (data.status == false) {
                        alert(data.message);//error message
                        reject(data);
                    } else {
                        //alert('Modifier Berhasil disimpan dengan id: ' + data);
                        //alert('Simpan Item~');
                        console.log(data);
                        resolve(data.data);//modifierID
                    }
                });
        });
    }


    ItemFormSaver.prototype.copyBahan = function (url, namabahanPostData) {
        return new Promise(function (resolve, reject) {
            $.post(url, namabahanPostData,
                function (data) {
                    data = JSON.parse(data);
                    if (data.status == false) {
                        alert(data.message);//error message
                        reject(data);
                    } else {
                        //alert('Modifier Berhasil disimpan dengan id: ' + data);
                        //alert('Simpan Item~');
                        console.log(data);
                        resolve(data.data);//modifierID
                    }
                });
        });
    }

    ItemFormSaver.prototype.saveItem = function (url, masterItemPostData) {
        console.log("saveItem" + url);
        return new Promise(function (resolve, reject) {
            $.post(
                url, masterItemPostData,
                function (data) {
                    try {
                        data = JSON.parse(data);
                        if (data.status == false) {
                            reject(data.message);//id item
                        } else {
                            resolve(data.data);
                        }
                    } catch(e) {
                        alert(e);
                        alert(data);  // error in the above string (in this case, yes)!
                    }
                });
        });
    }

    ItemFormSaver.prototype.saveItemGabung = function (url, pilihanEkstraPostData, masterItemPostData, arrayOfVariasiHarga) {
        console.log("saveItem" + url);
        var jumlahVariasiHarga = $('.item-variasi-harga').length;
        var variasiHarga = {};

        for (var x = 0; x < arrayOfVariasiHarga.length; x++) {
            variasiHarga[x] = {
                nama: arrayOfVariasiHarga[x].VarianName,
                harga: arrayOfVariasiHarga[x].SellPrice,
                reguler: arrayOfVariasiHarga[x].IsRegular,
                oldname: arrayOfVariasiHarga[x].OldVarianName
            };

        }
        return new Promise(function (resolve, reject) {
            $.post(
                url, {
                    modifiers: pilihanEkstraPostData,
                    variasiharga: variasiHarga,
                    items: masterItemPostData
                },
                function (data) {
                    try {
                        data = JSON.parse(data);
                        if (data.status == false) {
                            reject(data.message);//id item
                        } else {
                            resolve(data.data);
                        }
                    } catch(e) {
                        alert(e);
                        alert(data);  // error in the above string (in this case, yes)!
                    }
                });
        });
    }

    ItemFormSaver.prototype.push_imageitem = function (url, arrayOfSavedItemID) {
        for (var x = 0; x < arrayOfSavedItemID.length; x++) {
            var savedItem = arrayOfSavedItemID[x];
            if(savedItem.outlet != null) {
                console.log(savedItem);
                //console.log(" Di outlet: " + savedItem.outlet + " disimpan dengan id: " + savedItem.saved_id);
                //ids.push(savedItem.saved_id);
                //outlets.push(savedItem.outlet);
                //arrayOfSavedItemID2.push(savedItem);
            }
        }
        return new Promise(function (resolve, reject) {
            $.post(url, {
                    outletdanitem: arrayOfSavedItemID
                },
                function (data) {
                    var json = JSON.parse(data);
                    if (json.status) {
                        resolve(arrayOfSavedItemID);
                    } else {
                        reject(json);
                    }
                }
            );
        });

    }

    ItemFormSaver.prototype.saveImage = function (url, imageItemPostData) {
        return new Promise(function (resolve, reject) {
            $.post(url, imageItemPostData, function (data) {
                //data = JSON.parse(data);//dari WS  ga perlu convert json
                if (data.status) {
                    resolve(data);
                } else {
                    reject(data);
                }
            });
        });
    }

});
