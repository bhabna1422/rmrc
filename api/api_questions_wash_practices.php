<?php
//require __DIR__.'/header.php';
header("Access-Control-Allow-Methods: POST, GET");
error_reporting(0);
$data = json_decode(file_get_contents('php://input'), TRUE);
$response = [];



function Survey(){
	
	$filepath = "json/questions_wash_practices.json";
	
	$response =  file_get_contents($filepath);
	
    return $response;
}

$response = Survey($data);			
header('Content-type: application/json');
echo $response;
?>