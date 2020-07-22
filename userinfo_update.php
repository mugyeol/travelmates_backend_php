<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if( (($_SERVER['REQUEST_METHOD'] == 'POST')) || $android )
{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

    $userindex = $_POST['userid'];
    $birthyear= $_POST['birthyear'];
    $selfintro=  $_POST['selfintro'];
    $profession=  $_POST['profession'];
    $tripstyle=  $_POST['tripstyle'];
    $facebookid = $_POST['facebookid'];
    $instaid = $_POST['instaid'];



    if (empty($userindex)){
//        $result = array("result" => "cannot find userindex");
        $result = "cannot get userindex";
    }

    if(empty($result)){
            try{

                $stmt = $con->prepare("update userinfo set  userbirthyear ='$birthyear',selfintro = '$selfintro',profession='$profession',tripstyle ='$tripstyle',facebookid='$facebookid',instaid='$instaid' where userindex = '$userindex'");

                $result = $stmt -> execute();

                    if($result)
                    {
                        $result = "success";
                    }


                else
                {
                                            $result = "사용자 추가 에러";

                }
            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage());


            }
        }


}else{
    $result = "server error";

}

echo $result;

?>

