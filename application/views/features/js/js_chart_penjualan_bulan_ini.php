<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        function js_chart_penjualan_bulan_ini() {

            var chartOptionss = {
                grid: {
                    hoverable: true,
                    borderWidth: {
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 0
                    },
                    clickable: true,
                    borderColor: "#f0f0f0",
                    margin: {
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 10
                    },
                    minBorderMargin: 1,
                    labelMargin: 20,
                    mouseActiveRadius: 30,
                    backgroundColor: {
                        colors: ["#fff", "#fff"]
                    }
                },
                legend: {
                    noColumns: 0,
                    show: true,
                    labelFormatter: function (label, series) {
                        return "<span class=\"w_legend\" >&nbsp;" + label + "</span>&nbsp;";
                    },
                    backgroundOpacity: 0.9,
                    labelBoxBorderColor: "#000000",
                    position: "nw",
                    margin: [0, -20]
                },
                series: {

                    shadowSize: 1

                },
                xaxis: {
                    show: true,
                    axisLabel: 'Tanggal',
                    color: '#eee',
                    tickDecimals: 0,
                    ticks:<?=$chart['penjualan_bulan_ini']['ticks'];?>

                },
                yaxis: {
                    show: false
                },
                tooltip: {
                    show: true,
                    cssClass: "StatsFlotTip",
                    content: function (label, xval, yval, flotItem) {
                        return "Tgl " + xval + "<br/>" + yval.formatMoney(0, 'Rp. ', '.', ',');
                    }
                },
                colors: ["#87cfcb"]
            };
            var options = {
                grid: {
                    borderColor: "#f0f0f0",
                    clickable: true,
                    hoverable: true,

                },
                xaxis: {
                    show: true,
                    axisLabel: 'Tanggal',
                    color: '#eee',
                    tickDecimals: 0,
                    ticks:<?=$chart['penjualan_bulan_ini']['ticks'];?>

                }, series: {
                    stack: false,
                    shadowSize: 1

                },
                yaxis: {
                    show: false
                },
                tooltip: {
                    show: true,
                    cssClass: "StatsFlotTip",
                    content: "Rp. %y "
                },
            };
            var chartDataa = {

                lines: {
                    show: false,
                    fill: true,
                    lineWidth: 2
                },
                splines: {
                    show: true,
                    tension: 0.5,
                    lineWidth: 2,
                    fill: 0
                },
                points: {
                    show: true,
                    lineWidth: 2,
                    radius: 4,
                    symbol: "circle",
                    fill: true,
                    fillColor: "#ffffff"

                },
                data: [{
                    label: "Januari",
                    data: [[1, 30], [2, 10], [3, 15]]

                }, {
                    label: "Februari",
                    data: [[1, 10], [2, 30], [3, 5]]

                }


                ]

            };


            $.plot("#weekly-earnings", <?=$chart['penjualan_bulan_ini']['data'];?>, chartOptionss);
        }
    });
</script>