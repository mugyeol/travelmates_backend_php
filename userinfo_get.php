<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$userindex=isset($_POST['userid']) ? $_POST['userid'] : '';
$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if ($userindex != "" ){

    $sql="select username, userprofile
          from userinfo LEFT JOIN userphoto ON userinfo.userindex = userphoto.userindex where userinfo.userindex='$userindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0){

        echo "'cannot find user";
    }
    else{
       $row=$stmt->fetch(PDO::FETCH_ASSOC);

            extract($row);
            $data=
                array(
                    'userprofile'=>$row["userprofile"],
                    'currentusername'=>$row["username"]
                );
        }
            header('Content-Type: application/json; charset=utf8');
            $json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
            echo $json;
    }

else {

    echo "no userid";

}


?>

