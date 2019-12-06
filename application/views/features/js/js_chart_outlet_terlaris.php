<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        Number.prototype.formatMoney = function(places, symbol, thousand, decimal) {
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
        function labelFormatter(label, series) {
            return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + series.data[0][1].formatMoney(0,"",".",",") + "</div>";
        }
        var outletterlarischartOptions = {
            series: {
                pie: {
                    innerRadius: 0.3,
                    show: true,
                    radius:1,
                    stroke: {
                        width: 0.1
                    },
                    label: {
                        show: true,
                        radius:2/3 ,
                        threshold: 0.1,
                        formatter: labelFormatter,
                    }
                },
                stack: true,
                shadowSize: 1,
                label: {
                    orientation: 'horizontal',

                    color: '#0000FF',
                    'text-anchor': 'middle'
                },

            },
            legend: {
                position: "se",
                show:true,
                noColumns: 1,
            },
            grid: {
                hoverable: true
            },
            tooltip: {
                show: true,
                cssClass: "MainFlotTip",
                content: "%s - %n"
            },
            colors: ["#8BC34A", "#FDD835"]
        };
        var outletterlarischartData = <?= $chart['outlet_terlaris']['data']; ?>;
        if(outletterlarischartData.length >0){
            $('#nodataoutletterlaris').hide();
        }else{
            $('#nodataoutletterlaris').show();
        }

        $.plot("#outlet-terlaris-pie-chart", outletterlarischartData, outletterlarischartOptions);

    });
</script>