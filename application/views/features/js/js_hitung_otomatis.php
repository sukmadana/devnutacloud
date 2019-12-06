<?php
/**
 * Created by PhpStorm.
 * User: fal
 * Date: 10/26/2018
 * Time: 1:10 PM
 */
?>
<script>
    var websocket = new WebSocket("ws://localhost:9007");
    websocket.onopen = function(event) {
        console.log("connection open")
    }
    websocket.onmessage = function(event) {
        var Data = JSON.parse(event.data);
        var message = Data.message
        var outlet = document.getElementById("outlet").value
        var start = document.getElementsByName("date_start")[0].value
        var end = document.getElementsByName("date_end")[0].value
        message = JSON.parse(message)
        if (outlet == message.DeviceID && start == message.startdate && end == message.enddate) {
            var nm = "sedang menjurnal " + message.data.TransactionName + " nomor " + message.data.TransactionNumber
            document.getElementById("txtload").innerText = nm
            document.getElementById("prog").style.width = message.chat_message + "%"
        }
    };

    websocket.onerror = function(event){
        console.log('event', event)
    };
    websocket.onclose = function(event){
        console.log('connection closed')
    };

    jQuery(document).ready(function ($) {
        $('#btnHitung').click(function (){
            $('.progress').show()
            $('#txtload').show()
            var frm = $("#frmhitungotomatis").serialize()
            $.post('<?=base_url().'journal/hitung'?>', frm, function(data) {
                data = JSON.parse(data)
                $('#txtload').text(data.message)
            })
        })
    })
</script>