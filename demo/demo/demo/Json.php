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
// === php array to json ===
for($i=0;$i<count($predictVal);$i++){
    $result = array ($predictVal[$i], $targetVal[$i], $abnormalVal[$i]);
    array_push($resultArr, $result);
    
}   

// encode php array to json string
$resultJson = json_encode($resultArr);

// echo. 결과는 같다.
echo $resultJson;

// handle error
if (json_last_error() > 0) {
    echo json_last_error_msg() . PHP_EOL;
}
?>