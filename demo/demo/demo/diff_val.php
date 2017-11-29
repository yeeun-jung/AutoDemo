<?php
$fp = fopen("rnnOut.txt", "r");
$fstr = fread($fp, filesize("rnnOut.txt"));
fclose($fp);
// 문자열 자르기 : 배열로 저장된다.
$predictVal = array();
$targetVal = array();
$abnormalVal = array();

$arr = preg_split("/[\s]+/", $fstr);

for ($i = 0; $i < count($arr) / 60 - 1; $i ++) {
    
    $predictArr = array_slice($arr, $i * 61, 30);
    $targetArr = array_slice($arr, i * 61 + 30, 30);
    
    $diffArr = array_diff($predictArr, $targetArr);
    $maxVal = max($diffArr);
    $maxKey = array_search($maxVal, $diffArr);
    
    array_push($predictVal, $predictArr[$maxKey]);
    array_push($targetVal, $targetArr[$maxKey]);
    array_push($abnormalVal, $arr[$i * 61 + 60]);
      
}
$resultArr=array();
for($i=0;$i<count($predictVal);$i++){
    
    $result="[".$i.",".$predictVal[$i].",".$targetVal[$i].",".$abnormalVal[$i]."],";
    if($i==count($predictVal)-1)
        $result="[".$i.",".$predictVal[$i].",".$targetVal[$i].",".$abnormalVal[$i]."]";
    array_push($resultArr, $result);
} 

?>
<html>
<head>
<script type="text/javascript"
	src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', {'packages' : [ 'line' ]});
	google.charts.setOnLoadCallback(drawChart);
	function drawChart() {

		var data = new google.visualization.DataTable();
		data.addColumn('number', 'Order');
		data.addColumn('number', 'Predict Values');
		data.addColumn('number', 'Target Values');
		data.addColumn('number', 'AbnormalFlag');

		data.addRows([ <?php foreach($resultArr as $val){
    echo $val;}
    ?> ]);

		var options = {
			chart : {
				title : 'Predicted VS Target'
			},
			width : 900,
			height : 900
		};

		var chart = new google.charts.Line(document
				.getElementById('linechart_material'));

		chart.draw(data, google.charts.Line.convertOptions(options));
	}
</script>
</head>
<body>
	<div id="linechart_material" style="width: 900px; height: 500px"></div>
</body>
</html>


