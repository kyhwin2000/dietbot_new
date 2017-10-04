<?php
//친구 추가 시 기초적인 회원 정보 수집
header("Content-Type: application/json; charset=UTF-8");
$data = json_decode(file_get_contents('php://input'), true);
$user_key = $data["user_key"];

//새로운 신규 회원 유저 키를 DB에 등록하고 new 필드 값을 y로 저장
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';
$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
$usr_query = "insert into users (user_key,new) values ('$user_key','y')";	
mysqli_query($db,$usr_query);
mysqli_close($db);
?>
