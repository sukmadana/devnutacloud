<?php

/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 03/05/17
 * Time: 15:45
 */
?>
<script type="text/javascript">
    var nutaSocket;
    var NEvent = {
        'REQUEST_CREDENTIAL': 'ask_credential',
        'RESPONSE_CREDENTIAL': 'response_credential',
        'REQUEST_DEVICE_ID': 'ask_outlet_id',
        'RESPONSE_DEVICE_ID': 'response_outlet_id',
        'RESPONSE_DEVICES_STATUS': 'response_outlets_status',

        'TRANSFER_ITEM': 'transfer_item',
        'TRANSFER_ITEM_IMAGE': 'transfer_item_image',
        'TRANSFER_UNIT': 'transfer_satuan',
        'TRANSFER_CATEGORY': 'transfer_kategori',
        'TRANSFER_PRICE_VARIAN': 'transfer_variasi_harga',
        'TRANSFER_MODIFIER': 'transfer_modifier',
        'TRANSFER_MODIFIER_DETAIL': 'transfer_modifier_detail',
        'TRANSFER_MODIFIER_ITEM': 'transfer_modifier_item',
        'TRANSFER_CUSTOMER': 'transfer_pelanggan',
        'TRANSFER_CASHIN': 'transfer_uang_masuk',
        'TRANSFER_CASHOUT': 'transfer_uang_keluar',
        'TRANSFER_STOCKIN': 'transfer_stok_masuk',
        'TRANSFER_STOCKOUT': 'transfer_stok_keluar',
        'TRANSFER_STOCKIN_DETAIL': 'transfer_stok_masuk_detail',
        'TRANSFER_STOCKOUT_DETAIL': 'transfer_stok_keluar_detail',
        'RECEIVE_ITEM': 'receive_item',
        'RECEIVE_UNIT': 'receive_satuan',
        'RECEIVE_CATEGORY': 'receive_kategori',
        'RECEIVE_PRICE_VARIAN': 'receive_variasi_harga',
        'RECEIVE_MODIFIER': 'receive_modifier',
        'RECEIVE_MODIFIER_DETAIL': 'receive_modifier_detail',
        'RECEIVE_MODIFIER_ITEM': 'receive_modifier_item',
        'RECEIVE_CUSTOMER': 'receive_pelanggan',
        'RECEIVE_CASHIN': 'receive_uang_masuk',
        'RECEIVE_CASHOUT': 'receive_uang_keluar',
        'RECEIVE_STOCKIN': 'receive_stok_masuk',
        'RECEIVE_STOCKOUT': 'receive_stok_keluar',
        'RECEIVE_STOCKIN_DETAIL': 'receive_stok_masuk_detail',
        'RECEIVE_STOCKOUT_DETAIL': 'receive_stok_keluar_detail'

    };
    jQuery(document).ready(function($) {
        //        nutaSocket = io.connect('http://dev.nutacloud.com:3000/nutacloud');
        //
        //        nutaSocket.on('response_outlets_status', function (data) {
        //            var response = JSON.parse(data);
        //            var onlineHTML = '<span class="label label-info">online</span>';
        //            var offlineHTML = '<span class="label label-danger">offline</span>';
        //            for (var x = 0; x < response.length; x++) {
        //                $('#status' + response[x].outletid).html(response[x].is_online ? onlineHTML : offlineHTML);
        //            }
        //
        //        });
        //        nutaSocket.on('ask_credential', function (data) {
        //            nutaSocket.emit('response_credential', JSON.stringify(
        //                {
        //                    perusahaanID: ' //=getLoggedInUserID();
        //',
        //                    outletIDs: [//=join(',', $outletids);
        //]
        //                }
        //            ));
        //        });


    });

    function sendToSocket(event, data) {
        //nutaSocket.emit(event, JSON.stringify(data));
    }
</script>