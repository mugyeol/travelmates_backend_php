<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


//if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android ) -> jason parser task ( okhttp 라이브러리 활용) 시에 submit 체크 할 수 없음
if( $_SERVER['REQUEST_METHOD'] == 'POST' || $android )
{

    // 안드로이드 코드의 postParameters 변수에 적어준 이름을 가지고 값을 전달 받습니다.

    $imageurl="";

    $storyindex= isset($_POST['storyindex'])?$_POST['storyindex']:'';
    $content=isset($_POST['content'])?$_POST['content']:'';
    $isforchat = isset($_POST['isforchat'])?$_POST['isforchat']:'';


    if (!empty($_POST["isClicked"])){
        $isClicked = $_POST['isClicked'];
    }
    $oldimagepath=isset($_POST['oldimagepath']) ? trim($_POST['oldimagepath']) : '';

    $dir = "storyimage/";

// 새로운 이미지 업로드 하는 경우
    if (!empty($_FILES['uploaded_file'])){

        //기존 이미지 있던 경우 해당 이미지 unlink
        if (!empty($oldimagepath) &&  $oldimagepath!=="fakestring"){
            $deleteurl = substr($oldimagepath,strrpos($oldimagepath,'/')+1);
            unlink($dir.$deleteurl);
        }
//새로운 이미지 저장
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
                    $result = "error";
                }
            }else{
                $result = "error";
            }
        }
    }
    //이미지 포스팅 안하는 경우
    else{

        //기존에 이미지 있던 게시물일 경우
        if (!empty($oldimagepath) &&  $oldimagepath!=="fakestring"){

            //이미지 없앤 경우
            if ($isClicked==="1"){
                $deleteurl = substr($oldimagepath,strrpos($oldimagepath,'/')+1);
                unlink($dir.$deleteurl);
                $imageurl="";
            }
            //이미지 그대로 둔 경우 -> 이전 이미지경로 그대로 db 업데이트
            else if ($isClicked==="2"){
                $imageurl = $oldimagepath;
            }
        }
        //기존에 이미지 없었고 수정 과정에서 이미지 추가도 없음.
        else{
            $imageurl = "";
        }

    }





        try{

            $stmt = $con->prepare("update story set 
                                                     content = '$content',
                                                     isforchat = '$isforchat',
                                                     photo = '$imageurl'
                                                     where storyindex = '$storyindex'" );
                 $result = $stmt -> execute();
            if($result){
                $result = "success";
            }
        } catch(PDOException $e) {
            $result = $e;
            die("Database error: " . $e->getMessage());
        }



        echo $result;
}

