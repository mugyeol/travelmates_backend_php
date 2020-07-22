<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');

$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

//$currentuserid = isset($_GET['currentuserid'])? $_GET['currentuserid'] : '';
//$page = isset($_GET['page'])?$_GET['page']:'';
//
$currentuserid = isset($_POST['currentuserid'])? $_POST['currentuserid'] : '';
$page = isset($_POST['page'])?$_POST['page']:'';
$addedItemCount = isset($_POST['addeditemcount'])?$_POST['addeditemcount']:'';
$city = isset($_POST['city'])?$_POST['city']:'';
$sdate= isset($_POST['startdate'])?$_POST['startdate']:'';

$startdate ='';
if (!empty($sdate)){
    $startdate = date("Y-m-d",strtotime($sdate));
}

$sqlcondition ="";
if ($page==1){
    $startindex = 0;

}else{
    $startindex = ($page-1)*10+$addedItemCount;
}
$result_per_page =10;

$sql8="select storyindex from story";
$sql="select *from story order by uploaddate  desc, userindex desc LIMIT $startindex,$result_per_page";


//city & startdate 둘다 선택
if (!empty($city) && !empty($startdate)){

    $sql8="select storyindex from story where city = '$city' and  enddate > '$startdate' and startdate<='$startdate'";
    $sql="select * from story where city = '$city' and enddate > '$startdate' and startdate<='$startdate' order by startdate , userindex desc LIMIT $startindex,$result_per_page";

    //startdate 만 선택
}else if (!empty($startdate)){
    $sql8="select storyindex from story where enddate > '$startdate' and startdate<='$startdate'";
    $sql="select * from story where enddate > '$startdate' and startdate <='$startdate'order by startdate , userindex desc LIMIT $startindex,$result_per_page";

}//도시만 선택

else if (!empty($city)){
    $sql8="select storyindex from story where city = '$city'";
    $sql="select * from story where city = '$city' order by uploaddate  desc, userindex desc LIMIT $startindex,$result_per_page";

}
// 선택 안함
else{
    $sql8="select storyindex from story";
    $sql="select *from story order by uploaddate  desc, userindex desc LIMIT $startindex,$result_per_page";

}


$stmt8 = $con->prepare($sql8);
$stmt8->execute();
$total = $stmt8->rowCount();
$totalpage = ceil($total/10);

//$totalpage = ($total-($total)%10)/10+1;



//$sql="select *from story".$sqlcondition." order by uploaddate  desc, userindex desc LIMIT $startindex,$result_per_page";
$stmt = $con->prepare($sql);
$stmt->execute();

if($stmt->rowCount()==0){

    echo "no story";

}else{


    $data = array();
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);
        $userindex = $row['userindex'];
        $storyindex = $row['storyindex'];

        $sql2="select userprofile from userphoto where userindex = '$userindex'";
        $stmt2 = $con->prepare($sql2);
        $userprofile="";
        if ($stmt2->execute()){
            $row2=$stmt2->fetch(PDO::FETCH_ASSOC);
            extract($row2);

        }
        $sql11="select username from userinfo where userindex = '$userindex'";
        $stmt11 = $con->prepare($sql11);
        $stmt11->execute();
        $row11=$stmt11->fetch(PDO::FETCH_ASSOC);
        extract($row11);

        $sql4="select userindex from chatroom where storyindex = '$storyindex'"; //한개의 스토리에 참여한 유저 인덱스
        $stmt4 = $con->prepare($sql4);
        $stmt4->execute();
        $chatmemberdata = array();

        while($row4=$stmt4->fetch(PDO::FETCH_ASSOC)){
            extract($row4);
            $chatmembers = $row4['userindex'];
            $sql10="select userprofile from userphoto where userindex = '$chatmembers' limit 0,1"; //한개의 스토리에 참여한 유저 인덱스
            $stmt10 = $con->prepare($sql10);
            $stmt10->execute();
            $row10=$stmt10->fetch(PDO::FETCH_ASSOC);
            extract($row10);

            array_push($chatmemberdata,
                array(  'chatmemebers'=>$row4['userindex'],
                    'chatmemberprof'=>$row10['userprofile']
                ));
        }



        $sql3="select id from liking where userindex = '$currentuserid' and storyindex ='$storyindex' ";
        $stmt3 = $con->prepare($sql3);
        $stmt3->execute();
        if ($stmt3->rowCount()!=0){
            $like = "1";

        }else{
            $like ="2";
        }

        array_push($data,
            array(
                'userindex'=>$row['userindex'],
                'storyindex'=>$row['storyindex'],
                'username'=>$row11["username"],
                'country'=>$row["country"],
                'city'=>$row["city"],
                'startdate'=>$row["startdate"],
                'enddate'=>$row["enddate"],
                'content'=>$row["content"],
                'isforchat'=>$row["isforchat"],
                'likecount'=>$row["likecount"],
                'replycount'=>$row["replycount"],
                'userprofile'=>$row2['userprofile'],
                'like'=>$like,
                'photo'=>$row['photo'],
                'chatdata'=>$chatmemberdata


            ));

    };

    header('Content-Type: application/json; charset=utf8');
    $json = json_encode(array("totalpage"=>$totalpage,"story"=>$data), JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    echo $json;



}



?>

