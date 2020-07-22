<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) -> jason parser task ( okhttp 라이브러리 활용) 시에 submit 체크 하면 에러
if( $_SERVER['REQUEST_METHOD'] == 'POST' || $android )
{

    // 채팅방 참여여부 확인
    // 참여 면 ok
    // 참여 아니면 보유 코인 전송


    $storyindex= isset($_POST['storyindex'])?$_POST['storyindex']:'';
    $userindex= isset($_POST['userindex'])?$_POST['userindex']:'';


    if (!empty($storyindex) && !empty($userindex)){
        try{

            //현재 참여 여부 확인.
            $stmt2 = $con->prepare("select id from chatroom where storyindex = '$storyindex' and userindex='$userindex'") ;
            $stmt2 -> execute();
            $rowcount = $stmt2->rowCount();

            if ($rowcount ==1){
                $result = array("checkjoin"=>"joined"); //이미 참여한 챗룸

                $stmt00 = $con->prepare("update chatroom set status = 1 where storyindex = '$storyindex' and userindex='$userindex'") ;
                $stmt00 -> execute();



            }else if ($rowcount ==0){

//                $stmt3 = $con->prepare("select coin from userinfo where userindex = '$userindex' ") ;
//                $stmt3 -> execute();
//                $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
//                extract($row3);

                //coin check
                $result = array("checkjoin"=>"firstjoin"
//                    "coin"=>$row3['coin']
                );


            }else{
                $result = array("error"=>"db error row count :: ".$rowcount."userindex : ".$userindex." / storyindex :: ".$storyindex);
            }



        } catch(PDOException $e) {
            echo $e;
            die("Database error: " . $e->getMessage());
        }


    }else{

        $result = array("error"=>"story, user index error");

    }
    header('Content-Type: application/json; charset=utf8');
    $json = json_encode($result, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    echo $json;


}

