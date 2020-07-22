<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
$userindex=isset($_POST['userindex']) ? $_POST['userindex'] : '';


if ($userindex != "" ){

    $sql="select coin, userprofile
          from userinfo LEFT JOIN userphoto ON userinfo.userindex = userphoto.userindex where userinfo.userindex='$userindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0){

        $data=
            array(
                'error'=>'no userinfo'
            );
    }
    else{
        $row=$stmt->fetch(PDO::FETCH_ASSOC);

        extract($row);
        $data=
            array(
                'userprofile'=>$row["userprofile"],
                'coin'=>$row['coin']
            );


    }

}

else {
    $data=
        array(
         'error'=>'no userid'
        );


}
header('Content-Type: application/json; charset=utf8');
$json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
echo $json;

?>

