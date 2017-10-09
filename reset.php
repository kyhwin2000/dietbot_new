<?php
//DB 연결
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';
$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
$qry = "update users set eat_calorie = 0,eat_carbo=0,eat_protein=0,eat_fat=0, remain_calorie = recommended_calorie";
mysqli_query($db,$qry);
mysqli_close($db);
?>