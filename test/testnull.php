<?php

echo "php return nothing for false, 1 for true";

echo '<br/>';
echo '<br/>';
echo '<br/>';

echo 'when var = "null"';
echo '<br/>';
$a = "null";
$b = empty($a);
$c = isset($a);
$d = is_null($a);
echo "empty ::::::::::::  ".$b;
echo '<br/>';
echo "isset ::::::::::::  ".$c;
echo '<br/>';
echo "isnull ::::::::::::  ".$d;
echo '<br/>';
echo '<br/>';
echo '<br/>';



echo 'when var = ""';
echo '<br/>';
$a = "";
$b = empty($a);
$c = isset($a);
$d = is_null($a);
echo "empty ::::::::::::  ".$b; //true
echo '<br/>';
echo "isset ::::::::::::  ".$c; //true
echo '<br/>';
echo "isnull ::::::::::::  ".$d; //false
echo '<br/>';
echo '<br/>';
echo '<br/>';

$sdate = "Feb 18,2019";
$startdate = date("Y-m-d",strtotime($sdate));
echo $startdate;

echo '<br/>';
echo '<br/>';
echo '<br/>';
echo '<br/>';
echo '<br/>';

date_default_timezone_set("Asia/Seoul");
$uploaddate= date('Y-m-d H:i:s');            //Date
echo $uploaddate;
?>

