<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) -> jason parser task ( okhttp 라이브러리 활용) 시에 submit 체크 할 수 없음
if( $_SERVER['REQUEST_METHOD'] == 'POST' || $android )
{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.


    $storyindex= isset($_POST['storyindex'])?$_POST['storyindex']:'';
    $userindex= isset($_POST['userindex'])?$_POST['userindex']:'';




    try{

        //현재 참여 여부 확인.
        $stmt2 = $con->prepare("select id from chatroom where storyindex = '$storyindex' and userindex='$userindex'") ;
        $stmt2 -> execute();
        $rowcount = $stmt2->rowCount();

        if ($rowcount ==1){
            $result = "join"; //이미 참여한 챗룸
        }else if ($rowcount ==0){


            // SQL문을 실행하여 데이터를 MySQL 서버의 person 테이블에 저장합니다.
            $stmt = $con->prepare('INSERT INTO chatroom (storyindex, userindex) 
                                        VALUES(:storyindex, :userindex)');


            $stmt->bindParam(':storyindex', $storyindex);
            $stmt->bindParam(':userindex', $userindex);
            $result = $stmt -> execute();
            if($result){
                $result = "firstjoin"; //처음 참여
            }

        }else{
            $result ="error";
        }



    } catch(PDOException $e) {
        $result = $e;
        die("Database error: " . $e->getMessage());
    }



    echo $result;
}

