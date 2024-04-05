<?php
define('ADMINEMAIL', 'debasismaxim@gmail.com');
date_default_timezone_set("Asia/Calcutta");

// ---------- Cookie Info ---------- //
$cookie_name = 'NHPOdisha';
$cookie_time = (3600 * 24 * 30); // 30 days

//------------- AUTO LOGIN ----------------//

if(isSet($cookie_name)){
// Check if the cookie exists
	if(isSet($_COOKIE[$cookie_name])){
		parse_str($_COOKIE[$cookie_name]);
		$_SESSION['email']		= $email;
		$_SESSION['uname'] 		= $uname;
		$_SESSION['user_id']	= $user_id;
		//header("location:$fullurl");
		//exit;
	}
}


function siteURL(){
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	
	$domainName = $_SERVER['HTTP_HOST'].'/nhp_groundwater/';
	return $protocol.$domainName;
}

define('SITEURL', siteURL());

function filter_inputs($data){
	$filter = trim(strip_tags($data));
	return $filter;
}

function filter_number($data){
	$filter = preg_replace('/[^0-9]/', '', $data);
	return $filter;
}

function secure_inputs($data){
	$connection = db_connect();
	return mysqli_real_escape_string($connection, $data);
	mysqli_close($connection);
}

/* INSERT DATA */
function insert_data($table, $data){
	$connection = db_connect();
	$fld = $val = '';
	$result = [];
	foreach($data as $f=>$v){			
		$fld .= $f.',';
		$value = secure_inputs($v);
		$val .= "'".$value."',";
	}
	$fld = rtrim($fld, ",");
	$val = rtrim($val, ",");
	$qry = "insert into $table ($fld) values($val)";
	
	$lastid = db_query_last_id($qry);

	if($lastid){
		$result['status'] =1;
		$result['insertid'] = $lastid;
		$result['message'] = "Record added successfully!";
	} else {
		$result['status'] =0;
		$result['insertid'] = 0;
		$result['message'] = "ERROR!! ".db_error();
	}	

	return $result;
}

/* INSERT MULTIPLE */

function insert_data_batch($tbl,$data){
	$connection = db_connect();
	$sql = 'INSERT INTO '.$tbl.' ';
	$kks =''; $vvs ='';

	$data_o = $data[0];

	foreach($data_o as $k=>$v){
		$kks .=$k.', ';
	}
	$kk_f = rtrim($kks,', ');
	$sql .= '('.$kk_f.')';
	$sql .= 'VALUES '; 


	$vsql='';
	foreach($data as $datam){
		$vvs ='';
		foreach($datam as $kk=>$vv){
			$vvs .='"'.secure_inputs($vv).'", ';
		}

		$vv_f = rtrim($vvs,', ');
		$vsql .= '('.$vv_f.'), '; 
	}

	$vsql_f = rtrim($vsql,', ');
	
	$sql .= $vsql_f; 

	$lastid 	= db_query_last_id($sql);
	$affectrow  = mysqli_affected_rows($connection);
	
	if ($lastid) {
		$result['status'] =1;
		$result['affectrow'] = $affectrow;
		$result['insertid'] = $lastid+$affectrow-1;
		$result['message'] = "Record added successfully!";
	} else {
		$result['status'] =0;
		$result['affectrow'] = 0;
		$result['insertid'] = 0;
		$result['message'] = "ERROR!! ".db_error();
	}	
	//db_close();
	return $result;
}

/* UPDATE DATA */
function update_data($table, $data, $criteria){ 
	//example update_data($table, array('name'=>'abc'), array('id'=>1))
	$connection = db_connect();
	$sval = '';
	$qry = '';
	$result = [];
	foreach($data as $k=>$v){
		$value = secure_inputs($v);
		$qry .= "`$k`='$value',";
	}
	$qry=rtrim($qry,",");
	$whr = '';
	foreach($criteria as $f=>$v){ 
		$v = secure_inputs($v);
		$whr .= "`$f`='$v' AND "; 
	} 
	$whr = rtrim($whr," AND ");
	$query = "update $table set $qry where $whr ";

	if(db_query($query)===TRUE){
		$affectrow = mysqli_affected_rows($connection);
		$result['status'] =1;
		$result['affectrow'] = $affectrow;
		if($affectrow >0){
			$result['message'] = "Record updated successfully!";
		} else {
			$result['message'] = "Nothing to Update!";
		}
	} else {
		$result['status'] =0;
		$result['affectrow'] = 0;
		$result['message'] = "ERROR!! ".db_error();
	}

	return $result;
}

