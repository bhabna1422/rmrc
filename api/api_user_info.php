<?php
require __DIR__ . '/header.php';
header("Access-Control-Allow-Methods: POST");
mysqli_set_charset($connect,'utf8');


$data = $_POST;
$response = [];
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function savewelldata($data)
{
	$secret_key = "secret";
	$authHeader = getAuthorizationHeader();
	$arr = explode(" ", trim($authHeader));
	$jwt = $arr[1];
	try {
		//Decode JWT Token
		$decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
		$decoded_array = (array) $decoded;
		//Get id from JWT Token
		$userid = $decoded_array['data']->userid;
		$name = $decoded_array['data']->name;
		
		$response['status'] = "success";
		$response['userid'] = $userid;
		$response['name'] = $name;
		
	} catch (\Firebase\JWT\ExpiredException $e) {
		http_response_code(400);

		$response['status'] = 'error';

		$response['message'] = $e->getMessage();
	}


    return $response;
}

$response = savewelldata($data);

//echo "SELECT * FROM countries";

header('Content-type: application/json');

echo json_encode($response);
?>

