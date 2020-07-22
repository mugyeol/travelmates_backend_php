<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$storyindex=isset($_POST['storyindex']) ? $_POST['storyindex'] : '';
$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if ($storyindex != "" ){

    //유저가 참여한 채팅방 조회
    $sql="select city from story where storyindex= '$storyindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //get city

    //유저가 참여한 채팅방 조회
    $sql="select userindex from chatroom where storyindex= '$storyindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    //get involved userindex


    $data = array();
    while ($row2 = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row2);
        $userindex = $row2['userindex'];//유저가 참여한 채팅방 id

        //유저가 참여한 채팅방에 참여한 유저정보 추출
        $sql3 = "select userindex,username,userbirthyear,usergender,selfintro,facebookid,instaid from userinfo where userindex= '$userindex'";
        $stmt3 = $con->prepare($sql3);
        $stmt3->execute();

        $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        extract($row3);
        $userage = date('Y') - $row3["userbirthyear"]+1;

        $sql4 = "select userprofile from userphoto where userindex= '$userindex'";
        $stmt4 = $con->prepare($sql4);
        $stmt4->execute();

        $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        extract($row4);


        array_push($data,
            array(
                "userindex"=>$row3['userindex'],
                "username"=>$row3['username'],
                "userage"=>$userage,
                "usergender"=>$row3['usergender'],
                "selfintro"=>$row3['selfintro'],
                "facebookid"=>$row3['facebookid'],
                "instaid"=>$row3['instaid'],
                "userprofile"=>$row4['userprofile'],
            )
        );
    }

    header('Content-Type: application/json; charset=utf8');
    $json = json_encode(array("city"=>$row['city'],"involveduserdata"=>$data), JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
    echo $json;



}else {

    echo "no storyindex";

}


?>

