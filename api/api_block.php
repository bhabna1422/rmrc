<?php
require __DIR__.'/header.php';
header("Access-Control-Allow-Methods: POST");
//error_reporting(1);
$data = json_decode(file_get_contents('php://input'), TRUE);
$response = [];

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function district($data){
    if(isset($data['dist_id'])){
    $distid = $data['dist_id'];
    }

    if(isset($data['block_id'])){
    $blockid = $data['block_id'];
    }

    if(isset($distid) && $distid != ''){  
        $result = db_query("SELECT * FROM mng_district where status='1' AND id='$distid' order by district_name");
    } else{
        $result = db_query("SELECT * FROM mng_district where status='1' order by district_name");
    }                     
    $rows=[];

    if(mysqli_num_rows($result) > 0){
        while ($data = $result->fetch_assoc()) {
            $row =[];
            $row['id'] = $data['id'];
            $row['name']= clean(ucwords(strtolower($data['district_name'])));
            $row['block']= [];

            $dist_id = $data['id'];
            
    if(isset($blockid) && $blockid!= ''){
    $blocks = db_query("SELECT * FROM mng_blocks where dist_id='$dist_id' AND id='$blockid' order by block_name");
    } else{
    $blocks = db_query("SELECT * FROM mng_blocks where dist_id='$dist_id' order by block_name");
    }
    //$rowsb=[];
    if(mysqli_num_rows($blocks) > 0) {
        while ($datab = $blocks->fetch_assoc()) {
            //$rowb =[];
            $rowb['id'] = $datab['id'];
            $rowb['name']= clean(ucwords(strtolower($datab['block_name'])));

           array_push($row['block'], $rowb);
        }
      // $response['block_details'] = $rowsb; 
    }   
    array_push($rows, $row);    
        }
        $response['status'] = "success";
        $response['message'] = 'Block Results Found';
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