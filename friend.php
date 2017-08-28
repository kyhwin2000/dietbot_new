<?php
//친구 추가 시 기초적인 회원 정보 수집
header("Content-Type: application/json; charset=UTF-8");
$data = json_decode(file_get_contents('php://input'), true);

$user_key = $data["user_key"];

//유저 키가 이미 존재하는지 user db에서 검색
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
$query = "SELECT * from users where user_key like '$user_key'";	
$result=mysqli_query($db, $query);

//유저 키가 존재하지 않는다면(신규 회원)
if(count($result)<1){
$usr_query = "INSERT INTO users(user_key) VALUES ('$user_key')";	//먼저 유저 DB에 키값 집어넣기
$new_usr = mysqli_query($db,$usr_query);
echo <<<EOD
	{
	      message: { text: "하루 권장 열량 계산을 위해서 몇 가지만 여쭤볼게요. 성별이 어떻게 되시나요?"},
	      keyboard: {
	        type: "buttons",
	        buttons: 
	        [
	          "남자",
	          "여자"
	        ]
	      }
	    }
	}
EOD;
}




?>
