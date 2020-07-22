<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$useremailid=isset($_POST['useremailid']) ? $_POST['useremailid'] : '';
$userpassword=isset($_POST['userpassword']) ? $_POST['userpassword'] : '';


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

if ($useremailid != "" && $userpassword !='' ){

    $sql="select userindex from userinfo  where userid='$useremailid' and userpassword = '$userpassword'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0){

        $result ="존재하지 않는 유저";

    }
    else{

        $row=$stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $result = "success".$row['userindex'];
        }
}
else {

    $result ="error";


}
echo $result;

?>


