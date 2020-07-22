<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if( (($_SERVER['REQUEST_METHOD'] == 'POST') ) || $android )
{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

    $userindex= $_POST['userindex'];
    $storyindex= $_POST['storyindex'];
    $likeorcancel = $_POST['likeorcancel'];

    if (empty($userindex) || empty($storyindex)){
        $errMSG = '올바르지 않은 정보입니다.';
    }
    if (empty($errMSG)){


        try{

            //like
            if ($likeorcancel==1){
                // SQL문을 실행하여 데이터를 MySQL 서버의 person 테이블에 저장합니다.
                $stmt = $con->prepare('INSERT INTO liking (userindex, storyindex) 
                                        VALUES(:userindex, :storyindex)');


                $stmt->bindParam(':userindex', $userindex);
                $stmt->bindParam(':storyindex', $storyindex);
                $result = $stmt->execute();
            }else{
                $stmt = $con->prepare("delete from liking where userindex='$userindex' and storyindex = '$storyindex'");

                $result = $stmt->execute();
            }





            if($result){

                $sql2="select id from liking where storyindex = '$storyindex'";
                $stmt2 = $con->prepare($sql2);
                $stmt2->execute();

                    $totalike = $stmt2->rowCount();
                    $stmt3 = $con->prepare("update story set likecount ='$totalike'where storyindex= '$storyindex'");
                    $result3 = $stmt3 -> execute();
                    if ($result3){
                        $successMSG = "success".$totalike;

                    }


            }else{
            $errMSG = "error2";
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
if (isset($errMSG)) echo $errMSG;
if (isset($successMSG)) echo $successMSG;


?>

