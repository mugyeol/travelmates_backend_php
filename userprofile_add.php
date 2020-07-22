<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');

$image_file =  $_FILES['uploaded_file']['name'];
$imgExt = strtolower(pathinfo($image_file,PATHINFO_EXTENSION)); // get image extension

$img_size = $_FILES['uploaded_file']['size'];
$tmp_dir = $_FILES['uploaded_file']['tmp_name'];
$userindex = $_POST['userid'];


if (!empty($image_file)){
    if($img_size < 5000000){

    $upload_dir = "userimages/";
    $date = date('YmdHis');
    $image_name = "TravelMates_".$date."_".rand(1000,100000);
    $file_path = $upload_dir.$image_name.".".$imgExt;

    if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $file_path)) {
        // 동일한 파일명이면 덮어쓰기를 한다.
        $imageurl = "http://106.10.53.132/".$file_path;

        $stmt = $con->prepare('INSERT INTO userphoto (userindex, userprofile) VALUES(:userindex, :userprofile)');
        $stmt->bindParam(':userindex', $userindex);
        $stmt->bindParam(':userprofile', $imageurl);
        if($stmt->execute()){
            $result = array("result" => "success","imageurl" => $imageurl);
        }


    } else{
        $result = array("result" => "error","temp"=>$tmp_dir,"filepath"=>$file_path);
    }

    }else{
        $result = array("result" => "파일 용량이 너무 큽니다. 5MB 이하의 이미지 파일만 업로드 가능합니다");


    }

}else{
    $result = array("result" => "업로드 할 파일을 찾을 수 없습니다.");

}



echo json_encode($result);
?>

