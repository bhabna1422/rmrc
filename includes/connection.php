<?php
error_reporting(1);
header('X-Frame-Options: SAMEORIGIN');
header("X-XSS-Protection: 1; mode=block");
header('X-Content-Type-Options: nosniff');


@session_start();
global $connection;
function db_connect() {
	static $connection;
	$username	= "root";//"hydrology_survey"; 
	$password	= "";//"hydrology_survey"; 
	$dbname		= "rmrc_ohpc"; 
	$host		= "localhost";

	if(!isset($connection)) { 
		$connection = mysqli_connect($host,$username,$password,$dbname);
	}
	if($connection === false) {
		return mysqli_connect_error(); 
	}
	return $connection;
}

function db_query($query) {
	$connection = db_connect();
	$result = mysqli_query($connection,$query);
	return $result;
}
function db_query_last_id($query) {
	$connection = db_connect();
	$result = mysqli_query($connection,$query);
	$last_id = mysqli_insert_id($connection);
	return $last_id;
}

function db_error() {
	$connection = db_connect();
	return mysqli_error($connection);
}

function db_close(){
	$connection = db_connect();
	mysqli_close($connection);
}

$connect = db_connect();

set_time_limit(0);

include 'functions.php';
?>