/* DELETE DATA */
function delete_data($table, $id){
	//delete_data($table, array('id'=>1,'name'=>'abc');	
	$connection = db_connect();
	$result = [];
	if(is_array($id)){
		$qry = "delete from $table where ";
		foreach($id as $k=>$v){
			$v = secure_inputs($v);
			$qry.=" `$k`='$v' and";
		}
		$qry = rtrim($qry, "and");
		
	} else {
		$id = filter_number($id);
		$qry = 'delete from '.$table.' where id='.$id;
	}
	
	if(db_query($qry)===TRUE){
		$affectrow = mysqli_affected_rows($connection);
		$result['status'] =1;
		$result['affectrow'] = $affectrow;
		if($affectrow >0){
			$result['message'] = "Record deleted successfully!";
		} else {
			$result['message'] = "Nothing found to delete!";
		}
	} else {
		$result['status'] =0;
		$result['affectrow'] = 0;
		$result['message'] = "ERROR!! ".db_error();
	}

	return $result;
}

/* NUMBER OF ROWS */
function number_rows($table, $field=null, $value=null){ 
	//number_rows($table, array(field), array($value)) or 
	//number_rows($table, array(field1, field2), array(value1, value2)
	$connection = db_connect();
	if(is_array($field) && is_array($value)){
		$q = "select * from $table where ";
		$i=0;
		foreach($field as $f){
			$v = $value[$i++];
			$q.=" $f='".$v."' and";
		}
		$q = rtrim($q, 'and');
		
	} else {
		if($field==null && $value==null)
			$q = "select * from $table";
		else
			$q = "select * from ".$table." where ".$field."='".$value."'";
	}

	$query	= db_query($q);
	$result = mysqli_num_rows($query);
	if($result) {
		return $result;
	} else {
		return 'invalid';
	}
}

/* GET DATA */
function get_data($table, $id=null, $distinct=null, $order=null){
	$connection = db_connect();
	$id = filter_number($id);
	$row= array();
	if($id != null)
		$q = 'select * from '.$table.' where id='.$id;
	else if($distinct != null)
		$q = "select DISTINCT $distinct from $table";
	else if($order !=null)
		$q = 'select * from '.$table.' order by id '.$order;
	else	
		$q = 'select * from '.$table.' order by id desc';
	$qry = db_query($q);
	
	for ($res = array(); $tmp = $qry->fetch_array(MYSQLI_ASSOC);) $res[] = (object)$tmp;

	return $res;			
}

/* GET DATA BY */
function query_data($query, $type='obj', $len='M') {
	$connection = db_connect();
	
	$qry = db_query($query);
	if($type=='array'){
		for ($res = array(); $tmp = $qry->fetch_array(MYSQLI_ASSOC);) $res[] = $tmp; 
	} else {
		for ($res = array(); $tmp = $qry->fetch_array(MYSQLI_ASSOC);) $res[] = (object)$tmp;
	}
	
	if(!empty($res)){
		if($len=='S'){
			return $res[0];
		} else {
			return $res;
		}
	} else {
		return array();
	}
}

/* PRINT RESULT IN ARRAY */
function print_result($data){
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

/* Count Array data */
function count_data($data){
	$data = (array) $data;
	return count($data);
}

function word_limiter($str, $limit = 100, $end_char = '&#8230;'){
	if (trim($str) == '') {
		return $str;
	}
	preg_match('/^\s*+(?:\S++\s*+){1,' . (int) $limit . '}/', trim(strip_tags($str)), $matches);
	if (strlen($str) == strlen($matches[0])) {
		$end_char = '';
	}
	return rtrim($matches[0]) . $end_char;
}


function removeSpchar($str){
	$str = str_replace("%", "-", $str);
	$str = str_replace("#", "-", $str);
	$str = str_replace("!", "-", $str);
	$str = str_replace("@", "-", $str);
	$str = str_replace("^", "-", $str);
	$str = str_replace("*", "-", $str);
	$str = preg_replace('/\s\&+/', '-', $str);
	$str = preg_replace("/\s/", "-", $str);
	$str = preg_replace('/\-\-+/', '-', $str);
	$str = str_replace("(", "-", $str);
	$str = str_replace(")", "-", $str);
	$str = str_replace("(", "-", $str);
	$str = str_replace(")", "_", $str);
	$str = str_replace("_", "-", $str);
	$str = str_replace("&", "-", $str);
	$str = str_replace("'", "-", $str);
	$str = str_replace(",", "-", $str);
	// $str = str_replace(".", "-", $str);
	$str = preg_replace('/\-\-+/', '-', $str);
	$str = rtrim($str, '-');
	return $str;
}

function addhttp($url) {
	if($url){
	if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
		$url = "http://" . $url;
	}
	return $url;
}
}

