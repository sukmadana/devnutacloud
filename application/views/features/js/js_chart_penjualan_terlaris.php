<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        function labelFormatter(label, series) {
            return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>"+series.data[0][1] + "</div>";
        }
        var penjualanterlarisdataPie = <?= $chart['penjualan_terlaris']; ?>;

        var penjualanterlarisDataOptions = {
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
                        formatter: labelFormatter,
                    }
                }
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
                content: "%s - %n",
                shifts: {
                    x: 20,
                    y: 0
                }
            },
            colors: ["#FF80AB", "#4FC3F7", "#FDD835","#8BC34A","#8C9EFF"]
        };
        if(penjualanterlarisdataPie.length >0){
            $('#nodatapenjualanterlaris').hide();
        }else{
            $('#nodatapenjualanterlaris').show();
        }

        var pieChart = $.plot($("#penjualan-terlaris-pie-chart"), penjualanterlarisdataPie, penjualanterlarisDataOptions);



    });
</script>