<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

include('dbcon_test.php');

//POST 값을 읽어온다.
$country=isset($_POST['country']) ? $_POST['country'] : '';
$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


if ($country != "" ){

    $sql="select * from person where country='$country'";
    $stmt = $con->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() == 0){

        echo "'";
        echo $country;
        echo "'은 찾을 수 없습니다.";
    }
	else{

   		$data = array();

        while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

        	extract($row);

            array_push($data,
                array('id'=>$row["id"],
                'name'=>$row["name"],
                'country'=>$row["country"]
            ));
        }


        if (!$android) {
            echo "<pre>";
            print_r($data);
            echo '</pre>';
        }else
        {
            header('Content-Type: application/json; charset=utf8');
            $json = json_encode(array("webnautes"=>$data), JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
            echo $json;
        }
    }
}
else {
    echo "검색할 나라를 입력하세요 ";
}

?>



<?php

$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

if (!$android){
?>

<html>
   <body>

      <form action="<?php $_PHP_SELF ?>" method="POST">
         나라 이름: <input type = "text" name = "country" />
         <input type = "submit" />
      </form>

   </body>
</html>
<?php
}


?>

