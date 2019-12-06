<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">
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
    function labelFormatter(label, series) {
        return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + series.data[0][1].formatMoney(0, "Rp ", ".", ",") + "</div>";
    }

    function js_chart_rekap_pembayaran_me() {
        var datestart = $('input[name=date_start]').val();
        var dateend = $('input[name=date_end]').val();
        var outlet = $('#outlet').val();


        $.post('<?=base_url() . 'ajaxdsbchart/rekappembayaran';?>', {
            ds: datestart,
            de: dateend,
            o: outlet
        }, function (data) {
            var ret = JSON.parse(data);
            $('#loadmask-chart-pembayaran').hide();
            var adaData = false;
            for (var a = 0; a < ret.data.length; a++) {
                if (ret.data[a].data != 0) {
                    adaData = true;
                }
            }
            $('#caption-chart-pembayaran').html(ret.caption);
            if (adaData) {
                $('#nodatapembayaran').hide();
                var rekappembayaranchartOptions = {
                    series: {
                        pie: {
                            innerRadius: 0.3,
                            show: true,
                            radius: 1,
                            stroke: {
                                width: 0.1
                            },
                            label: {
                                show: true,
                                radius: 2 / 3,
                                threshold: 0.1,
                                formatter: labelFormatter,
                            }
                        },
                        stack: true,
                        shadowSize: 1,
                        label: {
                            orientation: 'horizontal',  //  or 'vertical'

                            color: '#0000FF',
                            'text-anchor': 'middle'
                        },

                    },
                    legend: {
                        position: "se",
                        show: true,
                        noColumns: 1,
                    },
                    grid: {
                        hoverable: true
                    },
                    tooltip: {
                        show: true,
                        cssClass: "StatsFlotTip",
                        content: function (label, xval, yval, flotItem) {
                            return label + ' - ' + yval.formatMoney(0, "Rp ", ".", ",");
                        }
                    },
                    colors: ["#8BC34A", "#FDD835"]
                };
                $.plot("#rekap-pembayaran-chart", ret.data, rekappembayaranchartOptions);
            } else {
                $('#nodatapembayaran').show();
            }
        });
    }
</script>