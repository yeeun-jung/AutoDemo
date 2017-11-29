<?php
header('Content-Type: application/json; charset=utf8');
const BINARY_DOCUMENT = "rnnIn.wav";

const HEADER_SIZE = 44;

const BINARY_SIZE = 3;
 // 한번에 8개씩, 64단위로...
const BINARY_ORDER = 64;

const BINARY_STACK = 512;

if (isset($_GET['step'])) {
    $step = $_GET['step'];
} else {
    $step = 0;
}
$fp = fopen(BINARY_DOCUMENT, 'rb'); // @fopen도 가능
if (! $fp) {
    echo 'Could not processed!</body></html>';
    exit();
}
$header = fread($fp, HEADER_SIZE);
 // header 44+3x
function getFileLine($fp, $step)
{
    $data = array();
    $rows= array();
    fseek($fp, ($step * BINARY_SIZE * BINARY_STACK), SEEK_CUR);
    if (feof($fp)) {
        return null;
    }
    $fstr = fread($fp, BINARY_SIZE * BINARY_STACK); // 한 블럭
    for ($i = 0; $i < BINARY_STACK/BINARY_ORDER; $i ++) {
        $str = substr($fstr, BINARY_ORDER * BINARY_SIZE * $i, 3);
        
        $temp = unpack('i', "\x00" . $str);
        if ($temp[1] < 0) {
            $data = unpack('i', $str . "\xff");
        } else {
            $data = unpack('i', $str . "\x00");
        }
        array_push($rows, $data[1] / 2 ** 23);
    }
    return $rows;
    

}
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