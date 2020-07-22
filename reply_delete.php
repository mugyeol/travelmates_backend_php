<?php

error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon.php');


$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{


    $replyid= $_POST['replyid'];


    if (empty($replyid)){
        $result = "no replyid";

    }else{


        $sql="delete from reply where id='$replyid' ";
        $stmt = $con->prepare($sql);
        $stmt->execute();
        if ($stmt->execute()){

            $result = "success";


        }


    }

}else{
    $result ="servererror";
}

echo $result;
