<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) -> jason parser task ( okhttp 라이브러리 활용) 시에 submit 체크 할 수 없음
if( $_SERVER['REQUEST_METHOD'] == 'POST' || $android )
{

    // 첫번째 채팅 참여


    $storyindex= isset($_POST['storyindex'])?$_POST['storyindex']:'';
    $userindex2= isset($_POST['userindex'])?$_POST['userindex']:'';
    $join = 1;



    try{
            $stmt = $con->prepare('INSERT INTO chatroom (storyindex, userindex, status)
                                        VALUES(:storyindex, :userindex, :status)');

            $stmt->bindParam(':storyindex', $storyindex);
            $stmt->bindParam(':userindex', $userindex2);
            $stmt->bindParam(':status', $join);


            $result = $stmt -> execute();
            if($result){
                $stmt2 = $con->prepare("UPDATE userinfo SET coin = coin-3 where userindex = '$userindex2'");

                $result2 = $stmt2 -> execute();
                if ($result2){


                    //chatroom 참여인원 token 알아내기
                    $sql="select userindex from chatroom where storyindex = '$storyindex'";
                    $stmt = $con->prepare($sql);
                    $stmt->execute();

                    $data = array();
                    while ($row3=$stmt->fetch(PDO::FETCH_ASSOC)) {
                        extract($row3);
                        $chatmemberindex = $row3['userindex'];

                        if ($userindex2 !== $chatmemberindex){


                            $sql5="select token,userindex from userinfo where userindex= '$chatmemberindex'";
                            $stmt5 = $con->prepare($sql5);
                            $stmt5->execute();
                            $row4=$stmt5->fetch(PDO::FETCH_ASSOC);
                            extract($row4);

                            array_push($data,
                                array("token"=>$row4['token'],"userindex"=>$row4['userindex']));
                        }

                    }


                    //참여하는 유저 이름 알아내기
                    $sql="select username from userinfo where userindex = '$userindex2'";
                    $stmt = $con->prepare($sql);
                    $stmt->execute();
                    $row=$stmt->fetch(PDO::FETCH_ASSOC);
                    extract($row);
                    $username = $row['username'];

                    //도시 이름 알아내기
                    $sql="select city from story where storyindex= '$storyindex'";
                    $stmt = $con->prepare($sql);
                    $stmt->execute();
                    $row2=$stmt->fetch(PDO::FETCH_ASSOC);
                    extract($row2);
                    $city = $row2['city'];

                    $resultdata = array("result"=>"success","city"=>$row2['city'],"name"=>$row['username'],"token"=>$data);


                }

            }else{
                $result= "error";
            }




    } catch(PDOException $e) {
        $resultdata = array("result"=>"error","username"=>"","token"=>"");
        die("Database error: " . $e->getMessage());
    }



    header('Content-Type: application/json; charset=utf8');
    $json = json_encode($resultdata, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    echo $json;
}

