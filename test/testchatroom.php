<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
header('Content-Type: application/json; charset=utf8');

include('../dbcon.php');


$member = array();

$membergroup = array();
//POST 값을 읽어온다.
$userindex="3";
$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if ($userindex != "" ){

    //유저가 참여한 채팅방 조회
    $sql="select storyindex from chatroom where userindex = '$userindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    //참여한 채팅 없음
    if ($stmt->rowCount() == 0){

        echo "have not joined any ";
    }
    //참여한 채팅 있음.
    else{
        $whole = array();
        while ($row=$stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<br/>";
            echo "while loop ";
            echo "<br/>";
            $data2 = array();
            $data = array();

            extract($row);
            $joinedchatroom = $row['storyindex'];//유저가 참여한 채팅방 id


            //유저가 참여한 채팅방의 여행 도시, 게시자 이름 추출
            $sql2 = "select city,username from story left join userinfo on story.userindex = userinfo.userindex 
                  where story.storyindex = '$joinedchatroom'";
            $stmt2 = $con->prepare($sql2);
            $stmt2->execute();

            //유저가 참여한 채팅방 정보 추출
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC); //storyindex 하나당 row 한줄이기 때문에 반복문 필요 없음.
            extract($row2);

            $array1 = array();

            array_push($data,
                array(
                    'username' => $row2["username"],
                    'storyindex' => $joinedchatroom,
                    'city' => $row2["city"]
                )
            );
            echo "<br/>";
            echo print_r($data);
            echo "<br/>";
            //유저가 참여한 채팅방에 참여한 유저정보 추출
            $sql3 = "select userindex from chatroom 
                                                 where storyindex = '$joinedchatroom'";


            $stmt3 = $con->prepare($sql3);
            $stmt3->execute();
            $rowcount = $stmt3->rowCount();


            echo "<br/>";
//            echo $rowcount;


            while ($row3 = $stmt3->fetch(PDO::FETCH_ASSOC)) {
                extract($row3);

                $userindex_prof = $row3['userindex'];

                $sql4 = "select userprofile from userphoto 
                                                 where userindex = '$userindex_prof'";
                $stmt4 = $con->prepare($sql4);
                $stmt4->execute();

                $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
                extract($row4);
                $userprofile = $row4['userprofile'];



                array_push($data2,
                    array(

                        "userprofile"=>$userprofile

                    ));
                echo "<br/>";

                echo print_r($data2);

                echo "<br/>";

                echo print_r($data);




            }
//            $data['profile']= $data2;
            array_push($whole,
                array("chatinfo"=>$data,"profile"=>$data2));

        }



            $json = json_encode(array("chatroom"=>$whole), JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
        echo "<br/>";
        echo "<br/>";
        echo $json;

    }








}else {

    echo "no userid";

}


?>

