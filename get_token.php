<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if ($_SERVER['REQUEST_METHOD'] == 'POST')
//{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

    $userindex= $_POST['userid'];
    $token= $_POST['token'];


    if (empty($userindex)){
        $result ="cannot get userindex";

    }else{


        $sql="update userinfo set token ='$token'where userindex = '$userindex' ";
        $stmt = $con->prepare($sql);
        if ($stmt->execute()){

            $result = "success";
        }


    }

//}else{
//    $result = "wrong access";
//
//}

echo $result;

?>

