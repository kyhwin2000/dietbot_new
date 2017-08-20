<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title> number handling </title>
</head>
<body>

<?php

$numbers = array();

include("./test.php");
$json = file_get_contents("php://input");
$decode = json_decode($json, true);
$data = explode(' ', $decode);	// 공백 기준으로 단어 분리하기
$d_length = count($data);

// 한글로 된 단위(개,그릇,잔 등) DB에서 검색하기
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);

// 한 단어씩 숫자명 매칭 여부 확인
for($j=0;$j<$d_length;$j++){
	$num = $data[$j];
	$qry = "SELECT * from numbers where n_Text = '$num'";
	$res = mysqli_query($db, $qry);
	// 단위 테이블에 있는 문자열이 있으면 단위로 저장하기
	while ($rw = mysqli_fetch_assoc($res)) {
        if($rw["n_Text"]!==false){
        	//echo $rw["n_Number"];
        	//echo "<BR>";
 	       	array_push($f_number,$rw["n_Number"]);
		}
    }	
}
// return $numbers;

?>