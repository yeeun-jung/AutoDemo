<?php
header('Content-Type: application/json; charset=utf8');

$fileName = "rnnOut.txt";
const LINE_SIZE=369;
$arr = array();
$predictVal = array();
$targetVal = array();
$abnormalVal = array();
$costVal= array();
$resultArr = array();
$rowArr = array();
$result=array();
$secArr=array();
if(isset($_GET['step'])){
    $step = $_GET['step'];
}else{
    $step=0;
}


function getFileLine($fp, $step)
{
   $inum = 0;
    fseek($fp, $step*LINE_SIZE, SEEK_SET);
    if(feof($fp)){
       return null;
    }
    $fstr = fgets($fp);
    $line = ftell($fp);
    while (($line % LINE_SIZE) != 0) {
        $inum = $line % LINE_SIZE;
        fseek($fp, - $inum, SEEK_CUR);
        sleep(3);
        $fstr = fgets($fp);
        $line = ftell($fp);
        
    }
    // 문자열 자르기 : 배열로 저장된다.
    $arr = preg_split("/[\s]+/", $fstr);
    $predictArr = array_slice($arr, 0, 30); // 0-29
    $targetArr = array_slice($arr, 30, 30); // 30-59
    $diffArr = array_diff($predictArr, $targetArr);
    $maxVal = max($diffArr);
    $maxKey = array_search($maxVal, $diffArr);
    if($targetArr==NULL||$arr==NULL){
        $targetArr[$maxKey]=0;
        $arr[61]=0;
        return null;
    }
    $resultArr= array(floatval($predictArr[$maxKey]),floatval($targetArr[$maxKey]),floatval($arr[60]),(int)$arr[61]);//predict target cost abnormal
    return $resultArr;
}

//encode php array to json string
$fp = fopen($fileName, "r");

     if(!feof($fp)){
        $arr=getFileLine($fp,$step); 
        
        if($arr!=NULL){            
            $arr= $arr;
        }        
    }

if($arr!=NULL)
    $step++;
fclose($fp);
$memberData = array(
    "rows" => $arr,
    "step" => $step
);

$resultJson = json_encode($memberData, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);

echo $resultJson;
?>