function short_str($str, $len, $cut = true) {
	if (strlen($str) <= $len)
		return $str;
	return ( $cut ? substr($str, 0, $len) : substr($str, 0, strrpos(substr($str, 0, $len), ' ')) ) . '...';
}

function getLnt($zip){
	$url = "http://maps.googleapis.com/maps/api/geocode/json?address=
	".urlencode($zip)."&sensor=false";
	$result_string = file_get_contents($url);
	$result = json_decode($result_string, true);
	$result1[]=$result['results'][0];
	$result2[]=$result1[0]['geometry'];
	$result3[]=$result2[0]['location'];
	return $result3[0];
}

function priceChange($val){
	$price =  number_format($val, 2, '.', ',');
	return $price;
}

function timeAgo($time_ago){
	$cur_time 	= time();
	$time_elapsed 	= $cur_time - $time_ago;
	$seconds 	= $time_elapsed ;
	$minutes 	= round($time_elapsed / 60 );
	$hours 		= round($time_elapsed / 3600);
	$days 		= round($time_elapsed / 86400 );
	$weeks 		= round($time_elapsed / 604800);
	$months 	= round($time_elapsed / 2600640 );
	$years 		= round($time_elapsed / 31207680 );
	// Seconds
	if($seconds <= 60){
		$tm= "$seconds seconds ago";
	}
	//Minutes
	else if($minutes <=60){
		if($minutes==1){
			$tm= "one minute ago";
		} else {
			$tm= "$minutes minutes ago";
		}
	}
	//Hours
	else if($hours <=24){
		if($hours==1){
			$tm=  "an hour ago";
		} else {
			$tm= "$hours hours ago";
		}
	}
	//Days
	else if($days <= 7){
		if($days==1){
			$tm= "yesterday";
		} else {
			$tm= "$days days ago";
		}
	}
	//Weeks
	else if($weeks <= 4.3){
		if($weeks==1){
			$tm= "a week ago";
		} else {
			$tm= "$weeks weeks ago";
		}
	}
	//Months
	else if($months <=12){
		if($months==1){
			$tm= "a month ago";
		} else {
			$tm= "$months months ago";
		}
	}
	//Years
	else{
		if($years==1){
			$tm= "one year ago";
		} else {
			$tm= "$years years ago";
		}
	}

	return $tm;
}

function dateDiff($date1, $date2) {
	$date1_ts = strtotime($date1);
	$date2_ts = strtotime($date2);
	$diff = $date2_ts - $date1_ts;

	$d = round($diff / 86400);
	if($d>1)
	return $d.' days ';
	else 
	return $d.' day ';
}

function formatSizeUnits($bytes){
	if ($bytes >= 1073741824) {
		$bytes = number_format($bytes / 1073741824, 0) . ' GB';
	} elseif ($bytes >= 1048576){
		$bytes = number_format($bytes / 1048576, 0) . ' MB';
	} elseif ($bytes >= 1024){
		$bytes = number_format($bytes / 1024, 0) . ' KB';
	} elseif ($bytes > 1){
		$bytes = $bytes . ' bytes';
	} elseif ($bytes == 1) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}

	return $bytes;
}

function pageurl(){
	$sql2 = db_query("select pageurl from mng_aboutus"); 
	$row2 = mysqli_fetch_array($sql2);
	$aboutus  = $row2['pageurl'];
	
	$object = new stdClass();
	$object->aboutus 			= $aboutus;
	
	return $object;
}

function myfilsSize($file, $type){
	$filesize = filesize($file);
	$fs = formatSizeUnits($filesize);
	return $fs;
}


if (!isset($_SESSION['token'])){
	$token = md5(uniqid(rand(), TRUE));
	$_SESSION['token'] = $token;
	$_SESSION['token_time'] = time();
} else {
	$token = $_SESSION['token'];
}

