<?php
$date = "2019-1-19";
$date =date('Y-m-d',strtotime($date));

echo $date;

echo '<br/>';
echo '<br/>';
echo '<br/>';
echo '<br/>';

echo "계산 테스트";
$a = 15-(15%10);
echo "15-(15%10) === ".$a;
echo '<br/>';
echo '<br/>';
echo '<br/>';
$a = (15-(15%10))/10;


echo "(15-(15%10))/10 === ".$a;
echo '<br/>';
echo '<br/>';
$a = ceil(15/10);
echo "15/10 올림 === ".$a;

echo '<br/>';
echo '<br/>';
$a = ceil(10/10);
echo "10/10 올림 === ".$a;

echo '<br/>';
echo '<br/>';
$a = ceil(7/10);
echo "7/10 올림 === ".$a;



?>