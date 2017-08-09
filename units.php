<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title> quantity and units string handling </title>
</head>
<body>

<?php

$json = file_get_contents("php://input");
$data = json_decode($json, true);
echo $data;

// 한글로 된 단위(개,그릇,잔 등) DB에서 검색하기
	$hostname = 'localhost';
	$username = 'root';
	$password = 'Dntjd13!';
	$dbname = 'dietbot';

	$db = new mysqli($hostname,$username,$password,$dbname);
	if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
	// 한 글자씩 매칭 여부 확인
	mysqli_query($db,"set names utf8");   
	$query = "SELECT 1 from units where u_Name = '$data'";
	$result=mysqli_query($db, $query);
	// 단위가 검색되면 
					


//output the array in the response of the curl request
//print_r($data);
//var_dump($data);

/*
$my_string = $_POST['json_data'];
$decode = json_decode($my_string);
echo($decode);
*/


/*
if ($_POST) {
	$kv = array();
	foreach ($_POST as $key => $value){
		$kv[] = stripslashes($key). "=" . stripslashes($value);
	}
	$query_string = join("&",$kv);
}
*/

?>