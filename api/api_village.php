<?php
require __DIR__.'/header.php';
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
//error_reporting(1);
$data = json_decode(file_get_contents('php://input'), TRUE);
//print_r($data);
$response = [];
function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function district($data){
	 $distid = $blockid = "";
    if(isset($data['dist_id'])){
        $distid = $data['dist_id'];
    }

    if(isset($data['block_id'])){
        $blockid = $data['block_id'];
    }

   

   if(isset($distid) && $distid != ''){
        $result = db_query("SELECT * FROM mng_district where status='1' AND id='$distid' order by district_name");
    } else {
        $result = db_query("SELECT * FROM mng_district where status='1' order by district_name");
    }                      
    $rows=[];

    if(mysqli_num_rows($result) > 0){
        while ($data = $result->fetch_assoc()) {
            $row =[];
            $row['id'] 		= $data['id'];
            $row['name']	= clean($data['district_name']);
			$row['block']	= [];
            $dist_id 		= $data['id'];
    
    //If block id is passed...........        
    if(isset($blockid) && $blockid != ''){
    $blocks = db_query("SELECT * FROM mng_blocks where dist_id='$dist_id' AND id='$blockid' order by block_name");
    } else{
    $blocks = db_query("SELECT * FROM mng_blocks where dist_id='$dist_id' order by block_name");
    }

	while ($datab = $blocks->fetch_assoc()){
		$rowb['id'] 		= $datab['id'];
		$rowb['block_name']	= clean(ucwords(strtolower($datab['block_name'])));
		$rowb['village']			= [];
		$block_id 			= $datab['id'];
	
	/* IF GP ID Passed */
	if(isset($blockid) && $blockid != ''){
	}
    /* Add Village Data */
	$vsql =  db_query("SELECT * FROM mng_village where block_id='$block_id' order by village_name"); 
	if(mysqli_num_rows($vsql) > 0){
		while ($datac2 =  $vsql->fetch_assoc()) {
			$rowc2['id']  = $datac2['id'];
			$rowc2['village_name']= clean(ucwords(strtolower($datac2['village_name'])));
			$rowc2['village_code']= clean(ucwords(strtolower($datac2['village_code'])));	
			array_push($rowb['village'], $rowc2);
			}
		}
		
	array_push($row['block'], $rowb);
	}
    

    array_push($rows, $row);    
	
    }
        $response['status'] = "success";
        $response['message'] = 'Village Results Found';
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