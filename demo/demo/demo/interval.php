<?php
$fp = fopen("rnnOut.txt", "r");
$fstr = fread($fp, filesize("rnnOut.txt"));
fclose($fp);
$arr = preg_split("/[\s]+/", $fstr);

// ¼±¾ð
$predictMaxArr = array();
$targetMaxArr = array();
$predictMinArr = array();
$targetMinArr = array();
$abnormalVal = array();
$predictAvgArr = array();
$targetAvgArr = array();
$predictStdArr = array();
$targetStdArr = array();

// standard deviation
function stdDev($array, $avg)
{
    foreach ($array as $val) {
        $sum += $val * $val;
    }
    $temp = $sum / sizeof($array);
    $result = sqrt(($temp - $avg * $avg));
    
    return $result;
}

for ($i = 0; $i < count($arr) / 60 - 1; $i ++) {
    
    $predictArr = array_slice($arr, $i * 61, 30);
    $targetArr = array_slice($arr, $i * 61 + 30, 30);
    
    // predict, target's max values
    $predictMaxVal = max($predictArr);
    $targetMaxVal = max($targetArr);
    
    // predict, target's min values
    $predictMinVal = min($predictArr);
    $targetMinVal = min($targetArr);
    
    // predict, target's avg values
    $presum = array_sum($predictArr);
    $tarsum = array_sum($targetArr);
    $preAvgVal = $presum / sizeof($predictArr);
    $tarAvgVal = $tarsum / sizeof($targetArr);
    
    // predict, target's std deviation
    $preDevVal = stdDev($predictArr, $preAvgVal);
    $tarDevVal = stdDev($targetArr, $tarAvgVal);
    
    // array push values
    array_push($predictMaxArr, $predictMaxVal);
    array_push($predictMinArr, $predictMinVal);
    array_push($predictAvgArr, $preAvgVal);
    array_push($predictStdArr, $preDevVal);
    
    array_push($targetMaxArr, $targetMaxVal);
    array_push($targetMinArr, $targetMinVal);
    array_push($targetAvgArr, $tarAvgVal);
    array_push($targetStdArr, $tarDevVal);
    
    array_push($abnormalVal, $arr[$i * 61 + 60]);
}
// order, predict(avg, max, min, +, -), target(avg, max, min, +, -)

$resultArr = array();
for ($i = 0; $i < count($predictAvgArr); $i ++) {
    
    $result = "[" . $i . "," . $predictAvgArr[$i] . "," . $predictMaxArr[$i] . "," . $predictMinArr[$i] . "," . ($predictAvgArr[$i] + $predictStdArr[$i]) . "," . ($predictAvgArr[$i] - $predictStdArr[$i]) . "," . $targetAvgArr[$i] . "," . $targetMaxArr[$i] . "," . $targetMinArr[$i] . "," . ($targetAvgArr[$i] + $targetStdArr[$i]) . "," . ($targetAvgArr[$i] - $targetStdArr[$i]) . "],";
    if ($i == count($predictAvgArr) - 1)
        $result = "[" . $i . "," . $predictAvgArr[$i] . "," . $predictMaxArr[$i] . "," . $predictMinArr[$i] . "," . ($predictAvgArr[$i] + $predictStdArr[$i]) . "," . ($predictAvgArr[$i] - $predictStdArr[$i]) . "," . $targetAvgArr[$i] . "," . $targetMaxArr[$i] . "," . $targetMinArr[$i] . "," . ($targetAvgArr[$i] + $targetStdArr[$i]) . "," . ($targetAvgArr[$i] - $targetStdArr[$i]) . "]";
    array_push($resultArr, $result);
}
?>
<html>
<head>
<script type="text/javascript"
	src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', {'packages' : [ 'corechart' ]});
	google.charts.setOnLoadCallback(drawChart);
	function drawChart() {

		var data = new google.visualization.DataTable();
        data.addColumn('number', 'orders');
        data.addColumn('number', 'predict');
        data.addColumn({id:'i0', type:'number', role:'interval'});
        data.addColumn({id:'i0', type:'number', role:'interval'});
        data.addColumn({id:'i1', type:'number', role:'interval'});
        data.addColumn({id:'i1', type:'number', role:'interval'});
        data.addColumn('number', 'target');
        data.addColumn({id:'t0', type:'number', role:'interval'});
        data.addColumn({id:'t0', type:'number', role:'interval'});
        data.addColumn({id:'i1', type:'number', role:'interval'});
        data.addColumn({id:'i1', type:'number', role:'interval'});

		data.addRows([ <?php

foreach ($resultArr as $val) {
    echo $val;
}
?> ]);

		// The intervals data as narrow lines (useful for showing raw source data)
        var options_points = {
                title:'Points, default',
                curveType:'function',
                lineWidth: 4,
                series: {
                	0:{'color': '#D3362D'},
                	1: { 'color': '#43459d' },
                },
                intervals: { 'lineWidth':2, 'barWidth': 0.5 },
                interval:{
                	'i0': { 'style':'points', 'color':'red', 'pointSize': 10,
                        'lineWidth': 0, 'fillOpacity': 0.3 },
                't0': { 'style':'points', 'color':'blue', 'pointSize': 10,
                    'lineWidth': 0, 'fillOpacity': 0.3 }
                },
                legend: 'none',
            };
  
        var chart_lines = new google.visualization.LineChart(document.getElementById('chart_lines'));
        chart_lines.draw(data, options_points);
      }
    </script>
  </head>
  <body>
    <div id="chart_lines" style="width: 900px; height: 500px;"></div>
  </body>
</html>
