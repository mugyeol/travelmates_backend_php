<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');



//POST 값을 읽어온다.
$userindex=isset($_POST['userindex']) ? $_POST['userindex'] : '';
//$userindex=isset($_GET['userindex']) ? $_GET['userindex'] : '';


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if ($userindex != "" ){

    $sql="select userindex
          from userinfo where userindex='$userindex'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0){

        echo "'유저를 찾을 수 없습니다.";
    }
	else{
   		$data2 = array();

                $sql2="select userprofile 
                from userphoto LEFT JOIN userinfo ON userphoto.userindex = userinfo.userindex where userphoto.userindex='$userindex'";
                $stmt2 = $con->prepare($sql2);
                $stmt2->execute();
                while( $row2=$stmt2->fetch(PDO::FETCH_ASSOC)){
               	extract($row2);

                    array_push($data2,
                        array(
                        'userprofile'=>$row2["userprofile"]

                    ));
               }

        $sql="select username, userbirthyear,usergender,selfintro,profession,tripstyle,facebookid,instaid
          from userinfo where userindex='$userindex'";
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $row=$stmt->fetch(PDO::FETCH_ASSOC);

        extract($row);

        //태어난 년도로 나이 계산
        $userage = date('Y') - $row["userbirthyear"]+1;

        $data=
            array(
                'username'=>$row["username"],
                'usergender'=>$row["usergender"],
                'userage'=>$userage,
                'selfintro'=>$row["selfintro"],
                'profession'=>$row["profession"],
                'tripstyle'=>$row["tripstyle"],
                'facebookid'=>$row["facebookid"],
                'instaid'=>$row["instaid"],
                'userprofile'=>$data2

            );

            header('Content-Type: application/json; charset=utf8');
            $json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
            echo $json;
    }
}
else {
    echo "no userindex ";
}
