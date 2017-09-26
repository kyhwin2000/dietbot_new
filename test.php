<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title> basic parsing </title>
</head>
<body>

<?php
// 카카오톡 붙이기 전 웹 페이지 테스트 용 파싱 엔진

$text = $_POST['text'];

$user_key = 'UYcMwWLfIPlw';
$json_data = json_encode($user_key);      

$url = "http://220.230.115.39/chart.php";
  
//남은 텍스트 parsing.php로 보내기
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/x-www-form-urlencoded', 'Content-Type: application/json; charset=UTF-8'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response =curl_exec($ch);
$decode = json_decode($response,1);
// print_r($decode);
//echo json_last_error_msg();
global $f_item, $f_name, $f_number, $f_unit;

$f_item = array();
$f_name = array();
$f_number = array();
$f_unit = array();

//DB 연결
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

$db = new mysqli($hostname,$username,$password,$dbname);
mysqli_query($db,"set names utf8");   
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
$query = "SELECT user_gender,user_age,user_height,user_weight,user_activity from users where user_key like 'UYcMwWLfIPlw'"; 
$result=mysqli_query($db, $query);
$row=mysqli_fetch_array($result);

echo($row['user_gender']);
echo "<BR>";
echo(in_array("0",$row));

// 공백과 쉼표를 제외한 나머지 특수 문자 제거하기
$text = str_replace( "<br/>","",$text); //줄바꿈
$text = str_replace( "." ,"",$text); //마침표
$text = str_replace( "!" ,"",$text); //느낌표
$text = str_replace( "?" ,"",$text); //물음표
$text = str_replace( "$#34" ,"",$text); //큰 따옴표
$text = str_replace( "&#39" ,"",$text); //작은 따옴표
$text = str_replace( "+" ,"",$text); //더하기
$text = str_replace( ":" ,"",$text); //콜론
$text = str_replace( ";" ,"",$text); //세미콜론
$text = str_replace( "^^" ,"",$text); //웃음표시
$text = str_replace( "~" ,"",$text); //물결표시

// &인 경우 쉼표로 바꿔주기
$text = str_replace( "&" ,",",$text);

// 전체 스트링에 숫자가 포함되었는지 검사	
for($f=0;$f<strlen($text);$f++){
	$char = mb_substr($text,$f,1);
	if(is_numeric($char)){
		$case = "numeric";
		break;
	}
	$case = "string";
}


switch ($case) {
// 스트링에 숫자가 있는 경우
  case "numeric"  : 
  		echo "숫자가 있는 케이스!<BR>";
		// 쉼표가 포함되어 있는 경우
		if (strlen(stristr($text,","))>0){
			// 쉽표 기준 잘라서 배열로 저장하기
				$f_item = explode(",",$text);
				for($a=0;$a<(count($f_item));$a++){		
					// 아이템 앞 뒤 공백 제거하기
					$f_item[$a] = trim($f_item[$a]);
					// 문자열 중 숫자만 추출하여 섭취량으로 저장
					$f_number[$a] = preg_replace("/[^0-9]*/s", "", $f_item[$a]);
					// 문자열 중 숫자 나오기 전까지 잘라내서 음식명으로 저장
					$f_name[$a] = substr($f_item[$a],0,strrpos($f_item[$a],$f_number[$a]));
					// 문자열 중 숫자 다음부터 잘라내서 단위로 저장
					$f_unit[$a] = strrchr($f_item[$a],$f_number[$a]);
					$f_unit[$a] = mb_substr($f_unit[$a],1,(strlen(f_unit[$a]-1)),'utf-8');
					
				}
		}
		// 쉼표가 포함되지 않은 경우
		else {
			//더 이상 스트링 내에 숫자가 없을 때까지 반복
			for($k=0;$k<strlen($text);$k++){
				
				//문자열 중 숫자 나오기 전까지 잘라내서 음식명으로 저장
				for($i=0;$i<strlen($text);$i++){
					$char = mb_substr($text,$i,1);
					if(is_numeric($char)){
						array_push($f_name,trim(mb_substr($text,0,($i-1))));
						$text = mb_substr($text,$i,strlen($text));
						break;
					}
				}
				//나머지 문자열 중 첫번째 공백이 나오기 전까지 잘라내기
				for($j=0;$j<strlen($text);$j++){
					$char = mb_substr($text,$j,1);
					if($char == " "){
						$cache = mb_substr($text,0,$j);
						// 숫자만 추출해서 섭취량 배열에 집어넣기
						array_push($f_number,preg_replace("/[^0-9]*/s", "",($cache)));		
						$text = mb_substr($text,$j,strlen($text));
						break;
					}
				}
				
				if(is_numeric(mb_substr($text,0,1))){
					array_push($f_number,mb_substr($text,0,1));
					break;	
				}
			}
		}

   break;


  // 스트링에 숫자가 없는 경우
  case "string"  : 
  				echo "숫자가 없는 케이스!<BR>";

  				// DB에 연결
	    		$hostname = 'localhost';
				$username = 'root';
				$password = 'Dntjd13!';
				$dbname = 'dietbot';

				$db = new mysqli($hostname,$username,$password,$dbname);
				if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
				// 유사한 음식명이 있는지 풀텍스트 검색
				mysqli_query($db,"set names utf8");   
				$query = "SELECT food_Name, MATCH (food_Name)
    						AGAINST ('$text' IN NATURAL LANGUAGE MODE) AS score
    						FROM foodCal WHERE MATCH (food_Name) AGAINST
    						('$text' IN NATURAL LANGUAGE MODE) LIMIT 5";
				$result=mysqli_query($db, $query);
				// 검색 결과가 원문과 동일한 문자열이 있으면 음식명으로 저장
				while($data = mysqli_fetch_array($result)){
 					if(strpos($text,$data['food_Name'])!==false){
 						array_push($f_name,$data['food_Name']);	//음식명 저장
 					}
 					$text = str_replace($data['food_Name'],"",$text);	//음식명 삭제해서 단위랑 숫자만 남기기
				}	
				$mysqli->close($db);  //DB 접속 끊기   

				//남은 텍스트(단위와 숫자)를 json 형태로 변환
				$json_data = json_encode($text);			

				$url = "http://220.230.115.39/units.php";
	  			
				//남은 텍스트 units.php로 보내기
	  			$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/x-www-form-urlencoded', 'Content-Type: application/json; charset=UTF-8'));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response =curl_exec($ch);
				$decode = json_decode($response,1);
				// print_r($decode);
				//echo json_last_error_msg();
				
				$f_number = $decode["f_number"];
				$f_unit = $decode["f_unit"];
				curl_close($ch);	

               break;

  default    : 
  				print "그냥 디폴트<br />\n";
               break;
}

