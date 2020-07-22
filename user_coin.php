<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$userindex=isset($_POST['userindex']) ? $_POST['userindex'] : '';


if ($userindex != "" ){

                $stmt3 = $con->prepare("select coin from userinfo where userindex = '$userindex' ") ;
                $stmt3 -> execute();
                $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
                extract($row3);
                    $result = array(
                    "coin"=>$row3['coin']
    );


}else {

    $result = array(
        "error"=>"no user found"
    );

}
header('Content-Type: application/json; charset=utf8');
$json = json_encode($result, JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
echo $json;



?>

