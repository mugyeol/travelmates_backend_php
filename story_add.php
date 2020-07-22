<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) -> jason parser task ( okhttp 라이브러리 활용) 시에 submit 체크 할 수 없음
if( $_SERVER['REQUEST_METHOD'] == 'POST' || $android )
{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.
    date_default_timezone_set("Asia/Seoul");
    $uploaddate= date('Y-m-d H:i:s');            //Date

    $currentuserid= $_POST['userid'];
    $content= $_POST['content'];
    $country=  $_POST['country'];
    $city=  $_POST['city'];
    $sdate=  $_POST['startdate'];
    $startdate = date("Y-m-d",strtotime($sdate));
    $edate = $_POST['enddate'];
    $enddate = date("Y-m-d",strtotime($edate));
    $isforchat = $_POST['isforchat'];
    $chatmember= $currentuserid;

    $imageurl="";




    if (!empty($_FILES['uploaded_file'])){


        $image_file =  $_FILES['uploaded_file']['name'];
        $imgExt = strtolower(pathinfo($image_file,PATHINFO_EXTENSION)); // get image extension

        $img_size = $_FILES['uploaded_file']['size'];
        $tmp_dir = $_FILES['uploaded_file']['tmp_name'];

        if (!empty($image_file)){
            if($img_size < 5000000){

                $upload_dir = "storyimage/";
                $date = date('YmdHis');
                $image_name = "TravelMates_".$date."_".rand(1000,100000);
                $file_path = $upload_dir.$image_name.".".$imgExt;

                if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
                    // 동일한 파일명이면 덮어쓰기를 한다.
                    $imageurl = "http://106.10.53.132/".$file_path;



                } else{
                    $result = array("result" => "error","temp"=>$tmp_dir,"filepath"=>$file_path);
                    $errMSG = "error";

                }

            }else{
                $result = array("result" => "파일 용량이 너무 큽니다. 5MB 이하의 이미지 파일만 업로드 가능합니다");
                $errMSG = "error";


            }

        }

    }else{

        $imageurl="";


    }




    if (empty($currentuserid)){
        $result = array("result" => "유저 정보가 없습니다.");
        $errMSG = "유저정보 오류";
    }

    if (empty($errMSG)){

            try{

                // SQL문을 실행하여 데이터를 MySQL 서버의 person 테이블에 저장합니다.
                $stmt = $con->prepare('INSERT INTO story (userindex, content,country,city,startdate,enddate,isforchat,chatmember,uploaddate,photo) 
                                        VALUES(:userindex, :content, :country, :city, :startdate,:enddate,:isforchat,:chatmember,:uploaddate,:photo)');


                $stmt->bindParam(':userindex', $currentuserid);
                $stmt->bindParam(':content', $content);
                $stmt->bindParam(':country', $country);
                $stmt->bindParam(':city', $city);
                $stmt->bindParam(':startdate', $startdate);
                $stmt->bindParam(':enddate', $enddate);
                $stmt->bindParam(':isforchat', $isforchat);
                $stmt->bindParam(':chatmember', $chatmember);
                $stmt->bindParam(':uploaddate', $uploaddate);
                $stmt->bindParam(':photo', $imageurl);

                $resultcheck = $stmt->execute();
                $lastinsertrow=$con->lastInsertId();




                if($resultcheck){



                    $stmt3 = $con->prepare('INSERT INTO chatroom (storyindex, userindex) 
                                        VALUES(:storyindex, :userindex)');


                    $stmt3->bindParam(':userindex', $currentuserid);
                    $stmt3->bindParam(':storyindex',$lastinsertrow);
                    $resultcheck2 = $stmt3->execute();

                if ($resultcheck2){




                    $sql="select *from story LEFT JOIN userinfo ON story.userindex = userinfo.userindex where story.storyindex = '$lastinsertrow'";
                    $stmt = $con->prepare($sql);
                    $stmt->execute();


                        $row=$stmt->fetch(PDO::FETCH_ASSOC);

                            extract($row);
                            $userindex = $row['userindex'];
                            $storyindex = $row['storyindex'];

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

                        };
                }







            } catch(PDOException $e) {
                $errMSG =$e;
                die("Database error: " . $e->getMessage());
            }

    }

    if (!empty($errMSG)){
        echo $errMSG;

    }else{
        header('Content-Type: application/json; charset=utf8');
        $json = json_encode($data, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
        echo $json;


    }


}

