<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$userindex=isset($_POST['userindex']) ? $_POST['userindex'] : '';
$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if ($userindex != "" ){

    //유저가 참여한 채팅방 조회
    $sql="select storyindex from chatroom where userindex= '$userindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    $data = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        array_push($data,
            array("storyindex"=>$row['storyindex']
            )
        );
    }

    header('Content-Type: application/json; charset=utf8');
    $json = json_encode(array("chatroomlist"=>$data), JSON_PRETTY_PRINT + JSON_UNESCAPED_UNICODE);
    echo $json;



}else {

    echo "no userindex";

}


?>

