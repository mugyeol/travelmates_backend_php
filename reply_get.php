<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');

$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

$storyindex = $_POST['storyindex'];
$currentuserid = $_POST['currentuserid'];

$page = isset($_POST['page'])?$_POST['page']:'';
$addedItemCount = isset($_POST['addeditemcount'])?$_POST['addeditemcount']:'';




if ($page==1){
    $startindex = 0;

}else{
    if ($addedItemCount<10){
        $startindex = ($page-1)*10+$addedItemCount;

    }
}

$result_per_page =10;

$sql8="select id from reply where storyindex = '$storyindex' ";
$stmt8 = $con->prepare($sql8);
$stmt8->execute();

//최초 20개 였는데 1개 아이템 추가되서 21개 되면 토탈 페이지가 3페이지가 된다.
//하지만 추가된 아이템 한개는 이미 로딩 된 상태.

$total = $stmt8->rowCount();
if ($addedItemCount>0){
$total = $total-$addedItemCount;
}
$totalpage= ceil($total/10);




//if ($totalpagecal<1){
//    $totalpage = 1;
//}else{
//    $totalpage = $totalpagecal;
//}
//



$sql5="select id,replycontent,replydate,userindex from reply  where storyindex ='$storyindex' order by reply.replydate desc LIMIT $startindex,$result_per_page ";
$stmt5 = $con->prepare($sql5);
//$stmt5->execute();

$replydata = array();


if ($stmt5->execute()){
    while ($row5=$stmt5->fetch(PDO::FETCH_ASSOC)){

        extract($row5);

        $replycontent = $row5['replycontent'];
        $replydate = $row5['replydate'];
        $replyuserindex = $row5['userindex'];
        $replyid = $row5['id'];

        $sql7="select username,userprofile from userinfo left join userphoto on userinfo.userindex = userphoto.userindex  where userinfo.userindex ='$replyuserindex'" ;
        $stmt7 = $con->prepare($sql7);
        $stmt7->execute();
        $row7 = $stmt7->fetch(PDO::FETCH_ASSOC);
        extract($row7);
        $replyusername = $row7['username'];
        $replyuserprofile = $row7['userprofile'];

        array_push($replydata,
            array(
                "replyusername" => $replyusername,
                "replyuserprofile"=>$replyuserprofile,
                "replycontent"=>$replycontent,
                "replydate"=>$replydate,
                "replyuserindex"=>$replyuserindex,
                "replyid"=>$replyid


            ));

    }
}


$sql4="select userprofile from userinfo LEFT JOIN userphoto ON userinfo.userindex = userphoto.userindex where userinfo.userindex = '$currentuserid'";
$stmt4 = $con->prepare($sql4);
$stmt4->execute();
$currentuserprofile="";
if ($stmt4->execute()){

    $row4=$stmt4->fetch(PDO::FETCH_ASSOC);
    extract($row4);

    $currentuserprofile = $row4['userprofile']; //uploaderuserprofile
    $storydata=
        array(
            'page'=>$page,
            'totalpage'=>$totalpage,
            'currentuserprofile'=>$currentuserprofile,

            'reply'=>$replydata

        );
}



header('Content-Type: application/json; charset=utf8');
$json = json_encode($storydata, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
echo $json;
