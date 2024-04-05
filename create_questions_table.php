<?php
include("includes/connection.php");
error_reporting(1);

$tableName 	= "questions_individual_schedule";
$path 		= 'api/json/questions_individual_schedule.json';

db_query("DROP TABLE IF EXISTS `$tableName`");

$sql = "
CREATE TABLE `$tableName` (
  `id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `secheading` varchar(200) NOT NULL,
  `question_id` varchar(20) NOT NULL,
  `issubquestion` int(11) NOT NULL,
  `question` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

## CHECK TABLE EXISTS OR NOT ##



	if(db_query($sql)){
		$sql2 = "ALTER TABLE `$tableName` ADD UNIQUE KEY `id` (`id`)";
		db_query($sql2);

		$sql3 = "ALTER TABLE `$tableName` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
		db_query($sql3);

		echo "Table created successfully: ".$tableName;
	}else{
		echo "ERROR: Could not able to execute ".db_error();
	}




/* INSERT QUESTIONS IN THE TABLE */

echo $path;
$question_data = file_get_contents($path);
$ageData = json_decode($question_data, true);
$questions = [];
// echo "<pre>";
// print_r($ageData);
// echo "</pre>";

foreach($ageData as $ages){
    $sid            = $ages['sid'];
    
	$secheading     = $ages['secheading'];
    $questons       = $ages['questions'];
	foreach($questons as $ans){
        $question_id 	= "q_".$ans['id'];
        $question	 	= str_replace("'","&#39;",$ans['label']);
		$issubquestion 	= str_replace("'","&#39;",$ans['isSubQuestions']);
		$question_type 	= str_replace("'","&#39;",$ans['qType']);
		$sq = 0;
// echo "INSERT INTO $tableName set sid ='".$sid."', secheading='".$secheading."', question_id='".$question_id."', question='".$question."'";
		db_query("INSERT INTO $tableName set sid ='".$sid."', secheading='".$secheading."', issubquestion='".$sq."', question_id='".$question_id."', question='".$question."'");

		if ($question_type == '5') {
			$options 	= $ans['options'];
			foreach ($options as $subques) {
				$subquestion_id = $question_id."_".$subques['id'];
				$question	 	= $subques['name'];
				db_query("INSERT INTO $tableName set sid ='".$sid."', secheading='".$secheading."',issubquestion='".$sq."', question_id='".$subquestion_id."', question='".$question."'");
			}
		}

		if ($issubquestion == 'true') {
			$subQuestions 	= $ans['subQuestions'];
			$sq = 1;
			foreach ($subQuestions as $subques) {
				$subquestion_id = $question_id."_".$subques['id'];
				$question	 	= $subques['label'];
				db_query("INSERT INTO $tableName set sid ='".$sid."', secheading='".$secheading."',issubquestion='".$sq."', question_id='".$subquestion_id."', question='".$question."'");
			}
		}
    }
}

?>