function encryptIt($q){
	$secret_key = 'Life is a long lesson';
	$secret_iv = 'Keep_smiling';
	$output = false;
	$encrypt_method = "AES-256-CBC";
	$key = hash( 'sha256', $secret_key );
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	$qEncoded = base64_encode(openssl_encrypt($q, $encrypt_method, $key, 0, $iv));
	return($qEncoded);
}

function decryptIt($q){
	$secret_key = 'Life is a long lesson';
	$secret_iv = 'Keep_smiling';
	$output = false;
	$encrypt_method = "AES-256-CBC";
	$key = hash( 'sha256', $secret_key );
	$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	$qDecoded = openssl_decrypt(base64_decode($q),$encrypt_method, $key, 0, $iv);
	return($qDecoded);
}


function url_get_contents($Url) {
	if (!function_exists('curl_init')){ 
		die('CURL is not installed!');
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $Url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function changeURL($str) {
	$str = str_replace("%", "-", $str);
	$str = str_replace("#", "-", $str);
	$str = str_replace("!", "-", $str);
	$str = str_replace("@", "-", $str);
	$str = str_replace("^", "-", $str);
	$str = str_replace("*", "-", $str);
	$str = preg_replace('/\s\&+/', '-', $str);
	$str = preg_replace("/\s/", "-", $str);
	$str = preg_replace('/\-\-+/', '-', $str);
	$str = str_replace("(", "-", $str);
	$str = str_replace(")", "-", $str);
	$str = str_replace("(", "-", $str);
	$str = str_replace(")", "_", $str);
	$str = str_replace("_", "-", $str);
	$str = str_replace("&", "-", $str);
	$str = str_replace("'", "-", $str);
	$str = str_replace(":", "-", $str);
	$str = preg_replace('/\-\-+/', '-', $str);
	$str = rtrim(strtolower($str), '');
	return $str;
}

/* Show Hide Sections */
function showhide(){
	$sql = db_query("select * from showhide");
	$row = mysqli_fetch_array($sql);
	$cashoutcheque = $row['cashoutcheque'];

	$object = new stdClass();
	$object->cashoutcheque = $row['cashoutcheque'];

	return $object;
	
}

function array_search_multidim($m_array, $key, $val){
	$out = array();
	$i=0;
	$m_array= (object) $m_array;

	

	foreach($m_array as $a){

		/*print_result($a); 
		echo $a->$key;
		echo '------';*/
		
		if(isset($a->$key)){
			if($a->$key==$val){
				$out[$i]=$a;
				$i++;
			}
		}else{
			if($a[$key]==$val){
				$out[$i]=$a;
				$i++;
			}
		}
		

	}
	return $out;
}

/* Admin Module Check 
function adminAccess($adminid, $admintype, $module){
	$access = 1;
	if($admintype=='1'){
		$access = 1;
	}
	else{
		$sql = db_query("select * from `user_to_modules` where user_id='".$adminid."' and module_id ='".$module."'");
		//print "select * from `user_to_modules` where user_id='".$adminid."' and module_id ='".$module."' and view='1'";
		$numrow = mysqli_num_rows($sql);
		if($numrow >0){
			$access = 1;	
		}
	}
	return $access;
}


$Manage_Block 			= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '1');
$Manage_Cluster 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '2');
$Manage_Age_Group 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '3');
$Manage_Sections 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '4');
$Manage_Location 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '5');
$Manage_User_Type 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '6');
$Manage_User 			= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '7');
$Manage_Tribe 			= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '8');
$Upload_EM360_Result 	= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '9');
$View_EM360_Result 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '10');
$Upload_E411_Result 	= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '11');
$View_E411_Result 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '12');
$View_Clusters 			= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '13');
$View_Households 		= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '14');
$Manage_Team 			= adminAccess($_SESSION['user_id'], $_SESSION['user_type'], '15');
*/
	$secretKey 	 = "6Lew8kEdAAAAAJEVGw-FnzDuPZuSb6F0ZGdNlq_M";
	$sitekey	 = "6Lew8kEdAAAAAE9n0C8SzNgtbf12VPzcEhdV37uM";

//$secretKey 	 = "6Lckb6gkAAAAAKdbUn_QlDC2DDXNS0vGmtzbTJp8"; 
//$sitekey	 = "6Lckb6gkAAAAABeQjSZTbSyDoAr2Sppj28flgct8";// 

?>