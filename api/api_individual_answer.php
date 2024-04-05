<?php
require __DIR__ . '/header.php';
header("Access-Control-Allow-Methods: POST");
mysqli_set_charset($connect,'utf8');
// error_reporting(1);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

$data = $_POST;
$response = [];
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Rakit\Validation\Validator;
// print_r($data);
function saveindivisualdata($data)
{
    //print_r($data);
	
    $interview_date		= $data['interview_date'];
    $visit_number		= $data['visit_number'];
    $district_id		= $data['district_id'];
    $district_name		= $data['district_name'];
    $block_id 			= $data['block_id'];
    $village_id 		= $data['village_id'];
    $household_id 		= $data['household_id'];
    $individual_id 		= $data['individual_id'];
    $longitude 			= $data['longitude'];
    $latitude 			= $data['latitude'];
    $altitude 			= $data['altitude'];
    $accuracy	    	= $data['accuracy'];
    $contact_number		= $data['contact_number'];
    $sample_id		    = $data['sample_id'];
    $question_type		= $data['question_type'];
    $answer_data 		= str_replace("'", "&#39;", $data['answer_data']);
    $starttime 			= $data['starttime'];
    $endtime 			= $data['endtime'];
    $created_on 		= date("Y-m-d H:i:s");

    $validator 			= new Validator();

    $validation = $validator->validate($data, [
		'village_id' 	=> 'required',
        'household_id' => 'required',
        'individual_id' => 'required',
    ]);

    if ($validation->fails()) {
        // handling errors
        $errors = $validation->errors();
        http_response_code(400);
        $response['status'] = 'required';
        $response['message'] = $errors->firstOfAll();
    } else {
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
            /* Select Household Details */
            // Household Json data store in file
            $individualjson_file = "individualjson"."_".date("Y_m_d_H_i_s");
            file_put_contents('json/individualdata/'.$individualjson_file.".json", json_encode($answer_data));
            file_put_contents('json/individualdata/'.$individualjson_file.'.txt', json_encode($data));

            $individual_data_filenm = $individualjson_file.".txt";
			
			/* Upload Files */
			
			/* Upload respondent Image */
			$rn= rand();
			$uploadpath = "../upload_images/";
			$respondent_image		= "";
			$livestock_animal = "";
			$livestock_bird = "";
			$livestock_pet_animal = "";
			$cattle_shed = "";
			
			if($_FILES['respondent_image']['name']){
				$respondent_image = $rn."_".$_FILES['respondent_image']['name'];
				move_uploaded_file($_FILES['respondent_image']['tmp_name'],$uploadpath.$respondent_image);
			}

			if($_FILES['livestock_animal']['name']){
				$livestock_animal = $rn."_".$_FILES['livestock_animal']['name'];
				move_uploaded_file($_FILES['livestock_animal']['tmp_name'],$uploadpath.$livestock_animal);
			}

			if($_FILES['livestock_bird']['name']){
				$livestock_bird = $rn."_".$_FILES['livestock_bird']['name'];
				move_uploaded_file($_FILES['livestock_bird']['tmp_name'],$uploadpath.$livestock_bird);
			}

			if($_FILES['livestock_pet_animal']['name']){
				$livestock_pet_animal = $rn."_".$_FILES['livestock_pet_animal']['name'];
				move_uploaded_file($_FILES['livestock_pet_animal']['tmp_name'],$uploadpath.$livestock_pet_animal);
			}

			if($_FILES['cattle_shed']['name']){
				$cattle_shed = $rn."_".$_FILES['cattle_shed']['name'];
				move_uploaded_file($_FILES['cattle_shed']['tmp_name'],$uploadpath.$cattle_shed);
			}
			
			
			/* UPLOAD Livestock Animal IMAGE ( question = 49) */
			
			// $cntanimalfiles = count($_FILES['livestock_animal']['name']);
			// //echo "Files=".$cntanimalfiles;
			
			// $livestock_animal = "";
			// for($i=0;$i<$cntanimalfiles;$i++){
			// 	$s= rand();
			// 	$filename = $s."_".$_FILES['livestock_animal']['name'][$i];
			// 	move_uploaded_file($_FILES['livestock_animal']['tmp_name'][$i],$uploadpath.$filename);
			// 	$livestock_animal.= $filename.",";
			// }

			/* UPLOAD Livestock Bird IMAGE ( question = 50)*/
			
			// $cntbirdfiles = count($_FILES['livestock_bird']['name']);
			// //echo "Files=".$cntbirdfiles;
				
			// $livestock_bird = "";
			// for($i=0;$i<$cntbirdfiles;$i++){
			// 	$s= rand();
			// 	$filename = $s."_".$_FILES['livestock_bird']['name'][$i];
			// 	move_uploaded_file($_FILES['livestock_bird']['tmp_name'][$i],$uploadpath.$filename);
			// 	$livestock_bird.= $filename.",";
			// }

			/* UPLOAD Livestock pet Animal IMAGE ( question = 51) */
			
			// $cntpetfiles = count($_FILES['livestock_pet_animal']['name']);
			// //echo "Files=".$cntpetfiles;
					
			// $livestock_pet_animal = "";
			// for($i=0;$i<$cntpetfiles;$i++){
			// 		$s= rand();
			// 		$filename = $s."_".$_FILES['livestock_pet_animal']['name'][$i];
			// 		move_uploaded_file($_FILES['livestock_pet_animal']['tmp_name'][$i],$uploadpath.$filename);
			// 		$livestock_pet_animal.= $filename.",";
			// }
				
			/* UPLOAD cattle shed IMAGE ( question = 59)*/
			
			// $cntcattlefiles = count($_FILES['cattle_shed']['name']);
			// //echo "Files=".$cntcattlefiles;
					
			// $cattle_shed = "";
			// for($i=0;$i<$cntcattlefiles;$i++){
			// 		$s= rand();
			// 		$filename = $s."_".$_FILES['cattle_shed']['name'][$i];
			// 		move_uploaded_file($_FILES['cattle_shed']['tmp_name'][$i],$uploadpath.$filename);
			// 		$cattle_shed.= $filename.",";
			// }
			

            /* Generate sample ID */
            $sample_id = $village_id.$household_id.$individual_id;
			$survey_type = "individual_schedule";
			// echo $sample_id;

			/* Insert data in survey json table */		
            $csql = db_query(
                "INSERT INTO `survey_json` set
				interview_date	= '".$interview_date."', 
				visit_number	= '".$visit_number."', 
				district_id		= '".$district_id."', 
				block_id 		= '".$block_id."', 
				village_id		= '".$village_id."', 
				household_id 	= '" .$household_id."', 
				individual_id	= '".$individual_id."', 
				sample_id		= '".$sample_id."', 
				respondent_image = '".$respondent_image."', 
				contact_number	= '".$contact_number."', 
				starttime		= '".$starttime."', 
				survey_type 	= '".$survey_type ."',
				endtime			= '".$endtime."', 
				livestock_animal = '".$livestock_animal."', 
				livestock_bird = '".$livestock_bird."',
				livestock_pet_animal = '".$livestock_pet_animal."',
				cattle_shed = '".$cattle_shed."',
				created_by		= '".$userid."', 
				created_on		= '".$created_on."', 
				json_file_path	= '".$individual_data_filenm."'"
			
            );

            /* Save Data in survey_json table */

            $individualData = json_decode($answer_data, true);
            $answers = [];

            /* Add DATA IN survey_answer_raw */
                if($individualData){
                    foreach ($individualData as $sec) {
                        $sid = $sec['sid'];
                        $secheading = $sec['secheading'];
                        $questions = $sec['questions'];
                        foreach ($questions as $ans) {
                            $question_id 	= $ans['id'];
                            $question 		= $ans['label'];
                            $answer_id 		= $ans['value'];
                            $question_type 	= $ans['qType'];
                            $answer 		= $ans['name'];
                            $allValues 		= $ans['allValues'];
                            $issubquestion 	= $ans['isSubQuestions'];
                            $isOtherQuestions = $ans['isOtherQuestions'];
                            if ($question_type == '5') {
                                $issubquestion = "1";
                            }

							$interview_date		= $data['interview_date'];
							$visit_number		= $data['visit_number'];
							$district_id		= $data['district_id'];
							
							$block_id 			= $data['block_id'];
							$village_id 		= $data['village_id'];
							$household_id 		= $data['household_id'];
							$individual_id 		= $data['individual_id'];
							$longitude 			= $data['longitude'];
							$latitude 			= $data['latitude'];
							$altitude 			= $data['altitude'];
							$accuracy	    	= $data['accuracy'];
							$contact_number		= $data['contact_number'];
							$sample_id		    = $data['sample_id'];
							$question_type		= $data['question_type'];
							$answer_data 		= str_replace("'", "&#39;", $data['answer_data']);
							$starttime 			= $data['starttime'];
							$endtime 			= $data['endtime'];

                            $answers[] = [
                                "interview_date" 		=> $interview_date,
                                "visit_number" 			=> $visit_number, 
                                "district_id" 			=> $district_id, 
                                "block_id" 				=> $block_id, 
                                "village_id" 			=> $village_id, 
                                "household_id" 			=> $household_id, 
                                "individual_id" 		=> $individual_id,
                                "sample_id" 			=> $sample_id ,
								"longitude" 			=> $longitude, 
                                "latitude" 				=> $latitude, 
                                "altitude" 				=> $altitude, 
                                "accuracy" 				=> $accuracy,
                                "respondent_image" 		=> $respondent_image ,
								"contact_number" 		=> $contact_number ,
								"survey_type" 			=> $survey_type ,
								"created_by" 			=> $userid,
                                "created_on" 			=> $created_on,
								"starttime" 			=> $starttime,
                                "endtime" 				=> $endtime,
								"livestock_animal" 		=> $livestock_animal, 
								"livestock_bird" 		=> $livestock_bird,
								"livestock_pet_animal" 	=> $livestock_pet_animal,
								"cattle_shed" 			=> $cattle_shed,
                                "question_id" 			=> $question_id,
                                "question" 				=> $question,
                                "answer_id" 			=> $answer_id,
                                "answer" 				=> $answer,
                                "issubquestion" 		=> $issubquestion,
                                "subquestion_id" 		=> "",
                                "subquestion" 			=> "",
                                "subanswer" 			=> "",
                                "otheranswer" 			=> "",
                                "question_type" 		=> $question_type,
                               
                            ];

                            if ($question_type == '5') {
                                $qtype_5_subques = explode(",", $ans['allValues']);
                                $qtype_5_ans = explode(",", $ans['name']);
                                $subq_id = '1';

                                foreach ($qtype_5_subques as $subques) {
                                    $found = array_search($subques, $qtype_5_ans);
                                    if ($found !== false) {
                                        $subans = "1";
                                    } else {
                                        $subans = "2";
                                    }
                                    $answers[] = [
                                        "interview_date" 		=> $interview_date,
										"visit_number" 			=> $visit_number, 
										"district_id" 			=> $district_id, 
										"block_id" 				=> $block_id, 
										"village_id" 			=> $village_id, 
										"household_id" 			=> $household_id, 
										"individual_id" 		=> $individual_id,
										"sample_id" 			=> $sample_id ,
										"longitude" 			=> $longitude, 
										"latitude" 				=> $latitude, 
										"altitude" 				=> $altitude, 
										"accuracy" 				=> $accuracy,
										"respondent_image" 		=> $respondent_image ,
										"contact_number" 		=> $contact_number ,
										"survey_type" 			=> $survey_type ,
										"created_by" 			=> $userid,
										"created_on" 			=> $created_on,
										"starttime" 			=> $starttime,
										"endtime" 				=> $endtime,
										"livestock_animal" 		=> $livestock_animal,
										"livestock_bird" 		=> $livestock_bird,
										"livestock_pet_animal" 	=> $livestock_pet_animal,
										"cattle_shed" 			=> $cattle_shed,
										"question_id" 			=> $question_id,
										"question" 				=> $question,
                                        "answer_id" 			=> "",
                                        "answer" 				=> "",
                                        "issubquestion" 		=> $issubquestion,
                                        "subquestion_id" 		=> $question_id . "_" . $subq_id,
                                        "subquestion" 			=> $subques,
                                        "subanswer" 			=> $subans,
                                        "otheranswer" 			=> "",
                                        "question_type" 		=> $question_type,
                                       
                                    ];
                                    $subq_id++;
                                }
                            }

                            if ($issubquestion == 'true') {
                                foreach ($ans['subQuestions'] as $subans) {
                                    $answers[] = [
										"interview_date" 		=> $interview_date,
										"visit_number" 			=> $visit_number, 
										"district_id" 			=> $district_id, 
										"block_id" 				=> $block_id, 
										"village_id" 			=> $village_id, 
										"household_id" 			=> $household_id, 
										"individual_id" 		=> $individual_id,
										"sample_id" 			=> $sample_id ,
										"longitude" 			=> $longitude, 
										"latitude" 				=> $latitude, 
										"altitude" 				=> $altitude, 
										"accuracy" 				=> $accuracy,
										"respondent_image" 		=> $respondent_image ,
										"contact_number" 		=> $contact_number ,
										"survey_type" 			=> $survey_type ,
										"created_by" 			=> $userid,
										"created_on" 			=> $created_on,
										"starttime" 			=> $starttime,
										"endtime" 				=> $endtime,
										"livestock_animal" 		=> $livestock_animal,
										"livestock_bird" 		=> $livestock_bird,
										"livestock_pet_animal" 	=> $livestock_pet_animal,
										"cattle_shed" 			=> $cattle_shed,
										"question_id" 			=> $question_id,
										"question" 				=> $question,
                                        "answer_id" 			=> $answer_id,
                                        "answer" 				=> $answer,
                                        "issubquestion"			=> $issubquestion,
                                        "subquestion_id" 		=> $subans['id'],
                                        "subquestion" 			=> $subans['label'],
                                        "subanswer" 			=> $subans['value'],
                                        "otheranswer" 			=> "",
                                        "question_type" 		=> $subans['qType'],
                                       
                                    ];
                                }
                            }

                        }
                    }

                    //print_r($answers);
					// echo "hu";
                    $result = insert_data_batch("survey_answer_raw", $answers);
                    $answers = [];
                }

            /*############## NEW CODE STARTED ######################3*/

            $question = $answer = $exclude_array = [];
            $subans = $ans = "";
			
			
//*  */

	foreach ($individualData as $individual) {

	
		$table = "answer_individual_schedule";
		$survey_type 		= "individual_schedule";

		$answer_data1 = $individual['questions'];

			foreach ($answer_data1 as $ans) {
				//$questons = $sec['questons'];
				//print_r($questons);
				//foreach ($questons as $ans) {
					$question_id = "q_".$ans['id'];
					$question_type = $ans['qType'];
					if (trim($ans['name']) == '') {
						$q_answer = "-";
					} else {
						$q_answer = $ans['name'];
						if(strpos($q_answer,'Other')!== false){
							foreach ($ans['subQuestions'] as $subans) {
								$q_answer = "Other - ".$subans['name'];
							}
						}
						
					}
					$issubquestion = $ans['isSubQuestions'];

					if ($question_type == '5') {
						$qtype_5_allans = explode(",", $ans['allValues']);
						$qtype_5_ans = explode(",", $ans['name']);

						$subq_id = '1';

						foreach ($qtype_5_allans as $allans) {
							$found = array_search($allans, $qtype_5_ans);

							if ($found !== false) {
								$q_sub_answer = "1";
							} else {
								$q_sub_answer = "2";
							}

							$sub_question_id = $question_id . "_" . $question_id . "_" . $subq_id;

							if (!in_array($sub_question_id, $exclude_array)) {
								array_push($question, $sub_question_id);
								array_push($answer, $q_sub_answer);
							}
							$subq_id++;
						}
					}

					if ($issubquestion == 'true') {
						foreach ($ans['subQuestions'] as $subans) {
							$sub_question_id = $question_id . "_" . $subans['id'];

							if ($subans['value'] != '') {
								$q_sub_answer = $subans['value'];
							} else {
								$q_sub_answer = "-";
							}
							if (!in_array($sub_question_id, $exclude_array)) {
								array_push($question, $sub_question_id);
								array_push($answer, $q_sub_answer);
							}
						}
					}

					if (!in_array($sub_question_id, $exclude_array)){
						array_push($question, $question_id);
						array_push($answer, $q_answer);
					}
				//}
			}
			
			$values = implode("','", $answer);
			$questions = implode("`,`", $question);


			$values ="
			'" .$interview_date."',
			'" .$visit_number."',
			'" .$district_id."',
			'" .$block_id."',
			'" .$village_id."',
			'" .$household_id."',
			'" .$individual_id."',
			'" .$sample_id."',
			'" .$longitude."',
			'" .$latitude."',
			'" .$altitude."',
			'" .$accuracy."',
			'" .$respondent_image."',
			'" .$contact_number."',
			'" .$survey_type."',
			'" .$question_type."',
			'" .$userid."',
			'" .$created_on."',
			'" .$starttime."',
			'" .$endtime."',
			'" .$livestock_animal."',
			'" .$livestock_bird."',
			'" .$livestock_pet_animal."',
			'" .$cattle_shed."',
			'" .$values."'";

			$questions = 
				"`interview_date`,`visit_number`,`district_id`,  
				`block_id`,  
				`village_id`,  
				`household_id`,  
				`individual_id`,  
				`sample_id`,  
				`longitude`,  
				`latitude`,  
				`altitude`,  
				`accuracy`,  
				`respondent_image`,  
				`contact_number`,  
				`survey_type`,
				`question_type`,  
				`created_by`,  
				`created_on`,  
				`starttime`,  
				`endtime`,
				`livestock_animal`,
				`livestock_bird`,
				`livestock_pet_animal`,
				`cattle_shed`,
				`".$questions."`";

// echo "INSERT INTO $table ($questions) VALUES($values)";
// echo "hi";
				$insertSQL = "INSERT INTO $table ($questions) VALUES($values)";
				// echo $insertSQL;
                db_query($insertSQL);

				file_put_contents('json/individualdata/sql.txt', $insertSQL);
                $question = $answer = [];

                $values = '';
            }

            if(count($individualData) == 0){
                http_response_code(200);
                $response['status'] = "success";
                $response['message'] = 'Answers Add Successfully';
            }
            else{
                if ($result) {
                    http_response_code(200);
                    $response['status'] = "success";
                    $response['message'] = 'Answers Add Successfully';
                    $response['district_id'] = $district_id;
                    // $response['district_name'] = $district_name;
                    $response['survey_type'] = $survey_type;
                    $response['sample_id'] = $sample_id;
                } else {
                    http_response_code(400);
                    $response['status'] = 'failed';
                    $response['message'] = "Error!".$result;
                }
            }
            
        } catch (\Firebase\JWT\ExpiredException $e) {
            http_response_code(400);

            $response['status'] = 'error';

            $response['message'] = $e->getMessage();
        }
    } 
    return $response;
}

$response = saveindivisualdata($data);

//echo "SELECT * FROM countries";

header('Content-type: application/json');

echo json_encode($response);
?>

