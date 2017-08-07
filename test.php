<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title> result page </title>
</head>
<body>

<?php
$text = $_POST['text'];

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
		$case = "have number";
		break;
	}
	$case = "don't have number";
}


switch ($case) {
// 스트링에 숫자가 있는 경우
  case "have number"  : 
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
  case "don't have number"  : 
  				// DB에 연결
	    		$hostname = 'localhost';
				$username = 'root';
				$password = 'Dntjd13!';
				$dbname = 'dietbot';

				$db = new mysqli($hostname,$username,$password,$dbname);
				if ( $db->connect_error ) exit('접속 실패 : '.$db->connect_error);
				// 풀텍스트 검색
				mysqli_query($db,"set names utf8");   
				$query = "SELECT food_Name, MATCH (food_Name)
    						AGAINST ('$text' IN NATURAL LANGUAGE MODE) AS score
    						FROM foodCal WHERE MATCH (food_Name) AGAINST
    						('$text' IN NATURAL LANGUAGE MODE) LIMIT 5";
				$result=mysqli_query($db, $query);
				// 결과값 배열로 저장하며 검색 결과가 원문과 동일한 문자열일 경우 음식명으로 저장한 뒤 삭제하여 단위와 숫자만 남기기
				while($data = mysqli_fetch_array($result)){
 					//echo $data['food_Name']." : ".$data['score'];
 					//echo "<BR>";
 					if(strpos($text,$data['food_Name'])!==false){
 						array_push($f_name,$data['food_Name']);
 					}
 					$text = str_replace($data['food_Name'],"",$text);
				}	

				//남은 텍스트를 json 형태로 변환
				$rest = array();
				$rest['string'] = $text;
				//print_r($rest);
				$json_data = json_encode($rest,JSON_UNESCAPED_UNICODE);
				print_r($json_data);
				 
				//curl 활용 남은 텍스트 units.php로 보내기
				$url = "http://220.230.115.39/units.php";
	  			$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/x-www-form-urlencoded', 'Content-Type: application/json; charset=UTF-8'));
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response =curl_exec($ch);
				
				$decode = json_decode($response,true);
				print_r($decode);

				curl_close($ch);

				// 단위 테이블에 있는 문자열이 있으면 단위로 저장하고 해당 문자 제거
				//if(strpos($text,"개")!==false){
				//	$text = str_replace("개","",$text);
				//}
				//echo($text);
				//echo "<BR>";

				//for ($e=0;$e<count($data);$e++){
				//	$data[$e] = 
				//}
               break;
  default    : print "그냥 디폴트<br />\n";
               break;
}

/*
// DB에서 검색하기
		$db = mysql_connect("localhost", "kyhwin2000", "woosung13") or die (mysql_error()); 
		mysql_select_db("kyhwin2000");
		mysql_query("set names utf8");   
		$j = 0;
			$sql = "SELECT * FROM foodCal where food_Name like '.$f_name[$j].'";
			$result=mysql_query($sql, $db);
			$result_cal = mysql_result($result,0,'food_Cal');	
			// 결과값 출력하기
			echo($f_name[$j]).' '.($f_number[$j]).($f_unit[$j]);
			echo(" is ");
			echo($f_number[$j]*$result_cal);
			echo(" Kcal");		
*/

// 확인해보기
//print_r($f_item);
//echo '<BR>';
echo '음식명은 ';
print_r($f_name);
echo '<BR>';
echo '섭취량은 ';	
print_r($f_number);
echo '<BR>';	
echo '단위는 ';
print_r($f_unit);

?>


<BR>
	<a href="index.php" target="_parent"> 검색화면으로 </a>
	</body>
</html>
