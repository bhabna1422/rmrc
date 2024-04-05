<?php
include("includes/connection.php");
error_reporting(1);
$tableName = "answer_wash_practices";
$questions_tableName = "questions_wash_practices";
// $patient_id = '2147483647';
$questions = '';

$query = db_query("SELECT question_id FROM `$questions_tableName`");
while($row = mysqli_fetch_array($query)){
    $question = $row['question_id'];
    $questions = $questions.'`'.$question."` text DEFAULT NULL,";
}
//echo $questions;
$questions = rtrim($questions,",");

// db_query("DROP TABLE IF EXISTS `$tableName`");

$sql = "
CREATE TABLE `$tableName` (
  `id` int(11) NOT NULL,
  `interview_date` datetime DEFAULT NULL,
  `visit_number` int(11) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `block_id` int(11) DEFAULT NULL,
  `village_id` int(11) DEFAULT NULL,
  `household_id` varchar(10) DEFAULT NULL,
  `individual_id` varchar(10) DEFAULT NULL,
  `sample_id` varchar(10) DEFAULT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `altitude` varchar(50) DEFAULT NULL,
  `accuracy` varchar(50) DEFAULT NULL,
  `respondent_image` varchar(150) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `question_type` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `endtime` datetime DEFAULT NULL, 
  $questions,
  PRIMARY KEY (id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;";

// $val = db_query("DESCRIBE  `$tableName` ");

// if(!$val){

	if(db_query($sql)){
		$sql2 = "ALTER TABLE `$tableName` ADD UNIQUE KEY `id` (`id`)";
		db_query($sql2);

		$sql3 = "ALTER TABLE `$tableName` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT";
		db_query($sql3);

		echo "Table created successfully: ".$tableName;
	}else{
		echo "ERROR: Could not able to execute ".db_error();
	}

// }else{
// 	echo "Table EXISTS!!".$tableName;
// }

?>