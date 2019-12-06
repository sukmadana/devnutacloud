<?php
/*
 * This file created by Em Husnan 
 * Copyright 2015 - Dyamazta
 * www.dyamazta.com
 */
?>
<script type="text/javascript">

        function labelFormatter(label, series) {
            return "<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>" + series.data[0][1] + "</div>";
        }

        function js_chart_penjualan_terlaris_me() {

            var datestart = $('input[name=date_start]').val();
            var dateend = $('input[name=date_end]').val();
            var outlet = $('#outlet').val();

            $.post('<?=base_url() . 'ajaxdsbchart/terlaris';?>', {
                ds: datestart,
                de: dateend,
                o: outlet
            }, function (data) {
                var ret = JSON.parse(data);
                var penjualanterlarisDataOptions = {
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
                                formatter: labelFormatter
                            }
                        }
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
                        cssClass: "MainFlotTip",
                        content: "%s - %n",
                        shifts: {
                            x: 20,
                            y: 0
                        }
                    },
                    colors: ["#FF80AB", "#4FC3F7", "#FDD835", "#8BC34A", "#8C9EFF"]
                };

                if (ret.data.length > 0) {
                    $('#nodatapenjualanterlaris').hide();
                } else {
                    $('#nodatapenjualanterlaris').show();
                }
                $('#loadmask-chart-terlaris').hide();
                $('#caption-chart-terlaris').html(ret.caption);
                var pieChart = $.plot($("#penjualan-terlaris-pie-chart"), ret.data, penjualanterlarisDataOptions);
            });
        }


</script>