<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');

$userindex=isset($_POST['userindex']) ? $_POST['userindex'] : '';
$coin = isset($_POST['quantity']) ? $_POST['quantity'] : '';

if ($userindex != "" ){


    try{
        $stmt2 = $con->prepare("UPDATE userinfo SET coin = coin+$coin where userindex = '$userindex'");
        $result2 = $stmt2 -> execute();
        if ($result2){
            $stmt3 = $con->prepare("SELECT coin from userinfo where userindex = '$userindex'");
            $stmt3 -> execute();
            $row3=$stmt3->fetch(PDO::FETCH_ASSOC);
            extract($row3);

            $data  = array("resultcode"=>"success",
                "updatedcoin"=>$row3['coin']);


        }



    }catch(PDOException $e) {
        $data  = array("resultcode"=>$e);
        die("Database error: " . $e->getMessage());

    }

}
else {

    $data  = array("resultcode"=>"error");

}

header('Content-Type: application/json; charset=utf8');
$json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
echo $json;

?>

