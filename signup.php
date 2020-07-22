<?php

    error_reporting(E_ALL);
    ini_set('display_errors',1);

    include('dbcon.php');


    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {

        // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.


        $userid= $_POST['userid'];


        $username=  $_POST['username'];
        $userbirthyear=  $_POST['userbirthyear'];
        $usergender=  $_POST['usergender'];
        $userpassword = $_POST['userpassword'];

        $signuptype = $_POST['signuptype'];


        $userprofile= $_POST['userprofile'];

        //facebook login
        if ($signuptype==1 ){
            //페북 유저 프로필 존재 하는 경우
            if (isset($userprofile)) {
                $imageurl = $userprofile;
            }
            //유저 프로필 없는 경우 (없는경우 url이 어떻게 오는지는 아직 확인 안함)
            else {

                $old_path = "defaultprofileimgs/randomprofile".rand(1,39).".jpg";
                $upload_dir = "userimages/";
                $date = date('YmdHis');
                $imgExt = "jpg";
                $new_name= "TravelMates_".$date."_".rand(1000,100000);
                $new_path = $upload_dir.$new_name.".".$imgExt;

                //랜덤이미지 디렉토리 사진 복사
                $copied = copy($old_path , $new_path);
                if (!$copied)
                {
                    $errMSG = 'not copied';
                }
                else
                {
                    //이메일 유저 랜덤 이미지 경로
                    $imageurl = "http://106.10.53.132/".$new_path;

                }
            }

        }else if($signuptype==2){

            $old_path = "defaultprofileimgs/randomprofile".$userprofile.".jpg";
            $upload_dir = "userimages/";
            $date = date('YmdHis');
            $imgExt = "jpg";
            $new_name= "TravelMates_".$date."_".rand(1000,100000);
            $new_path = $upload_dir.$new_name.".".$imgExt;

            //랜덤이미지 디렉토리 사진 복사
            $copied = copy($old_path , $new_path);
            if (!$copied)
            {
                $errMSG = 'not copied';
            }
            else
            {
                //이메일 유저 랜덤 이미지 경로
                $imageurl = "http://106.10.53.132/".$new_path;

            }
        }




//        $userprofile= "http://106.10.53.132/userimages/TravelMates_20190108144500_88546.jpg";





        if (empty($userid)){
            $errMSG = '올바르지 않은 정보입니다.';
        }


        if (empty($errMSG)){

            $sql="select userindex from userinfo where userid='$userid'";
            $stmt = $con->prepare($sql);
            $stmt->execute();


            //이미 가입된 아이디면 페이스북로그인은 로그인 성공 이메일 로그인은 아이디 중복.
            if ($stmt->rowCount() != 0) {



                    if ($signuptype ==1){

                        $row=$stmt->fetch(PDO::FETCH_ASSOC);
                        extract($row);

                        $successMSG = "success".$row['userindex'];

                    }else if ($signuptype==2){
                        $errMSG = "duplicateid";
                    }



                }else{
                try{

                    // SQL문을 실행하여 데이터를 MySQL 서버의 person 테이블에 저장합니다.
                    $stmt = $con->prepare('INSERT INTO userinfo (userid, username,usergender
                                                    ,userbirthyear,userpassword) 
                                        VALUES(:userid, :username, :usergender, :userbirthyear,:userpassword)');


                    $stmt->bindParam(':userid', $userid);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':usergender', $usergender);
                    $stmt->bindParam(':userbirthyear', $userbirthyear);
                    $stmt->bindParam(':userpassword', $userpassword);

                    if($stmt->execute())
                    {
                        $sql3="select userindex from userinfo where userid='$userid'";
                        $stmt3 = $con->prepare($sql);
                        if ($stmt3->execute()){


                            $row=$stmt3->fetch(PDO::FETCH_ASSOC);
                            extract($row);

                            $userindex = $row['userindex'];


                            $stmt2 = $con->prepare('INSERT INTO userphoto (userindex, userprofile) VALUES(:userindex, :userprofile)');
                            $stmt2->bindParam(':userindex', $userindex);
                            $stmt2->bindParam(':userprofile',$imageurl);

                            if($stmt2->execute())
                            {
                                $successMSG = "success".$userindex;
                            }



                        };




                    }
                    else
                    {
                        $errMSG = "사용자 추가 에러";
                    }
                } catch(PDOException $e) {
                    die("Database error: " . $e->getMessage());
                }
            }
            }
    }

?>


<?php
    if (isset($errMSG)) echo $errMSG;
    if (isset($successMSG)) echo $successMSG;


?>

