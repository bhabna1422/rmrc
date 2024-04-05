<?php

require __DIR__.'/header.php';


header("Access-Control-Allow-Methods: POST");

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Rakit\Validation\Validator;

$data = json_decode(file_get_contents('php://input'), true);
$response = [];
//print_r($data);


function Login($data)
{
    $username 	= $data['username']; 
    $password 	= encryptIt($data['password']);
	$deviceid 	= $data['deviceid'];

    $validator = new Validator;
    $validation = $validator->validate($data, [
        'username'	=> 'required|alpha_num',
        'password'	=> 'required|min:3',
    ]);

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
        http_response_code(400);
        $response['status']	= 'required';
        $response['message']= $errors->firstOfAll();
    } else {
        /// validate login Credentials
        $sql = db_query("select * from mng_user where username ='" . $username . "' && password='" . $password . "' && status='1'");
        /// if credentials valid
        if (mysqli_num_rows($sql) > 0) {
            /// return success
            $res = mysqli_fetch_object($sql);
            $site_url = SITEURL;
			$sql2 = db_query("select * from mng_usertype where id=".$res->user_type);

			$res2 = mysqli_fetch_object($sql2);
            $user = [
                'userid' => $res->id,
                'name' => $res->fullname,
                'email' => $res->email,
                'phone' => $res->phone,
                'username' => $res->username,
                'user_type_id' => $res->user_type,
                'user_type' => $res2->usertype,
                'district_id' => $res->district_id,
            ];
            db_query("UPDATE mng_user set deviceid='".$deviceid."' WHERE id=".$res->id);
			
            //JWT Authentication
            $secret_key = "secret";
            $issuer_claim = "localhost"; // this can be the servername
            $audience_claim = "THE_AUDIENCE";
            $issuedat_claim = time(); // issued at
            $notbefore_claim = $issuedat_claim; //not before in seconds
            $expire_claim = $issuedat_claim + 31536000; // expire time in seconds
            $headers = array(
                "alg"=> "HS256",
                "typ"=> "JWT"
            );
            $payload = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "nbf" => $notbefore_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "userid" => $res->id,
                    "name" => $res->fullname,
                    "email" => $res->email,
                    "username" => $res->username,
                    "user_type" => $res2->usertype,
                )
            );

            $jwt = generate_jwt($headers, $payload);
			

            $encode_jwt = $jwt;
            $decode_jwt = JWT::decode($jwt, new Key($secret_key, 'HS256'));

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'Login Successful';
            //$response['user'] = $user;
            $response['jwt_encode'] = $encode_jwt;
            //$response['jwt_decode'] = $decode_jwt;
            $response['jwt_status'] = is_jwt_valid($encode_jwt);
        } else {
            /// else failure
            http_response_code(401);
           	$response['status'] = 'unauthorized';
            $response['message'] = 'Invalid Username or Password';
        }
    }
	return $response;
}
$response = Login($data);
header('Content-type: application/json');
echo json_encode($response);
?>
