<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('../dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) -> jason parser task ( okhttp 라이브러리 활용) 시에 submit 체크 할 수 없음





for($i=0; $i<90; $i++){
    $url =  "http://106.10.53.132/defaultprofileimgs/randomprofile";
    $profran = rand(0,38);
    $ext = ".jpg";

    $uploaddate= date('Y-m-d H:i:s');            //Date

        try{
            $A = "M";
            $B = "1991";
            $C = "1";
            $username = "user".$i;
                // SQL문을 실행하여 데이터를 MySQL 서버의 person 테이블에 저장합니다.
                $stmt = $con->prepare('INSERT INTO userinfo (userid, username,usergender
                                                    ,userbirthyear,userpassword) 
                                        VALUES(:userid, :username, :usergender, :userbirthyear,:userpassword)');


                $stmt->bindParam(':userid', $i);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':usergender', $A);
                $stmt->bindParam(':userbirthyear', $B);
                $stmt->bindParam(':userpassword', $C);
            $lastinsertrow=$con->lastInsertId();

                if($stmt->execute())
                {
                    $sql3="select userindex from userinfo where userid='$i'";
                    $stmt3 = $con->prepare($sql3);
                    if ($stmt3->execute()) {


                        $row = $stmt3->fetch(PDO::FETCH_ASSOC);
                        extract($row);

                        $userindex = $row['userindex'];
                        $userimg = $url . $profran . $ext;

                        $stmt2 = $con->prepare('INSERT INTO userphoto (userindex, userprofile) VALUES(:userindex, :userprofile)');
                        $stmt2->bindParam(':userindex', $userindex);
                        $stmt2->bindParam(':userprofile', $userimg);

                        if ($stmt2->execute()) {


                            $photoran = rand(0,38);
                            $country = "country".$i;
                            $city = "city".$i;
                            $yearran = rand(1,9);
                            $yearstart = "2019-04-0".$yearran;
                            $yearend = "2019-04-1".$yearran;
                            $isforchat = "1";
                            $content = "내용 ".$i;
                            $url = $url.$photoran.$ext;

                            $stmt = $con->prepare('INSERT INTO story (userindex, content,country,city,startdate,enddate,isforchat,chatmember,uploaddate,photo) 
                                        VALUES(:userindex, :content, :country, :city, :startdate,:enddate,:isforchat,:chatmember,:uploaddate,:photo)');



                            $stmt->bindParam(':userindex', $userindex);
                            $stmt->bindParam(':content',$content);
                            $stmt->bindParam(':country', $country);
                            $stmt->bindParam(':city',  $city);
                            $stmt->bindParam(':startdate',  $yearstart);
                            $stmt->bindParam(':enddate', $yearend);
                            $stmt->bindParam(':isforchat', $isforchat);
                            $stmt->bindParam(':chatmember', $i);
                            $stmt->bindParam(':uploaddate', $uploaddate);
                            $stmt->bindParam(':photo', $url);


                            if ($stmt->execute()){
                                $lastinsertrow=$con->lastInsertId();

                                $stmt7 = $con->prepare('INSERT INTO chatroom (storyindex, userindex) 
                                        VALUES(:storyindex, :userindex)');

                                $stmt7->bindParam(':userindex', $userindex);
                                $stmt7->bindParam(':storyindex',$lastinsertrow);
                                $stmt7->execute();


                            }




                        }

                    }

                    }





            }

        catch(PDOException $e) {
            $result = array("result" => "database error: ".$e);
            die("Database error: " . $e->getMessage());
        }




        }



