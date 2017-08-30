<?php
// 섭취량 숫자와 단위 파싱 부

$json = file_get_contents("php://input");
$decode = json_decode($json, true);
$decode = trim($decode);
// echo $decode;
// echo "<BR>";
$data = explode(' ', $decode);	// 공백 기준으로 단어 분리하기
// print_r($data);
// echo "<BR>";
$d_length = count($data);


// 한글로 된 단위(개,그릇,잔 등) DB에서 검색하기
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);


// 한 단어씩 단위명 매칭 여부 확인

// 단위명 DB 쿼리값 배열로 밀어넣기
for($i=0;$i<$d_length;$i++){
	$var = $data[$i];
	$query = "SELECT u_Name from units where u_Name = '$var'";
	$result = mysqli_query($db, $query);
	$row[] = mysqli_fetch_assoc($result);
}


for($j=0;$j<count($row);$j++){
	$return_unit[] = $row[$j]["u_Name"];	// 배열 안에 배열이 들어있어서 값을 빼냄
}

for($k=0;$k<count($return_unit);$k++){
	if(is_string($return_unit[$k])){
		$r_unit[] = $return_unit[$k];	// 문자열이 있으면 저장
	}	
}

// 한 단어씩 숫자명 매칭 여부 확인
for($l=0;$l<$d_length;$l++){
	$num = $data[$l];
	$qry = "SELECT n_Number from numbers where n_Text = '$num'";
	$res = mysqli_query($db, $qry);
	$rw[] = mysqli_fetch_assoc($res);
}
for($m=0;$m<count($rw);$m++){
	$return_number[] = $rw[$m]["n_Number"];	// 배열 안에 배열이 들어있어서 값을 빼냄
}
for($n=0;$n<count($return_number);$n++){
	if(is_numeric($return_number[$n])){
		$r_number[] = $return_number[$n];	// 문자열이 있으면 저장
	}	
}

$ru['f_unit'] = $r_unit;
$ru['f_number'] = $r_number;

$js_ru = json_encode($ru);
echo $js_ru;

?>