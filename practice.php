<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title> result page </title>
</head>
<body>

<?		
$text = $_POST['text'];
$f_item = array();

// 공백과 쉼표를 제외한 나머지 특수 문자 제거하기
$text = str_replace( "<br/>","",$text); //줄바꿈
$text = str_replace( "$#46" ,"",$text); //마침표
$text = str_replace( "$#33" ,"",$text); //느낌표
$text = str_replace( "&#63" ,"",$text); //물음표
$text = str_replace( "$#34" ,"",$text); //큰 따옴표
$text = str_replace( "&#39" ,"",$text); //작은 따옴표
$text = str_replace( "&#43" ,"",$text); //더하기
$text = str_replace( "&#58" ,"",$text); //콜론
$text = str_replace( "&#59" ,"",$text); //세미콜론
$text = str_replace( "&#94" ,"",$text); //웃음표시
$text = str_replace( "&#126" ,"",$text); //물결표시

// &인 경우 쉼표로 바꿔주기
$text = str_replace( "&#38" ,",",$text);

// 쉼표가 포함되어 있는 경우
if (strlen(stristr($text,","))>0){
	// 쉽표 기준 잘라서 배열로 저장하기
		$f_item = explode(",",$text);
		
		for($a=0;$a<(count($f_item));$a++){		
			// 아이템 앞 뒤 공백 제거하기
			$f_item[$a] = trim($f_item[$a]);
			// 문자열 중 숫자만 추출하여 섭취량으로 저장
			$f_number[$a] = return_number($f_item[$a]);
			// 문자열 중 숫자 나오기 전까지 잘라내서 음식명으로 저장
			$f_name[$a] = substr($f_item[$a],0,strrpos($f_item[$a],$f_number[$a]));
			// 문자열 중 숫자 다음부터 잘라내서 단위로 저장
			$f_unit[$a] = strrchr($f_item[$a],$f_number[$a]);
			$f_unit[$a] = mb_substr($f_unit[$a],1,(strlen(f_unit[$a]-1)),'utf-8');
			
		}
}
// 쉼표가 포함되지 않은 경우
else {
	$f_item = array();
	$f_name = array();
	$f_number = array();
	$f_unit = array();

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
				array_push($f_number,return_number($cache));		
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


function return_number($string){

     for($i=0;$i<strlen($string);$i++ ){
       if(eregi("[0-9]",$string[$i])){
           $string_result = $string_result.$string[$i]; 
       }
     } 
     return $string_result; 
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
print_r($f_item);
echo '<BR>';
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
