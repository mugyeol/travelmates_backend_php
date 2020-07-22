<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) -> jason parser task ( okhttp 라이브러리 활용) 시에 submit 체크 할 수 없음
if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{

    // 첫번째 채팅 참여


    $storyindex= isset($_POST['storyindex'])?$_POST['storyindex']:'';
    $userindex= isset($_POST['userindex'])?$_POST['userindex']:'';
    $status =  isset($_POST['status'])?$_POST['status']:0;



    try{
        $stmt = $con->prepare("update chatroom set status = $status where storyindex = '$storyindex' and userindex = '$userindex'  ");

        $result = $stmt -> execute();
        if ($result){
            $data ="success";
        }


    } catch(PDOException $e) {
//        $data = array("error"=>$e);
        $data ="error";
        die("Database error: " . $e->getMessage());
    }

//    header('Content-Type: application/json; charset=utf8');
//    $json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    echo $data;



}

