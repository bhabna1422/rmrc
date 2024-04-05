<?php
require __DIR__.'/header.php';
header("Access-Control-Allow-Methods: POST");
// error_reporting(1);
// $data = json_decode(file_get_contents('php://input'), TRUE);

$data = $_POST;
$response = [];

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Rakit\Validation\Validator;

function User_patient_info($data){
    $sample_id 	= $data['sample_id'];

    $validator = new Validator();
    $validation = $validator->validate($data, [
        'sample_id' => 'required',
    ]);

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
        http_response_code(400);
        $response['status'] = 'required';
        $response['message'] = $errors->firstOfAll();
    } else {
        $rows=[];
        $result = db_query("SELECT  `interview_date`,`visit_number`, `district_id`, `block_id`, `village_id`,`contact_number`, `sample_id`, `survey_type` FROM answer_individual_schedule  WHERE sample_id = '$sample_id'
        UNION ALL
        (SELECT `interview_date`,`visit_number`, `district_id`, `block_id`, `village_id`,`contact_number`,`sample_id`, `survey_type` FROM answer_wash_practices WHERE sample_id = '$sample_id')
		 ");  
       
        if(mysqli_num_rows($result) > 0){
            while ($data = mysqli_fetch_array($result)) {

                $villageid = $data['village_id'];
                $result_village = db_query("SELECT * FROM `mng_village` where id='$villageid'");
                $village_data = mysqli_fetch_array($result_village);

                $blockid = $data['block_id'];
                $result_block = db_query("SELECT * FROM `mng_blocks` where id='$blockid'");
                $block_data = mysqli_fetch_array($result_block);

                $visitno = $data['visit_number'];
                $result_visit = db_query("SELECT * FROM `mng_visit_number` where id='$visitno'");
                $visit_data = mysqli_fetch_array($result_visit);

                $row =[];
                
                $row['sample_id']	    	= $data['sample_id'];
                $row['survey_type'] 	    = $data['survey_type'];
                $row['village_id'] 	        = $data['village_id'];
                $row['village_name'] 	    = $village_data['village_name'];
                $row['block_id'] 	        = $data['block_id'];
                $row['block_name'] 	        = $block_data['block_name'];
                $row['interview_date'] 	    = $data['interview_date'];
                $row['contact_number'] 		= $data['contact_number'];
                $row['visit_number'] 		= $data['visit_number'];
                $row['visit_name'] 		    = $visit_data['visit_name'];
              

                array_push($rows, $row);
            }
            
            $response['status'] = "success";
            $response['message'] = "Answer Info Found";
            $response['ans_info'] = $rows;
        }
        else{
            $response['status'] = "success";
            $response['message'] = 'No Results Found';
            $response['ans_info'] = $rows;
        }
    }
    return $response;
}

$response = User_patient_info($data);			
header('Content-type: application/json');
echo json_encode($response);
?>