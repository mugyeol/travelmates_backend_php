<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if( (($_SERVER['REQUEST_METHOD'] == 'POST')) || $android )
{


    $storyindex= $_POST['storyindex'];
    if (!empty($_POST['photopath'])){
        $imageurl= $_POST['photopath'];
        $trimrul = trim($imageurl);
    }


    if (empty($storyindex)){
        $result = "no storyindex";

    }else{


        $sql="delete from story where storyindex='$storyindex' ";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        if ($stmt->execute()){

            $result = "success";

            if (!empty($trimrul)){
                $upload_dir = "storyimage/";
                $deleteurl = substr($imageurl,strrpos($imageurl,'/')+1);
                unlink($upload_dir.$deleteurl);

            }else{

            }

        }


    }

}else{
    $result ="servererror";
}

echo $result;
