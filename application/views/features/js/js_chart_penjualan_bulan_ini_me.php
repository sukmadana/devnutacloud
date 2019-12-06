<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 05/04/16
 * Time: 13:44
 */
?>
<script type="text/javascript">
        function js_chart_penjualan_bulan_ini_me() {
            var datestart = $('input[name=date_start]').val();
            var dateend = $('input[name=date_end]').val();
            var outlet = $('#outlet').val();
            $.post('<?=base_url() . 'ajaxdsbchart/penjualan';?>', {
                ds: datestart,
                de: dateend,
                o: outlet
            }, function (data) {
                var ret = JSON.parse(data);
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
                        ticks: ret.ticks

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
                $('#loadmask-chart-penjualan').hide();
                $('#caption-chart-penjualan').html(ret.caption);
                $.plot("#weekly-earnings", ret.data, chartOptionss);
            });
        }
</script>
