<!DOCTYPE html>
<html>

<head>
	<link rel="apple-touch-icon-precomposed" href="<?php echo WEB_DOMAIN;?>resource/img/apple-touch-icon-120x120-precomposed.png" />
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<title>服务负载详情</title>
</head>

<body>
<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

<script type="text/javascript" src="http://cdn.hcharts.cn/jquery/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="http://cdn.hcharts.cn/highcharts/highcharts.js"></script>
<script type="text/javascript">
	$(function () {
    $.getJSON('<?php echo WEB_DOMAIN;?>/timeseries.php?job_str=<?php echo $job_str;?>&date=<?php echo $date;?>&callback=?', function (data) {
        $('#container').highcharts({
            chart: {
                zoomType: 'x'
            },
            title: {
                text: '冗余能力图'
            },
            subtitle: {
                text: document.ontouchstart === undefined ?
                        'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
            },
            xAxis: {
                type: 'datetime'
            },
            yAxis: {
                title: {
                    text: '服务冗余能力'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                area: {
                    fillColor: {
                        linearGradient: {
                            x1: 0,
                            y1: 0,
                            x2: 0,
                            y2: 1
                        },
                        stops: [
                            [0, Highcharts.getOptions().colors[0]],
                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                        ]
                    },
                    marker: {
                        radius: 2
                    },
                    lineWidth: 1,
                    states: {
                        hover: {
                            lineWidth: 1
                        }
                    },
                    threshold: null
                }
            },

            series: [{
                type: 'area',
                name: '剩余能力',
                data: data
            }]
        });
    });
});
</script>
</body>
</html>
