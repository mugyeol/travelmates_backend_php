<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if( (($_SERVER['REQUEST_METHOD'] == 'POST')) || $android )
{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

    $currentuserid= $_POST['currentuserid'];
    $storyindex= $_POST['storyindex'];
    $replycontent = $_POST['replycontent'];
    $uploaddate=date('Y-m-d H:i:s');;  //Date


    if (empty($currentuserid) || empty($storyindex)){
        $errMSG = '올바르지 않은 정보입니다.';
    }
    if (empty($errMSG)){


        try{

            //like
//            if ($likeorcancel==1){
                // SQL문을 실행하여 데이터를 MySQL 서버의 person 테이블에 저장합니다.
                $stmt = $con->prepare('INSERT INTO reply (storyindex,userindex,replycontent,replydate) 
                                        VALUES(:storyindex, :userindex,:replycontent,:replydate)');

            $stmt->bindParam(':storyindex', $storyindex);
            $stmt->bindParam(':userindex', $currentuserid);
            $stmt->bindParam(':replycontent', $replycontent);
            $stmt->bindParam(':replydate', $uploaddate);
                $result = $stmt->execute();
            if($result){


                $lastinsertrow=$con->lastInsertId();
                $sql5="select reply.userindex,reply.id,username, userprofile, replycontent,replydate from reply
 left join userphoto on reply.userindex=userphoto.userindex right join userinfo on reply.userindex=userinfo.userindex where reply.id ='$lastinsertrow' limit 0,1";


                $stmt5 = $con->prepare($sql5);
                $stmt5->execute();

                if ($stmt5->execute()){
                    $row5=$stmt5->fetch(PDO::FETCH_ASSOC);

                        extract($row5);

                        $replyuserindex = $row5['userindex'];
                        $replyusername = $row5['username'];
                        $replyuserprofile = $row5['userprofile'];
                        $replycontent = $row5['replycontent'];
                        $replydate = $row5['replydate'];
                        $replyid = $row5['id'];


                        $replydata =   array(
                                "replyusername" => $replyusername,
                                "replyuserindex" => $replyuserindex,
                                "replyuserprofile"=>$replyuserprofile,
                                "replycontent"=>$replycontent,
                                "replydate"=>$replydate,
                                "replyid"=>$replyid

                            );

                    }
                }

        } catch(PDOException $e) {
            die("Database error: " . $e->getMessage());
        }

    }else{
        $errMSG = "error1";
    }
}

?>


<?php
if (!empty($errMSG)){
    echo $errMSG;

}else{
    header('Content-Type: application/json; charset=utf8');
    $json = json_encode($replydata, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    echo $json;


}

?>

