<?php
require __DIR__.'/header.php';
header("Access-Control-Allow-Methods: POST, GET");
//error_reporting(1);
$data = json_decode(file_get_contents('php://input'), TRUE);
$response = [];

function district($data){
    $distid = $data['dist_id'];

    if(isset($distid) && $distid != ''){
        $result = db_query("SELECT * FROM mng_district where status='1' AND id='$distid' order by district_name ");	
    } else {
        $result = db_query("SELECT * FROM mng_district where status='1' order by district_name ");
    }				
    $rows=[];

    if(mysqli_num_rows($result) > 0){
        while ($data = $result->fetch_assoc()) {
            $row =[];
            $row['id'] = $data['id'];
            $row['name']= $data['district_name'];
            array_push($rows, $row);
        }
        $response['status'] = "success";
        $response['message'] = 'District Results Found';
        $response['district_details'] = $rows;
    }
    else{
        $response['status'] = "success";
        $response['message'] = 'No Results Found';
        $response['district_details'] = $rows;
    }
    return $response;
}

$response = district($data);		
header('Content-type: application/json');
echo json_encode($response);
?>