/*
echo '<BR>';
echo '음식명은 ';
print_r($f_name);
echo '<BR>';
echo '섭취량은 ';	
print_r($f_number);
echo '<BR>';	
echo '단위는 ';
print_r($f_unit);
*/

//쿼리에 공백이 들어가지 않게 트림해주기

for($a=0;$a<count($f_name);$a++){
	$f_name[$a] = trim($f_name[$a]);
}

// DB에서 검색하기
$cal = array();
$hostname = 'localhost';
$username = 'root';
$password = 'Dntjd13!';
$dbname = 'dietbot';

$db = new mysqli($hostname,$username,$password,$dbname);
if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
mysqli_query($db,"set names utf8");   

// 음식 별 칼로리 결과값 불러오기
for($i=0;$i<count($f_name);$i++){
  $sql = "SELECT food_Cal FROM foodCal where food_Name like "."'$f_name[$i]'";
  $result=mysqli_query($db, $sql);
  $row = mysqli_fetch_array($result);
  // print_r($row);
  $cal[$i] = $f_number[$i]*$row['food_Cal'];  //1인분 칼로리와 수량 곱하기
}

$cal_total = array_sum($cal); //총 칼로리 계산

// 음식 별 영양소 결과값 불러오기
for($h=0;$h<count($f_name);$h++){
  $sql_h = "SELECT * FROM foodNutrient where food_Name like "."'$f_name[$h]'";
  $result03=mysqli_query($db, $sql_h);
  $row03 = mysqli_fetch_array($result03);
  $carbo[$h] = $f_number[$h]*$row03['Carbohydrate'];  //1인분 탄수화물과 수량 곱하기
  $protein[$h] = $f_number[$h]*$row03['Protein'];  //1인분 단백질과 수량 곱하기
  $fat[$h] = $f_number[$h]*$row03['Fat'];  //1인분 지방과 수량 곱하기
}

// echo $f_name[0];

// $test = "SELECT * FROM foodNutrient where food_Name like "."'$f_name[0]'";
// $rst = mysqli_query($db02,$test);
// // var_dump($rst);
// $row_test = mysqli_fetch_array($rst);
// $carbo[0] = $f_number[0]*$row_test['Carbohydrate'];

// echo $carbo[0];

$carbo_total = array_sum($carbo); //총 탄수화물 계산
$protein_total = array_sum($protein); //총 단백질 계산
$fat_total = array_sum($fat); //총 지방 계산

echo $carbo_total;
echo $protein_total;
echo $fat_total;

//응답 문장 만들기
$response = " ";

for($j=0;$j<count($f_name);$j++){
  $response .= " $f_name[$j] $f_number[$j] $f_unit[$j] $cal[$j]kcal ";  
}
$response = "기록되었습니다! 총 $cal_total 칼로리입니다!".$response ;

$timestamp = date("Y-m-d H:i:s"); //현재 시각 저장하기
//meals DB에 기록하기
for($k=0;$k<count($f_name);$k++){
  $meal_data = "INSERT INTO meals(user_key,food_id,food_name,number,unit,cal,time) VALUES ('$user_key', '$f_id[$k]', '$f_name[$k]', '$f_number[$k]', '$f_unit[$k]','$cal[$k]','$timestamp')";
  $record=mysqli_query($db, $meal_data);
}

//user DB에 기록하기
$add_Cal = "update users set eat_calorie = eat_calorie+$cal_total where user_key = '$user_key'";
mysqli_query($db, $add_Cal);
$add_Carbo = "update users set eat_carbo = eat_carbo+$carbo_total where user_key = '$user_key'";
mysqli_query($db, $add_Carbo);
$add_Protein = "update users set eat_protein = eat_protein+$protein_total where user_key = '$user_key'";
mysqli_query($db, $add_Protein);
$add_Fat = "update users set eat_fat = eat_fat+$fat_total where user_key = '$user_key'";
mysqli_query($db, $add_Fat);

//남은 칼로리 계산하기
$remain_calorie = $row['recommended_calorie']-$row['eat_calorie'];
$remain = "update users set remain_calorie = $remain_calorie where user_key = '$user_key'";
mysqli_query($db, $remain);

$response = $response."오늘 $remain_calorie 칼로리 남으셨네요 (권장 열량 : $recommended_calorie)";

mysqli_close($db);

//응답하기
echo <<< EOD
    {
        "message": {
            "text": "$response"
        }
    }    
EOD;


     
?>


<BR>
	<a href="index.php" target="_parent"> 검색화면으로 </a>
	</body>
</html>
