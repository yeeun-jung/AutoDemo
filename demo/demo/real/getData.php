<?php
header('Content-Type: application/json; charset=utf8');

$fileName = "rnnOut.txt";
$arr = array();
$predictVal = array();
$targetVal = array();
$abnormalVal = array();
$resultArr = array();
$rowArr = array();
$result=array();
if(isset($_GET['step'])){
    $step = $_GET['step'];
}else{
    $step=0;
}


function getFileLine($fp, $step)
{
    $inum = 0;
    fseek($fp, $step*362, SEEK_SET);
    if(feof($fp)){
       return null;
    }
    $fstr = fgets($fp);
    $line = ftell($fp);
    while (($line % 362) != 0) {
        $inum = $line % 362;
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
        $arr[60]=0;
        return null;
    }
   /* $result = array(
        array(
            "v" => (int)($line / 362)
        ),
        array(
            "v" => $predictArr[$maxKey]
        ),
        array(
            "v" => $targetArr[$maxKey]
        ),
        array(
            "v" => $arr[60]
        )
    );
    */
 //   $resultArr["c"] = $result;
    $resultArr= array(((int)($line / 362)),floatval($predictArr[$maxKey]),floatval($targetArr[$maxKey]),(int)$arr[60]);
    return $resultArr;
}

$randomNum = mt_rand(0.9, 1.0);

$col0 = array(
    "id" => "x",
    "label" => "Order",
    "type" => "number"
);
$col1 = array(
    "id" => "predict",
    "label" => "Predict Values",
    "type" => "number"
);
$col2 = array(
    "id" => "target",
    "label" => "Target Values",
    "type" => "number"
);
$col3 = array(
    "id" => "flag",
    "label" => "AbnormalFlag",
    "type" => "number"
);

$colArr = array(
    $col0,
    $col1,
    $col2,
    $col3
);

//encode php array to json string
$fp = fopen($fileName, "r");

     if(!feof($fp)){
        $arr=getFileLine($fp,$step);     
        if($arr!=NULL){            
            array_push($rowArr, array($arr));
        }        
    }

if($arr!=NULL)
    $step++;
fclose($fp);
$memberData = array(
    "rows" => $rowArr,
    "step" => $step
);

$resultJson = json_encode($memberData, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);

echo $resultJson;
?>