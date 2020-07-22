<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if( (($_SERVER['REQUEST_METHOD'] == 'POST')) || $android )
{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

    $userindex= $_POST['currentuserindex'];
    $imageurl= $_POST['imageurl'];


    if (empty($userindex)){
        $result ="cannot get userindex";

    }else{


        $sql="delete from userphoto where userindex='$userindex' and userprofile = '$imageurl'";
            $stmt = $con->prepare($sql);
            $stmt->execute();
            if ($stmt->execute()){

                $upload_dir = "userimages/";
//                $id = substr($url, strrpos($url, '/') + 1);
                $deleteurl = substr($imageurl,strrpos($imageurl,'/')+1);
//                $result = array("result" => $deleteurl);


                unlink($upload_dir.$deleteurl);

                $result = "success";
            }


    }

}else{
    $result = "wrong access";

}

echo $result;

?>

