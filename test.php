<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title> basic parsing </title>
</head>
<body>

<?php


$text = $_POST['text'];

global $f_item, $f_name, $f_number, $f_unit;

$f_item = array();
$f_name = array();
$f_number = array();
$f_unit = array();


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
$response = " ";

for($j=0;$j<count($f_name);$j++){
  $response .= "$f_name[$j] $f_number[$j] $f_unit[$j] $cal[$j]kcal ";  
}

$response = "총 $cal_total 칼로리입니다!".$response ;

//meals DB에 기록하기
for($k=0;$k<count($f_name);$k++){
	$meal_data = "INSERT INTO meals(user_key,food_id,food_name,number,unit,cal) VALUES ('userkey', 53, '$f_name[$k]', '$f_number[$k]', '$f_unit[$k]','$cal[$k]')";
	$record=mysqli_query($db, $meal_data);
}

$user_key = "yes";
$query = "SELECT * from users where user_key like '$user_key'";
$user_result=mysqli_query($db, $query);
echo count($user_result);

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
