<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$calleeindex=isset($_POST['calleeindex']) ? $_POST['calleeindex'] : ''; //callee index
$callerindex=isset($_POST['callerindex']) ? $_POST['callerindex'] : ''; //callee index


if ($calleeindex != "" && $callerindex!="" ){

    $sql="select token from userinfo where userindex='$calleeindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $row=$stmt->fetch(PDO::FETCH_ASSOC);
    extract($row);

    $sql2="select username from userinfo where userindex='$callerindex'";
    $stmt2 = $con->prepare($sql2);
    $stmt2->execute();
    $row2=$stmt2->fetch(PDO::FETCH_ASSOC);
    extract($row2);

    $sql3="select userprofile from userphoto where userindex='$callerindex'";
    $stmt3 = $con->prepare($sql3);
    $stmt3->execute();
    $row3=$stmt3->fetch(PDO::FETCH_ASSOC);
    extract($row3);




    $data = array(

        "token"=>$row['token'],
        "callername"=>$row2['username'],
        "callerprofile"=>$row3['userprofile']
    );


}

else {


    $data = array(

        "token"=>"error"
    );

}
header('Content-Type: application/json; charset=utf8');
$json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
echo $json;

?>

