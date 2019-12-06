<?php
/**
 * Created by PhpStorm.
 * User: husnan
 * Date: 05/04/16
 * Time: 13:44
 */
?>
<script type="text/javascript">


        function shownotetip(x, y, contents, colour) {
            $('<div id="value">' + contents + '</div>').css({
                position: 'absolute',
                display: 'none',
                top: y,
                left: x,
                'border-style': 'solid',
                'border-width': '1px',
                'border-color': colour,
                'background-color': '#ffffff',
                'color': '#545454',
                'font-size': 'smaller',
            }).appendTo("body").fadeIn(200);
        }

        function js_chart_pengunjung_bulan_ini_me() {

            var datestart = $('input[name=date_start]').val();
            var dateend = $('input[name=date_end]').val();
            var outlet = $('#outlet').val();
            $.post('<?=base_url() . 'ajaxdsbchart/pengunjung';?>', {
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
                $('#loadmask-chart-pengunjung').hide();
                $('#caption-chart-pengunjung').html(ret.caption);
                plot = $.plot("#chart-pengunjung", ret.data, chartOptionss);
                var divPos = plot.offset();
                for (var h = 0; h < ret.data.length; h++) {
                    var data = ret.data[h].data;
                    for (var i = 0; i < data.length; i++) {
                        pos = plot.p2c({x: data[i][0], y: data[i][1]});
                        shownotetip(pos.left + divPos.left, pos.top + divPos.top, data[i][1].formatMoney(0, '', '.', ','), ret.data[h].color);
                    }
                }
            });
        }


</script>
