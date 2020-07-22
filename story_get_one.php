<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

if( $_SERVER['REQUEST_METHOD'] == 'POST'  )
{

    $currentuserid =isset($_POST['currentuserid'])?$_POST['currentuserid'] : '';
    $storyindex =isset($_POST['storyindex'])?$_POST['storyindex'] : '';

        try{


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

            $sql="select *from story LEFT JOIN userinfo ON story.userindex = userinfo.userindex where story.storyindex = '$storyindex'";
                    $stmt = $con->prepare($sql);
                    $stmt->execute();


                    $row=$stmt->fetch(PDO::FETCH_ASSOC);

                    extract($row);
                    $userindex = $row['userindex'];
                    $storyindex = $row['storyindex'];

                    $sql2="select userprofile from userphoto where userindex = '$userindex'";
                    $stmt2 = $con->prepare($sql2);

                    $userprofile="";

                    if ($stmt2->execute()){
                        $row2=$stmt2->fetch(PDO::FETCH_ASSOC);
                        extract($row2);
                        $userprofile = $row2['userprofile'];

                    }
                    $sql3="select id from liking where userindex = '$currentuserid' and storyindex ='$storyindex' ";
                    $stmt3 = $con->prepare($sql3);
                    $stmt3->execute();
                    if ($stmt3->rowCount()!=0){
                        $like = "1";

                    }else{
                        $like ="2";
                    }

                    $data=
                        array(
                            'userindex'=>$row["userindex"],
                            'storyindex'=>$row["storyindex"],
                            'username'=>$row["username"],
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

                        );
        } catch(PDOException $e) {
            $errMSG =$e;
            die("Database error: " . $e->getMessage());
        }
    header('Content-Type: application/json; charset=utf8');
    $json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
    echo $json;


    }





